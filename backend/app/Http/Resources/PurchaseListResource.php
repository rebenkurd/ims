<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseListResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'code' => $this->purchase_code,
            'purchase_status' => $this->purchase_status,
            'total_quantities' => $this->total_quantities,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'supplier' => $this->supplier?->name,
            'total' => $this->total,
            'subtotal' => $this->subtotal,
            'created_by' => $this->createdBy?->name,
            'status' => $this->status,
            'invoice' => $this->invoices->first() ? [
                'id' => $this->invoices->first()->id,
                'invoice_number' => $this->invoices->first()->invoice_number
            ] : null
        ];
    }
}
