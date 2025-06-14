<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyProfileResource extends JsonResource
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
            "id" =>$this->id,
            "company_name" =>$this->company_name,
            "email" => $this->email,
            "mobile" =>$this->mobile,
            "phone" =>$this->phone,
            "address"=> $this->address,
            "state" =>$this->state,
            "country" => $this->country,
            "city" => $this->city,
            "postcode" =>$this->postcode,
            "logo" => $this->logo,
            "website" =>$this->website,
        ];
}
}
