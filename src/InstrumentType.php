<?php

namespace TinkoffInvest;

class InstrumentType
{
    public const BOND = 'Bond';
    public const CURRENCY = 'Currency';
    public const ETF = 'Etf';
    public const STOCK = 'Stock';
    /**
     * @var string|null
     */
    private string $value;

    /**
     * @param string $instrument_type
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $instrument_type)
    {
        $this->value = self::checkInstrumentTypeValue($instrument_type);
    }

    /**
     * Get instrument type value
     * @param string $instrument_type
     * @return \TinkoffInvest\InstrumentType
     * @throws \TinkoffInvest\Exception
     */
    public static function getType(string $instrument_type): self
    {
        return new self($instrument_type);
    }

    /**
     * Check instrument type value
     * @param string $instrument_type
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkInstrumentTypeValue(string $instrument_type): ?string
    {
        $instrument_type = strtolower($instrument_type);
        switch ($instrument_type) {
            case 'bond' :
                return self::BOND;
            case 'currency' :
                return self::CURRENCY;
            case 'etf' :
                return self::ETF;
            case 'stock' :
                return self::STOCK;
            case null:
                return null;
            default :
                throw new Exception('Undefined instrument type');
        }
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}
