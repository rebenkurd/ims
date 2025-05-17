<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'purchase_code' => $this->purchase_code,
            'total_quantities' => $this->total_quantities,
            'purchase_status' => $this->purchase_status,
            'reference_no' => $this->reference_no,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'total' => $this->total,
            'subtotal' => $this->subtotal,
            'due_balance' => $this->due_balance,
            'note' => $this->note,
            'status' => $this->status,
            'supplier_id' => $this->supplier_id,

            // Include relationships
            'supplier' => $this->whenLoaded('supplier'),
            'purchase_items' => $this->whenLoaded('purchaseItems', function () {
                return $this->purchaseItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? null,
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->unit_price,
                        'total_amount' => $item->total_price,
                        'discount' => $item->discount,
                        'purchase_price' => $item->product->purchase_price,
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
