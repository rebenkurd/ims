<?php

namespace App\Http\Resources;

use App\Models\Category;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{

    // public static $wrap ='products';
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'brand' => $this->brand?->name,
            'category' => $this->category?->name,
            'per_carton' => $this->per_carton,
            'minimum_qty' => $this->minimum_qty,
            'expire_date' => $this->expire_date,
            'image_url' => $this->image,
            'price' => $this->price,
            'tax' => $this->tax,
            'purchase_price' => $this->purchase_price,
            'tax_type' => $this->tax_type,
            'profit_margin' => $this->profit_margin,
            'sales_price' => $this->sales_price,
            'final_price' => $this->final_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'status' => $this->status == 1 ? 'Active':'Inactive',
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
