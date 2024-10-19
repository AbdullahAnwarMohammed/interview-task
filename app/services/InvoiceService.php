<?php 
// app/Services/InvoiceService.php
namespace App\Services;

use App\Models\Cart;

class InvoiceService
{
    private $cart;
    private $subtotal = 0;
    private $shippingFees = 0;
    private $vat = 0;
    private $discounts = [];

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function calculateSubtotal()
    {
        foreach ($this->cart->items as $item) {
            $this->subtotal += $item->product->price * $item->quantity;
        }

        return $this->subtotal;
    }

    public function calculateShipping()
    {
        foreach ($this->cart->items as $item) {
            $rate = $this->getShippingRate($item->product->shipped_from);
            $this->shippingFees += $item->product->weight * $item->quantity * $rate;
        }

        return $this->shippingFees;
    }

    private function getShippingRate($country)
    {
        switch ($country) {
            case 'US':
                return 2;
            case 'UK':
                return 3;
            case 'CN':
                return 2;
        }
    }

    public function applyDiscounts()
    {
        foreach ($this->cart->items as $item) {
            if ($item->product->name == 'Shoes') {
                $discount = $item->product->price * 0.10 * $item->quantity;
                $this->discounts[] = "10% off shoes: -\${$discount}";
                $this->subtotal -= $discount;
            }
        }
        
    }

    public function calculateVAT()
    {
        $this->vat = $this->subtotal * 0.14;
        return $this->vat;
    }

    public function getTotal()
    {
        return $this->subtotal + $this->shippingFees + $this->vat - array_sum($this->discounts);
    }

    public function generateInvoice()
    {
        $this->calculateSubtotal();
        $this->calculateShipping();
        $this->applyDiscounts();
        $this->calculateVAT();

        return [
            'subtotal' => $this->subtotal,
            'shipping' => $this->shippingFees,
            'vat' => $this->vat,
            'discounts' => $this->discounts,
            'total' => $this->getTotal(),
        ];
    }
}
