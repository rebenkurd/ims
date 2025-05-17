<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'brand_id', 'category_id', 'unit', 'per_carton', 'minimum_qty', 'expire_date',
        'barcode', 'description', 'image', 'price', 'tax', 'purchase_price', 'tax_type', 'profit_margin',
        'sales_price', 'final_price', 'discount_type', 'discount', 'current_opening_stock', 'adjust_stock',
        'adjustment_note', 'created_by', 'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

}
