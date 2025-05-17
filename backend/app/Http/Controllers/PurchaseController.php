<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseInvoice;
use App\Http\Requests\PurchaseRequest;
use App\Http\Resources\PurchaseListResource;
use App\Http\Resources\PurchaseResource;
use App\Models\Product;
use App\Models\Payment;
use App\Models\Supplier;
use App\Services\InvoicePdfService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index()
    {
        $search = request('search', false);
        $per_page = request('per_page', 10);
        $sort_field = request('sort_field', 'updated_at');
        $sort_direction = request('sort_direction', 'desc');

        $query = Purchase::query()->with(['invoices', 'supplier', 'createdBy']);
        $query->orderBy($sort_field, $sort_direction);

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return PurchaseListResource::collection($query->paginate($per_page));
    }

    public function store(PurchaseRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;
            $data['purchase_code'] = $this->generatePurchaseCode();
            $data['discount'] = $request->discount_all;

            $purchase = Purchase::create($data);

            // Process purchase items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $purchase->purchaseItems()->create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'discount' => $item['discount'],
                        'unit_price' => $item['unit_cost'],
                        'total_price' => $item['total_amount']
                    ]);

                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->minimum_qty = $product->minimum_qty + $item['quantity'];
                        $product->save();
                    }
                }
            }

            // Process payments
            if ($request->has('payments') && is_array($request->payments)) {
                foreach ($request->payments as $payment) {
                    $purchase->payments()->create([
                        'amount' => $payment['amount'],
                        'payment_method' => $payment['payment_type'],
                        'payment_date' => $payment['date'],
                        'note' => $payment['payment_note'],
                        'created_by' => $request->user()->id,
                        'status' => 1
                    ]);
                }
            }

            // Create purchase invoice
            $invoice = $this->createPurchaseInvoice($purchase);
            DB::commit();

            return response([
                "message" => "Purchase and invoice created successfully",
                "data" => new PurchaseResource($purchase),
                "invoice" => $invoice
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error creating purchase: " . $e->getMessage()
            ], 500);
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['invoices', 'payments', 'purchaseItems.product']);
        return new PurchaseResource($purchase);
    }

    public function update(PurchaseRequest $request, Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['updated_by'] = $request->user()->id;
            $data['discount'] = $request->discount_all;

            $purchase->update($data);

            // Handle items: First, remove existing items
            $purchase->purchaseItems()->delete();

            // Then add new items from the request
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $purchase->purchaseItems()->create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'discount' => $item['discount'],
                        'total_price' => $item['total_amount'],
                        'unit_price' => $item['unit_cost'],
                    ]);

                    // Update product quantity if changed
                    $product = Product::find($item['product_id']);
                    if ($product) {
                        $product->minimum_qty = $product->minimum_qty + $item['quantity'];
                        $product->save();
                    }
                }
            }

            // Handle payments for existing purchases
            if ($request->has('payments') && is_array($request->payments)) {
                // First remove existing payments
                $purchase->payments()->delete();

                // Then add new payments
                foreach ($request->payments as $payment) {
                    $purchase->payments()->create([
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
            $this->updatePurchaseInvoice($purchase);

            DB::commit();

            return response([
                "message" => "Purchase updated successfully",
                "data" => new PurchaseResource($purchase)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error updating Purchase: ". $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Purchase $purchase)
    {
        try {
            DB::beginTransaction();

            // Delete related records
            $purchase->purchaseItems()->delete();
            $purchase->payments()->delete();
            $purchase->invoices()->delete();

            // Delete the purchase
            $purchase->delete();

            DB::commit();

            return response()->noContent();
        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error deleting purchase: ". $e->getMessage()
            ], 500);
        }
    }

    private function generatePurchaseCode()
    {
        $latest = Purchase::latest()->first();
        $number = $latest ? (int) substr($latest->purchase_code, 3) + 1 : 1;
        return 'PUR' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    private function createPurchaseInvoice(Purchase $purchase)
    {
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($purchase->id, 6, '0', STR_PAD_LEFT);

        $invoiceData = [
            'invoice_number' => $invoiceNumber,
            'purchase_id' => $purchase->id,
            'supplier_id' => $purchase->supplier_id,
            'total_amount' => $purchase->subtotal,
            'discount' => $purchase->discount,
            'final_amount' => $this->calculateFinalAmount($purchase),
            'payment_status' => $this->determinePaymentStatus($purchase),
            'created_by' => $purchase->created_by,
            'status' => 1,
            'due_date' => now()->addDays(30)->toDateString(),
            'invoice_date' => now()->toDateString()
        ];

        return PurchaseInvoice::create($invoiceData);
    }

    private function updatePurchaseInvoice(Purchase $purchase)
    {
        $invoice = $purchase->invoices()->first();

        if (!$invoice) {
            return $this->createPurchaseInvoice($purchase);
        }

        $invoice->update([
            'total_amount' => $purchase->total,
            'discount' => $purchase->discount,
            'final_amount' => $this->calculateFinalAmount($purchase),
            'payment_status' => $this->determinePaymentStatus($purchase),
            'updated_by' => Auth::id()
        ]);

        return $invoice;
    }

    private function calculateTax(Purchase $purchase)
    {
        // Implement your tax calculation logic here
        // For example, if you have a fixed tax rate:
        $taxRate = 0.1; // 10%
        return $purchase->total * $taxRate;
    }

    private function calculateFinalAmount(Purchase $purchase)
    {
        $tax = $this->calculateTax($purchase);
        // return $purchase->total + $tax - $purchase->discount;
        return $purchase->total;
    }

    private function determinePaymentStatus(Purchase $purchase)
    {
        $totalPaid = $purchase->payments->sum('amount');
        $grandTotal = $this->calculateFinalAmount($purchase);

        if ($totalPaid >= $grandTotal) {
            return 'paid';
        } elseif ($totalPaid > 0) {
            return 'partial';
        } else {
            return 'unpaid';
        }
    }
    public function getInvoice(Purchase $purchase, $invoiceId)
    {
        $invoice = PurchaseInvoice::where('id', $invoiceId)
                    ->where('purchase_id', $purchase->id)
                    ->first();

        if (!$invoice) {
            return response()->json([
                'message' => 'Invoice not found for this purchase'
            ], 404);
        }

        // Load relationships
        $purchase->load([
            'supplier',
            'purchaseItems.product',
            'payments',
            'createdBy'
        ]);

        $invoice->load(['supplier', 'createdBy']);

        // Return directly without using PurchaseResource
        return response([
            "invoice" => $invoice,
            "purchase" => $purchase
        ]);
    }

    public function showInvoice($id)
{
    $purchase = Purchase::with([
        'supplier',
        'purchaseItems.product',
        'payments',
        'invoices'
    ])->findOrFail($id);

    $invoice = $purchase->invoices->first();

    if (!$invoice) {
        return redirect()->back()->with('error', 'No invoice found for this purchase');
    }

    return view('purchases.invoice', [
        'purchase' => $purchase,
        'invoice' => $invoice
    ]);
}
}
