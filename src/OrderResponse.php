<?php

namespace TinkoffInvest;

class OrderResponse
{
    public float $price;
    public int $quantity;

    /**
     * @param float $price
     * @param int $quantity
     */
    public function __construct(float $price, int $quantity)
    {
        $this->price = $price;
        $this->quantity = $quantity;
    }
}
