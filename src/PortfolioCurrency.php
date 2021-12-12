<?php

namespace TinkoffInvest;

class PortfolioCurrency
{
    /**
     * @var float
     */
    private float $balance;
    /**
     * @var float
     */
    private float $blocked;
    /**
     * @var \TinkoffInvest\Currency
     */
    private Currency $currency;

    /**
     * @param float $balance
     * @param \TinkoffInvest\Currency $currency
     * @param float $blocked
     */
    function __construct(float $balance, float $blocked, Currency $currency)
    {
        $this->balance = $balance;
        $this->blocked = $blocked;
        $this->currency = $currency;
    }

    /**
     * @return float
     */
    function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @return float
     */
    public function getBlocked(): float
    {
        return $this->blocked;
    }

    /**
     * @return \TinkoffInvest\Currency
     */
    function getCurrency(): Currency
    {
        return $this->currency;
    }
}
