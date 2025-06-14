<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'sale_code' => $this->sale_code,
            'total_quantities' => $this->total_quantities,
            'sale_status' => $this->sale_status,
            'reference_no' => $this->reference_no,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'total' => $this->total,
            'subtotal' => $this->subtotal,
            'due_balance' => $this->due_balance,
            'note' => $this->note,
            'status' => $this->status,
            'customer_id' => $this->customer_id,

            // Include relationships
            'customer' => $this->whenLoaded('customer'),
            'sale_items' => $this->whenLoaded('saleItems', function () {
                return $this->saleItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? null,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total_amount' => $item->total_price,
                        'discount' => $item->discount,
                        'sale_price' => $item->product->sales_price,
                        'product' => $item->product ? [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'barcode' => $item->product->barcode
                        ] : null
                    ];
                });
            }),
            'payments' => $this->whenLoaded('payments'),
            'created_by_user' => $this->whenLoaded('createdBy', function () {
                return $this->createdBy ? [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name
                ] : null;
            })
        ];
    }
}
