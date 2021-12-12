<?php

namespace TinkoffInvest;

class Commission
{
    /**
     * @var \TinkoffInvest\Currency
     */
    private Currency $currency;

    /**
     * @var float
     */
    private float $value;

    /**
     * @param \TinkoffInvest\Currency|null $currency
     * @param float|null $value
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(?Currency $currency, ?float $value)
    {
        $this->currency = $currency ?: Currency::getCurrency('RUB');
        $this->value = $value ?: .0;
    }

    /**
     * @return \TinkoffInvest\Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }
}
