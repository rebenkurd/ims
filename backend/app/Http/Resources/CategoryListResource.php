<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryListResource extends JsonResource
{
    public static $wrap =false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status == 1 ? 'Active':'Inactive',
            'created_by' => $this->created_by,
            'created_at' => $this->created_at -> format('Y-m-d H:i:s'),
        ];
    }
}
