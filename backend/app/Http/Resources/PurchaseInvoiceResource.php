<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseInvoiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'purchase_id' => $this->purchase_id,
            'supplier_id' => $this->supplier_id,
            'total_amount' => $this->total_amount,
            'discount' => $this->discount,
            'discount_type' => $this->discount,
            'final_amount' => $this->final_amount,
            'payment_status' => $this->payment_status,
            'status' => $this->status,
            'invoice_date' => $this->invoice_date,
            'due_date' => $this->due_date,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'supplier' => $this->whenLoaded('supplier'),
            'purchase' => $this->whenLoaded('purchase'),
            'payments' => $this->whenLoaded('payments'),
            'created_by' => $this->whenLoaded('createdBy'),
        ];
    }
}
