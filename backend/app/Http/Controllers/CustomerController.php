<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerListResource;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        $search = request('search', '');
        $per_page = request('per_page', 10);
        $sort_field = request('sort_field', 'updated_at');
        $sort_direction = request('sort_direction', 'desc');

        $query = Customer::query()
            ->orderBy($sort_field, $sort_direction);

        if ($search) {
            $query->where('name', 'like', '%'.$search.'%')
                ->orWhere('mobile', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%');
        }

        return CustomerListResource::collection($query->paginate($per_page));
    }

    public function store(CustomerRequest $request)
    {
        try {
            $data = $request->all();
            $data['created_by'] = $request->user()->id;

            $customer = Customer::create($data);

            return response([
                "message" => "Customer created successfully",
                "data" => new CustomerResource($customer)
            ]);

        } catch (\Exception $e) {
            return response([
                "message" => "Error creating customer: " . $e->getMessage()
            ], 500);
        }
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(CustomerRequest $request, Customer $customer)
    {
        try {
            $data = $request->all();
            $customer->update($data);

            return response([
                "message" => "Customer updated successfully",
                "data" => new CustomerResource($customer)
            ]);

        } catch (\Exception $e) {
            return response([
                "message" => "Error updating customer: " . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        DB::beginTransaction();
        try {
            $customer->delete();
            DB::commit();
            return response()->noContent();

        } catch (\Exception $e) {
            DB::rollBack();
            return response([
                "message" => "Error deleting customer: " . $e->getMessage()
            ], 500);
        }
    }

    public function getCustomersForSelect()
    {
        $customers = Customer::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($customers);
    }
}
