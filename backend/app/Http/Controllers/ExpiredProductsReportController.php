<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpiredProductsReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->select(
                'id',
                'code',
                'name',
                'expire_date',
                'minimum_qty',
            )
            ->where('expire_date', '<=', $request->to_date ?: Carbon::today())
            ->orderBy('expire_date', 'asc');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                  ->orWhere('code', 'like', '%'.$request->search.'%');
            });
        }

        return $query->get();
    }
}
