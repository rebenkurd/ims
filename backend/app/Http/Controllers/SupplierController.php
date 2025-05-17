<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Http\Requests\SupplierRequest;
use App\Http\Resources\SupplierListResource;
use App\Http\Resources\SupplierResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    public function index()
    {
        $search = request('search', '');
        $per_page = request('per_page', 10);
        $sort_field = request('sort_field', 'updated_at');
        $sort_direction = request('sort_direction', 'desc');

        $query = Supplier::query()
            ->orderBy($sort_field, $sort_direction);

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('mobile', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%');
        }

        return SupplierListResource::collection($query->paginate($per_page));
    }

    public function store(SupplierRequest $request)
    {

        // DB::beginTransaction();
        try {
            $data = $request->all();
            $data['created_by'] = $request->user()->id;

            $supplier = Supplier::create($data);

            // DB::commit();
            return response([
                "message" => "Supplier created successfully",
                "data" => new SupplierResource($supplier)
            ]);

        } catch (\Exception $e) {
            // DB::rollBack();
            return response([
                "message" => "Error creating supplier: " . $e->getMessage()
            ], 500);
        }
    }

    public function show(Supplier $supplier)
    {

        return new SupplierResource($supplier);
    }

    public function update(SupplierRequest $request, Supplier $supplier)
    {
        // DB::beginTransaction();
        try {
            $data = $request->all();
            // $data['updated_by'] = $request->user()->id;

            $supplier->update($data);

            // DB::commit();
            return response([
                "message" => "Supplier updated successfully",
                "data" => new SupplierResource($supplier)
            ]);

        } catch (\Exception $e) {
            // DB::rollBack();
            return response([
                "message" => "Error updating supplier: " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        DB::beginTransaction();
        try {
            $supplier->delete();
            DB::commit();
            return response()->noContent();

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error deleting supplier: " . $e->getMessage()
            ], 500);
        }
    }

    public function getSuppliersForSelect()
    {
        $suppliers = Supplier::query()
            ->select('id', 'supplier_name as name')
            ->orderBy('supplier_name')
            ->get();

        return response()->json($suppliers);
    }
}
