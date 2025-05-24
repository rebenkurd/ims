<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Original summary data
        $summary = [
            'total_sales' => Sale::sum('total'),
            'total_purchases' => Purchase::sum('total'),
            'total_customers' => Customer::count(),
            'total_suppliers' => Supplier::count()
        ];

        // New financial summary data based on due_balance
        $financialSummary = [
            'total_purchase_due' => Purchase::sum('due_balance'),
            'todays_purchase' => Purchase::whereDate('created_at', today())->sum('total'),
            'total_sales_due' => Sale::sum('due_balance'),
            'today_payment_received' => $this->getTodayPaymentsReceived(),
            'total_sales_amount' => Sale::sum('total'),
            'todays_sales' => Sale::whereDate('created_at', today())->sum('total'),
            'purchase_due_trend' => $this->getPurchaseDueTrend(),
            'todays_purchase_trend' => $this->getTodaysPurchaseTrend(),
            'sales_due_trend' => $this->getSalesDueTrend(),
            'payment_received_trend' => $this->getPaymentReceivedTrend(),
            'sales_amount_trend' => $this->getSalesAmountTrend(),
            'todays_sales_trend' => $this->getTodaysSalesTrend()
        ];

        // Sales data for chart (last 12 months)
        $salesData = DB::table('sales')
            ->select(
                DB::raw('SUM(total) as amount'),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"),
                DB::raw("DATE_FORMAT(created_at, '%M %Y') as month")
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->whereNull('deleted_at')
            ->groupBy('month_year', 'month')
            ->orderBy('month_year')
            ->get();

        $months = [];
        $amounts = [];
        foreach ($salesData as $sale) {
            $months[] = $sale->month;
            $amounts[] = $sale->amount;
        }

        // Recently added products (last 5)
        $recentProducts = Product::with('category')
            ->latest()
            ->take(5)
            ->get();

        // Expired products
        $expiredProducts = Product::where('expire_date', '<=', now())
            ->with('category')
            ->take(5)
            ->get();

        // Low stock products
        $lowStockProducts = Product::whereColumn('current_opening_stock', '<=', 'minimum_qty')
            ->with('category')
            ->take(5)
            ->get();

        return response()->json([
            'summary' => $summary,
            'financialSummary' => $financialSummary,
            'sales' => [
                'months' => $months,
                'amounts' => $amounts
            ],
            'recentProducts' => $recentProducts,
            'expiredProducts' => $expiredProducts,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }

private function getTodayPaymentsReceived()
{
    // Calculate payments received today from sales
    $salesPayments = Payment::whereNotNull('sale_id')
        ->whereDate('payment_date', today())
        ->sum('amount');

    // Calculate payments received today from purchases (if needed)
    $purchasePayments = Payment::whereNotNull('purchase_id')
        ->whereDate('payment_date', today())
        ->sum('amount');

    return $salesPayments + $purchasePayments;
}
    // Helper methods to determine trends based on due_balance
    private function getPurchaseDueTrend()
    {
        $current = Purchase::sum('due_balance');
        $previous = Purchase::whereDate('created_at', '<', today())
            ->sum('due_balance');
        return $this->calculateTrend($current, $previous);
    }

    private function getTodaysPurchaseTrend()
    {
        $today = Purchase::whereDate('created_at', today())->sum('total');
        $yesterday = Purchase::whereDate('created_at', today()->subDay())->sum('total');
        return $this->calculateTrend($today, $yesterday);
    }

    private function getSalesDueTrend()
    {
        $current = Sale::sum('due_balance');
        $previous = Sale::whereDate('created_at', '<', today())
            ->sum('due_balance');
        return $this->calculateTrend($current, $previous);
    }

private function getPaymentReceivedTrend()
{
    $today = $this->getTodayPaymentsReceived();

    $yesterday = Payment::whereDate('payment_date', today()->subDay())
        ->sum('amount');

    return $this->calculateTrend($today, $yesterday);
}
    private function getSalesAmountTrend()
    {
        $current = Sale::sum('total');
        $previous = Sale::whereDate('created_at', '<', today())->sum('total');
        return $this->calculateTrend($current, $previous);
    }

    private function getTodaysSalesTrend()
    {
        $today = Sale::whereDate('created_at', today())->sum('total');
        $yesterday = Sale::whereDate('created_at', today()->subDay())->sum('total');
        return $this->calculateTrend($today, $yesterday);
    }

    private function calculateTrend($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 'up' : 'neutral';
        }
        return $current > $previous ? 'up' : ($current < $previous ? 'down' : 'neutral');
    }
}
