<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserListResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Trait\SaveImageTrait;
class UserController extends Controller
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

        $query = User::query();
        $query->orderBy($sort_field,$sort_direction);
        if ($search) {
            $query->where('name', 'like', '%'.$search.'%');
        }

        return UserListResource::collection($query->paginate($per_page));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try{

            $data = $request->all();
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;
            $data['status'] = 1;
            $data['role_id'] = 1;

            /** @var \Illuminate\Http\UploadedFile $images */
            $image = $data['image'] ?? null;

            if($image){
                $relativePath =$this -> saveImage($image,'users');
                $data['image'] = URL::to('/storage/'.$relativePath);
            }

            $user = User::create($data);
            return response([
                "message" => "User Created successfully",
                "data" => new UserResource($user)
            ]);

        } catch (\Exception $e) {
                return response([
                    "message" => "Error creating user: ". $e->getMessage()
                ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserListResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {

        try{
            $data = $request->all();
            $data['updated_by'] = $request->user()->id;

            /** @var \Illuminate\Http\UploadedFile $image */
            $image = $data['image'] ?? null;

            if($image){
                if($image !== $user->image){
                    $relative_path =$this -> saveImage($image,'users');
                    $data['image'] = URL::to('/storage/'.$relative_path);
                }

                if($image && $image == $user->image){
                $relative_path =$this -> saveImage($image,'users');
                $data['image'] = URL::to(Storage::url($relative_path));


                    if($user->image){
                        $old_image_path = str_replace(URL::to('/storage/'),'',$user->image);

                        Storage::disk('public')->delete($old_image_path);
                        Storage::disk('public')->deleteDirectory(dirname($old_image_path));
                    }
                }
            }else{
                $data['image'] = $user->image;
            }

                $user -> update($data);
                return response([
                    "message" => "User updated successfully",
                    "data" => new UserResource($user)
                ]);
            } catch (\Exception $e) {
                return response([
                    "message" => "Error updating User: ". $e->getMessage()
                ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->noContent();
    }


}
