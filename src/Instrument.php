<?php

namespace TinkoffInvest;

class Instrument
{
    /**
     * @var \TinkoffInvest\Currency
     */
    private Currency $currency;
    /**
     * @var string
     */
    private string $figi;
    /**
     * @var \TinkoffInvest\InstrumentType
     */
    private InstrumentType $instrument_type;
    /**
     * @var string
     */
    private string $isin;
    /**
     * @var int
     */
    private int $lot;
    /**
     * @var float
     */
    private float $min_price_increment;
    /**
     * @var float
     */
    private float $min_quantity;
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $ticker;

    /**
     * @param string $figi
     * @param string $ticker
     * @param string|null $isin
     * @param float|null $min_price_increment
     * @param int $lot
     * @param int|null $min_quantity
     * @param \TinkoffInvest\Currency $currency
     * @param string $name
     * @param \TinkoffInvest\InstrumentType $instrument_type
     */
    public function __construct(string $figi, string $ticker, ?string $isin, ?float $min_price_increment, int $lot, ?int $min_quantity, Currency $currency, string $name, InstrumentType $instrument_type)
    {
        $this->currency = $currency;
        $this->figi = $figi;
        $this->isin = $isin ?: '';
        $this->lot = $lot;
        $this->min_price_increment = $min_price_increment ?: .0;
        $this->min_quantity = $min_quantity ?: $lot;
        $this->name = $name;
        $this->ticker = $ticker;
        $this->instrument_type = $instrument_type;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getFigi(): string
    {
        return $this->figi;
    }

    /**
     * @return \TinkoffInvest\InstrumentType
     */
    public function getInstrumentType(): InstrumentType
    {
        return $this->instrument_type;
    }

    /**
     * @return string
     */
    public function getIsin(): string
    {
        return $this->isin;
    }

    /**
     * @return int
     */
    public function getLot(): int
    {
        return $this->lot;
    }

    /**
     * @return float
     */
    public function getMinPriceIncrement(): float
    {
        return $this->min_price_increment;
    }

    /**
     * @return int
     */
    public function getMinQuantity(): int
    {
        return $this->min_quantity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTicker(): string
    {
        return $this->ticker;
    }
}
