<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'name' => 'required|string|max:255',
            'brand_id' => 'required|integer|exists:brands,id',
            'category_id' => 'required|integer|exists:categories,id',
            'per_carton' => 'nullable|integer',
            'minimum_qty' => 'integer',
            'expire_date' => 'nullable',
            'barcode' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'purchase_price' => 'required|numeric|min:0',
            'profit_margin' => 'nullable|numeric|min:0|max:100',
            'sales_price' => 'required|numeric|min:0',
            'final_price' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric',
            'current_opening_stock' => 'nullable|integer',
            'adjust_stock' => 'nullable|integer',
        ];
    }
}
