<?php

namespace TinkoffInvest;

class Currency
{
    public const CHF = 'CHF';
    public const CNY = 'CNY';
    public const EUR = 'EUR';
    public const GBP = 'GBP';
    public const HKD = 'HKD';
    public const JPY = 'JPY';
    public const RUB = 'RUB';
    public const TRY = 'TRY';
    public const USD = 'USD';
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $currency
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $currency)
    {
        $this->value = self::checkCurrencyValue($currency);
    }

    /**
     * Get currency value
     * @param string $currency
     * @return \TinkoffInvest\Currency
     * @throws \TinkoffInvest\Exception
     */
    public static function getCurrency(string $currency): self
    {
        return new self($currency);
    }

    /**
     * Check currency value
     * @param string $currency
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkCurrencyValue(string $currency): string
    {
        $currency = strtolower($currency);
        switch ($currency) {
            case 'chf' :
                return self::CHF;
            case 'cny' :
                return self::CNY;
            case 'eur' :
                return self::EUR;
            case 'gbp' :
                return self::GBP;
            case 'hkd' :
                return self::HKD;
            case 'jpy' :
                return self::JPY;
            case 'rub' :
            case 'rur' :
                return self::RUB;
            case 'usd' :
                return self::USD;
            case 'trl' :
            case 'try' :
                return self::TRY;
            default :
                throw new Exception('Undefined currency');
        }
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
