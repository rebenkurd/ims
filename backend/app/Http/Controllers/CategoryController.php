<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\CategoryListResource;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\CategoryRequest;

class CategoryController extends Controller
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

        $query = Category::query();
        $query->orderBy($sort_field,$sort_direction);
        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return CategoryListResource::collection($query->paginate($per_page));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
    try {
        $data = $request->all();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;

        $category = Category::create($data);
        return response([
            "message" => "Category Created successfully",
            "data" => new CategoryResource($category)
        ]);

    } catch (\Exception $e) {
            return response([
                "message" => "Error Creating Category: ". $e->getMessage()
            ], 500);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
    try{
        $data = $request->all();
        $data['updated_by'] = $request->user()->id;
        $category -> update($data);
        return response([
            "message" => "category updated successfully",
            "data" => new CategoryResource($category)
        ]);
    } catch (\Exception $e) {
        return response([
            "message" => "Error updating category: ". $e->getMessage()
        ], 500);
 }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent();
    }


    /**
     *Get Category resource
    **/
    public function getCategory()
    {
        $query = Category::query()->latest()->get();
        return CategoryListResource::collection($query);
    }
}
