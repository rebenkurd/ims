<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;

class PurchaseInvoiceController extends Controller
{
    public function show(Purchase $purchase, PurchaseInvoice $invoice)
    {
        // Verify the invoice belongs to the purchase
        if ($invoice->purchase_id !== $purchase->id) {
            abort(404);
        }

        $invoice->load([
            'purchase.purchaseItems.product',
            'purchase.payments',
            'supplier',
            'createdBy'
        ]);

        return view('purchases.invoices.show', compact('purchase', 'invoice'));
    }

    public function print(Purchase $purchase, PurchaseInvoice $invoice)
    {
        if ($invoice->purchase_id !== $purchase->id) {
            abort(404);
        }

        $invoice->load([
            'purchase.purchaseItems.product',
            'purchase.payments',
            'supplier',
            'createdBy'
        ]);

        return view('purchases.invoices.print', compact('purchase', 'invoice'));
    }
}
