<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with([
                'customer',
                'payments',
                'saleItems.product',
                'invoices'
            ])
            ->select(
                'sales.*',
                DB::raw('total as total_amount'),
                DB::raw('due_balance as due_amount'),
                DB::raw('(total - due_balance) as paid_amount')
            );

        // Date Range Filter
        if ($request->from_date && $request->to_date) {
            $query->whereBetween('sales.created_at', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // Customer Filter (using ID from select)
        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        // Payment Status Filter - using due_balance
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
        $result = $query->orderBy('sales.created_at', 'desc')
                       ->paginate($request->per_page ?? 10);

        return $result;
    }
}
