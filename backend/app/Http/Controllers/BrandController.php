<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandListResource;
use App\Http\Resources\BrandResource;
use App\Models\Brand;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search',false);
        $per_page = request('per_page',10);
        $sort_field = request('sort_field','updated_at');
        $sort_direction = request('sort_direction','desc');

        $query = Brand::query();
        $query->orderBy($sort_field,$sort_direction);
        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return BrandListResource::collection($query->paginate($per_page));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        try{
            $data = $request->all();
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;

            $brand = Brand::create($data);
            return response([
                "message" => "brand Created successfully",
                "data" => new BrandResource($brand)
            ]);

        } catch (\Exception $e) {
                return response([
                    "message" => "Error Creating brand: ". $e->getMessage()
                ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        try {
                $data = $request->all();
                $data['updated_by'] = $request->user()->id;
                $brand -> update($data);
                return response([
                    "message" => "brand updated successfully",
                    "data" => new BrandResource($brand)
                ]);
            } catch (\Exception $e) {
                return response([
                    "message" => "Error updating brand: ". $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->noContent();
    }

        /**
     *Get Brands resource
    **/
    public function getBrands()
    {
        $query = Brand::query()->latest()->get();
        return BrandListResource::collection($query);
    }
}
