<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'phone' => $this->phone,
            'opening_balance' => $this->opening_balance,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'postcode' => $this->postcode,
            'address' => $this->address,
            'created_by' => $this->createdBy ? $this->createdBy->name : null,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
