<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleInvoice;
use App\Http\Requests\SaleRequest;
use App\Http\Resources\SaleListResource;
use App\Http\Resources\SaleResource;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Customer;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function index()
    {

        $search = request('search', false);
        $per_page = request('per_page', 10);
        $sort_field = request('sort_field', 'updated_at');
        $sort_direction = request('sort_direction', 'desc');

        $query = Sale::query()->with(['invoices', 'customer', 'createdBy']);
        $query->orderBy($sort_field, $sort_direction);

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return SaleListResource::collection($query->paginate($per_page));
    }

    public function store(SaleRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;
            $data['sale_code'] = $this->generateSaleCode();
            $data['discount'] = $request->discount_all;

            $sale = Sale::create($data);

            // Process sale items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $sale->saleItems()->create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'discount' => $item['discount'],
                        'sales_price' => $item['sales_price'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['total_amount']
                    ]);

                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->minimum_qty = $product->minimum_qty - $item['quantity'];
                        $product->save();
                    }
                }
            }

            // Process payments
            if ($request->has('payments') && is_array($request->payments)) {
                foreach ($request->payments as $payment) {
                    $sale->payments()->create([
                        'amount' => $payment['amount'],
                        'payment_method' => $payment['payment_type'],
                        'payment_date' => $payment['date'],
                        'note' => $payment['payment_note'],
                        'created_by' => $request->user()->id,
                        'status' => 1
                    ]);
                }
            }

            // Create sale invoice
            $invoice = $this->createSaleInvoice($sale);
            DB::commit();

            return response([
                "message" => "Sale and invoice created successfully",
                "data" => new SaleResource($sale),
                "invoice" => $invoice
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error creating sale: " . $e->getMessage()
            ], 500);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['invoices', 'payments', 'saleItems.product']);
        return new SaleResource($sale);
    }

    public function update(SaleRequest $request, Sale $sale)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['updated_by'] = $request->user()->id;
            $data['discount'] = $request->discount_all;

            $sale->update($data);

            // Handle items: First, remove existing items
            $sale->saleItems()->delete();

            // Then add new items from the request
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $sale->saleItems()->create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'discount' => $item['discount'],
                        'sales_price' => $item['sales_price'],
                        'total_price' => $item['total_amount'],
                        'unit_price' => $item['unit_price'],
                    ]);

                    // Update product quantity if changed
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->quantity = $product->quantity - $item['quantity'];
                        $product->save();
                    }
                }
            }

            // Handle payments for existing sales
            if ($request->has('payments') && is_array($request->payments)) {
                // First remove existing payments
                $sale->payments()->delete();

                // Then add new payments
                foreach ($request->payments as $payment) {
                    $sale->payments()->create([
                        'amount' => $payment['amount'],
                        'payment_method' => $payment['payment_type'],
                        'payment_date' => $payment['date'],
                        'note' => $payment['payment_note'],
                        'created_by' => $request->user()->id,
                        'status' => 1
                    ]);
                }
            }

            // Update existing invoice or create new one if needed
            $this->updateSaleInvoice($sale);

            DB::commit();

            return response([
                "message" => "Sale updated successfully",
                "data" => new SaleResource($sale)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error updating Sale: ". $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Sale $sale)
    {
        try {
            DB::beginTransaction();

            // Delete related records
            $sale->saleItems()->delete();
            $sale->payments()->delete();
            $sale->invoices()->delete();

            // Delete the sale
            $sale->delete();

            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error deleting sale: ". $e->getMessage()
            ], 500);
        }
    }

    private function generateSaleCode()
    {
        $latest = Sale::latest()->first();
        $number = $latest ? (int) substr($latest->sale_code, 3) + 1 : 1;
        return 'SAL' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    private function createSaleInvoice(Sale $sale)
    {
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);

        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'sale_id' => $sale->id,
            'customer_id' => $sale->customer_id,
            'total_amount' => $sale->subtotal,
            'discount' => $sale->discount,
            'final_amount' => $this->calculateFinalAmount($sale),
            'payment_status' => $this->determinePaymentStatus($sale),
            'created_by' => $sale->created_by,
            'status' => 1,
            'due_date' => now()->addDays(30)->toDateString(),
            'invoice_date' => now()->toDateString()
        ];

        return SaleInvoice::create($invoiceData);
    }

    private function updateSaleInvoice(Sale $sale)
    {
        $invoice = $sale->invoices()->first();

        if (!$invoice) {
            return $this->createSaleInvoice($sale);
        }

        $invoice->update([
            'total_amount' => $sale->total,
            'discount' => $sale->discount,
            'final_amount' => $this->calculateFinalAmount($sale),
            'payment_status' => $this->determinePaymentStatus($sale),
            'updated_by' => Auth::id()
        ]);

        return $invoice;
    }

    private function calculateTax(Sale $sale)
    {
        // Implement your tax calculation logic here
        // For example, if you have a fixed tax rate:
        $taxRate = 0.1; // 10%
        return $sale->total * $taxRate;
    }

    private function calculateFinalAmount(Sale $sale)
    {
        $tax = $this->calculateTax($sale);
        return $sale->total;
    }

    private function determinePaymentStatus(Sale $sale)
    {
        $totalPaid = $sale->payments->sum('amount');
        $grandTotal = $this->calculateFinalAmount($sale);

        if ($totalPaid >= $grandTotal) {
            return 'paid';
        } elseif ($totalPaid > 0) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }

    public function getInvoice(Sale $sale, $invoiceId)
    {
        $invoice = SaleInvoice::where('id', $invoiceId)
                    ->where('sale_id', $sale->id)
                    ->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found for this sale'
            ], 404);
        }

        // Load relationships
        $sale->load([
            'customer',
            'saleItems.product',
            'payments',
            'createdBy'
        ]);

        $invoice->load(['customer', 'createdBy']);

        // Return directly without using SaleResource
        return response([
            "invoice" => $invoice,
            "sale" => $sale
        ]);
    }

    public function showInvoice($id)
    {
        $sale = Sale::with([
            'customer',
            'saleItems.product',
            'payments',
            'invoices'
        ])->findOrFail($id);

        $invoice = $sale->invoices->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'No invoice found for this sale');
        }

        return view('sales.invoice', [
            'sale' => $sale,
            'invoice' => $invoice
        ]);
    }
}
