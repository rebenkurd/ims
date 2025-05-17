<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SaleListResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'code' => $this->sale_code,
            'sale_status' => $this->sale_status,
            'total_quantities' => $this->total_quantities,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'customer' => $this->customer?->name,
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
