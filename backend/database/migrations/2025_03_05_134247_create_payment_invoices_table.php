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
        Schema::create('payment_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number');
            $table->string('reference_no');
            $table->unsignedBigInteger('sale_invoice_id')->nullable();
            $table->unsignedBigInteger('purchase_invoice_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date');
            $table->string('payment_method'); // Cash, Credit, Bank Transfer
            $table->text('note')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sale_invoice_id')->references('id')->on('sales_invoices');
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_invoices');
    }
};
