<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function show(string $invoiceNumber): Response
    {
        $order = Order::where('invoice_number', $invoiceNumber)
            ->with(['transactions'])
            ->firstOrFail();

        return Inertia::render('Public/Checkout/Invoice', [
            'order' => $order,
        ]);
    }
}
