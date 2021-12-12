<?php

namespace TinkoffInvest;

use Carbon\Carbon;

class OperationTrade
{
    /**
     * @var \Carbon\Carbon
     */
    private Carbon $date;
    /**
     * @var float
     */
    private float $price;
    /**
     * @var int
     */
    private int $quantity;
    /**
     * @var string
     */
    private string $trade_id;

    /**
     * @param string $trade_id
     * @param \Carbon\Carbon $date
     * @param float $price
     * @param int $quantity
     */
    public function __construct(string $trade_id, Carbon $date, float $price, int $quantity)
    {
        $this->trade_id = $trade_id;
        $this->date = $date;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function getTradeId(): string
    {
        return $this->trade_id;
    }
}
