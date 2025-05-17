<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('unit')->nullable();
            $table->integer('per_carton')->default(0);
            $table->integer('minimum_qty');
            $table->date('expire_date')->nullable();
            $table->string('barcode')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('tax')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->string('tax_type')->nullable();
            $table->integer('profit_margin')->nullable()->default(0);
            $table->decimal('sales_price', 10, 2);
            $table->decimal('final_price', 10, 2)->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('discount', 5, 2)->nullable();
            $table->integer('current_opening_stock')->nullable();
            $table->integer('adjust_stock')->nullable();
            $table->text('adjustment_note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
