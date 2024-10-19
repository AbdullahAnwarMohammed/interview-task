<?php 
// tests/Unit/InvoiceServiceTest.php
namespace Tests\Unit;

use Tests\TestCase;
use App\Services\InvoiceService;
use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;

class InvoiceServiceTest extends TestCase
{
    public function testInvoiceCalculations()
    {
        $cart = Cart::create(['user_id' => null]);
        $product1 = Product::create(['name' => 'T-shirt', 'price' => 30.99, 'shipped_from' => 'US', 'weight' => 0.2]);
        $product2 = Product::create(['name' => 'Blouse', 'price' => 10.99, 'shipped_from' => 'UK', 'weight' => 0.3]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product1->id, 'quantity' => 1]);
        CartItem::create(['cart_id' => $cart->id, 'product_id' => $product2->id, 'quantity' => 1]);

        $invoiceService = new InvoiceService($cart);
        $invoice = $invoiceService->generateInvoice();

        $this->assertEquals(41.98, $invoice['subtotal']);
    }
}
