<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'previous_due' => $this->previous_due,
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
