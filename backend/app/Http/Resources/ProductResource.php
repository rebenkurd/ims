<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap=false;
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
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'unit' => $this->unit,
            'per_carton' => $this->per_carton,
            'minimum_qty' => $this->minimum_qty,
            'expire_date' => $this->expire_date,
            'barcode' => $this->barcode,
            'description' => $this->description,
            'image' => $this->image,
            'price' => $this->price,
            'purchase_price' => $this->purchase_price,
            'profit_margin' => $this->profit_margin,
            'sales_price' => $this->sales_price,
            'final_price' => $this->final_price,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'current_opening_stock' => $this->current_opening_stock,
            'adjust_stock' => $this->adjust_stock,
            'adjustment_note' => $this->adjustment_note,
            'created_by' => $this->created_by,
            'deleted_by' => $this->deleted_by,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
