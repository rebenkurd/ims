<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Invoice #{{ $invoice->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                font-size: 12pt;
                padding: 0;
                margin: 0;
            }
            .container {
                width: 100%;
                padding: 0;
                margin: 0;
            }
            .invoice-header {
                padding-top: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="invoice-header bg-blue-600 text-white px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold">PURCHASE INVOICE</h1>
                        <p class="text-blue-100">Invoice #{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-blue-100">Date: {{ $invoice->invoice_date->format('d M Y') }}</p>
                        <p class="text-blue-100">Due Date: {{ $invoice->due_date->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Company and Supplier Info -->
            <div class="grid grid-cols-2 gap-4 px-6 py-4 border-b">
                <div>
                    <h2 class="font-bold text-gray-700">From:</h2>
                    <p class="font-bold">{{ config('app.name') }}</p>
                    <p>123 Business Street</p>
                    <p>City, State 10001</p>
                    <p>Phone: (123) 456-7890</p>
                    <p>Email: accounts@example.com</p>
                </div>
                <div>
                    <h2 class="font-bold text-gray-700">To:</h2>
                    <p class="font-bold">{{ $purchase->supplier->name }}</p>
                    <p>{{ $purchase->supplier->address }}</p>
                    <p>{{ $purchase->supplier->phone }}</p>
                    <p>{{ $purchase->supplier->email }}</p>
                </div>
            </div>

            <!-- Purchase Details -->
            <div class="px-6 py-4 border-b">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-gray-600">Purchase Code:</p>
                        <p class="font-semibold">{{ $purchase->purchase_code }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Reference:</p>
                        <p class="font-semibold">{{ $purchase->reference_no ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status:</p>
                        <p class="font-semibold capitalize">{{ $invoice->payment_status }}</p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="px-6 py-2">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border text-left">#</th>
                            <th class="py-2 px-4 border text-left">Item</th>
                            <th class="py-2 px-4 border text-left">Qty</th>
                            <th class="py-2 px-4 border text-left">Unit Price</th>
                            <th class="py-2 px-4 border text-left">Discount</th>
                            <th class="py-2 px-4 border text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->purchaseItems as $index => $item)
                        <tr>
                            <td class="py-2 px-4 border">{{ $index + 1 }}</td>
                            <td class="py-2 px-4 border">
                                {{ $item->product->name }}
                                @if($item->product->barcode)
                                <br><small class="text-gray-500">SKU: {{ $item->product->barcode }}</small>
                                @endif
                            </td>
                            <td class="py-2 px-4 border">{{ $item->quantity }}</td>
                            <td class="py-2 px-4 border">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($item->discount, 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($item->total_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="px-6 py-4">
                <div class="flex justify-end">
                    <div class="w-64">
                        <div class="flex justify-between py-2 border-b">
                            <span class="font-semibold">Subtotal:</span>
                            <span>{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="font-semibold">Discount:</span>
                            <span>-{{ number_format($invoice->discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="font-semibold">Tax:</span>
                            <span>{{ number_format($invoice->tax, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b-2 border-gray-300">
                            <span class="font-semibold">Grand Total:</span>
                            <span class="font-bold">{{ number_format($invoice->final_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="font-semibold">Amount Paid:</span>
                            <span>{{ number_format($purchase->payments->sum('amount'), 2) }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-t-2 border-gray-300">
                            <span class="font-semibold">Balance Due:</span>
                            <span class="font-bold">{{ number_format($invoice->final_amount - $purchase->payments->sum('amount'), 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payments -->
            @if($purchase->payments->count() > 0)
            <div class="px-6 py-4 border-t">
                <h3 class="font-bold mb-2">Payment History</h3>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 border text-left">Date</th>
                            <th class="py-2 px-4 border text-left">Method</th>
                            <th class="py-2 px-4 border text-left">Note</th>
                            <th class="py-2 px-4 border text-left">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->payments as $payment)
                        <tr>
                            <td class="py-2 px-4 border">{{ $payment->payment_date->format('d M Y') }}</td>
                            <td class="py-2 px-4 border capitalize">{{ $payment->payment_method }}</td>
                            <td class="py-2 px-4 border">{{ $payment->note ?? 'N/A' }}</td>
                            <td class="py-2 px-4 border">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Notes -->
            <div class="px-6 py-4 border-t">
                <h3 class="font-bold mb-2">Notes</h3>
                <p class="text-gray-700">{{ $invoice->notes ?? 'No notes available.' }}</p>
            </div>

            <!-- Footer -->
            <div class="bg-gray-100 px-6 py-4 flex justify-between items-center no-print">
                <div>
                    <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Print Invoice
                    </button>
                </div>
                <div>
                    <a href="{{ route('purchases.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to Purchases
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
