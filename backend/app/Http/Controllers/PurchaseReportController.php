<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with([
                'supplier',
                'payments',
                'purchaseItems.product',
                'invoices'
            ])
            ->select(
                'purchases.*',
                DB::raw('total as total_amount'),
                DB::raw('due_balance as due_amount'),
                DB::raw('(total - due_balance) as paid_amount')
            );

        // Date Range Filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('purchases.created_at', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // Supplier Filter (using ID from select)
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Payment Status Filter - updated to use due_balance
        if ($request->payment_status && $request->payment_status !== 'all') {
            switch ($request->payment_status) {
                case 'paid':
                    $query->where('due_balance', '<=', 0);
                    break;
                case 'partial':
                    $query->where('due_balance', '>', 0)
                          ->where('due_balance', '<', DB::raw('total'));
                    break;
                case 'due':
                    $query->where('due_balance', DB::raw('total'));
                    break;
            }
        }

        // Order and Paginate
        $result = $query->orderBy('purchases.created_at', 'desc')
                       ->paginate($request->per_page ?? 10);

        return $result;
    }
}
