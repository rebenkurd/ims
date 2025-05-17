<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductListResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Trait\SaveImageTrait;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;


class ProductController extends Controller
{
    use SaveImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request('search',false);
        $per_page = request('per_page',10);
        $sort_field = request('sort_field','updated_at');
        $sort_direction = request('sort_direction','desc');

        $query = Product::query();
        $query->orderBy($sort_field,$sort_direction);
        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return ProductListResource::collection($query->paginate($per_page));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try{

        $data = $request->all();
        $data['created_by'] = $request->user()->id;
        $data['updated_by'] = $request->user()->id;
        $data['status'] = 1;

        /** @var \Illuminate\Http\UploadedFile $images */
        $image = $data['image'] ?? null;

        if($image){
            $relativePath =$this -> saveImage($image,'products');
            $data['image'] = URL::to('/storage/'.$relativePath);
        }

        $product = Product::create($data);
        return response([
            "message" => "Product Created successfully",
            "data" => new ProductResource($product)
        ]);

    } catch (\Exception $e) {
            return response([
                "message" => "Error creating product: ". $e->getMessage()
            ], 500);
    }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {

        try{
        $data = $request->all();
        $data['updated_by'] = $request->user()->id;

        /** @var \Illuminate\Http\UploadedFile $image */
        $image = $data['image'] ?? null;

        if($image){
            if($image !== $product->image){
                $relative_path =$this -> saveImage($image,'products');
                $data['image'] = URL::to('/storage/'.$relative_path);
            }

            if($image && $image == $product->image){
            $relative_path =$this -> saveImage($image,'products');
            $data['image'] = URL::to(Storage::url($relative_path));


                if($product->image){
                    $old_image_path = str_replace(URL::to('/storage/'),'',$product->image);

                    Storage::disk('public')->delete($old_image_path);
                    Storage::disk('public')->deleteDirectory(dirname($old_image_path));
                }
            }
        }else{
            $data['image'] = $product->image;
        }

        $product -> update($data);
        return response([
            "message" => "Product updated successfully",
            "data" => new ProductResource($product)
        ]);
    } catch (\Exception $e) {
        return response([
            "message" => "Error updating product: ". $e->getMessage()
        ], 500);
 }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }



    // Status Update Method
    public function updateStatus(Product $product){
        try{
            $status = !$product->status;
            $product->update(['status' => $status]);
            if ($product->status){
                $message = "Product Activated successfully";
            }else{
                $message = "Product Inactivated successfully";
            }
            return response([
                "message" => $message,
                "data" => new ProductResource($product)
            ]);
        } catch (\Exception $e) {
            return response([
                "message" => "Error updating product status: ". $e->getMessage()
            ], 500);
        }
    }
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            // Add logging for debugging
            Log::info('Search query: ' . $query);

            if (empty($query)) {
                return response()->json([], 200);
            }

            $products = Product::where('name', 'like', "%{$query}%")
                ->orWhere('code', 'like', "%{$query}%")
                ->get();

            // Log the results
            Log::info('Search results count: ' . $products->count());

            return response()->json($products, 200);
        } catch (\Exception $e) {
            // Log any errors
            Log::error('Product search error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


}
