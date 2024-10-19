<?php 
// app/Http/Controllers/CartController.php
namespace App\Http\Controllers;

use App\Services\InvoiceService;
use App\Models\Cart;

class CartController extends Controller
{
    public function showInvoice($cartId)
    {
        $cart = Cart::with('items.product')->findOrFail($cartId);
        $invoiceService = new InvoiceService($cart);
        $invoice = $invoiceService->generateInvoice();
        return view('invoice', compact('invoice'));
    }
}
