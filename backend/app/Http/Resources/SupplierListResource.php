<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierListResource extends JsonResource
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
            'created_by' => $this->created_by,
            'status' => $this->status,
        ];
    }
}
