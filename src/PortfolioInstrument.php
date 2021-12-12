<?php

namespace TinkoffInvest;

class PortfolioInstrument
{
    /**
     * @var \TinkoffInvest\Currency
     */
    private Currency $average_position_currency;
    /**
     * @var float
     */
    private float $average_position_price;
    /**
     * @var float
     */
    private float $average_position_price_no_nkd;
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
    private Currency $expected_yield_currency;
    /**
     * @var float
     */
    private float $expected_yield_value;
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
     * @param \TinkoffInvest\InstrumentType $instrument_type
     * @param float $balance
     * @param float|null $blocked
     * @param int $lot
     * @param \TinkoffInvest\Currency|null $expected_yield_currency
     * @param float|null $expected_yield_value
     * @param string $name
     * @param \TinkoffInvest\Currency|null $average_position_currency
     * @param float|null $average_position_price
     * @param float|null $average_position_price_no_nkd
     * @throws \TinkoffInvest\Exception
     */
    function __construct(string $figi, string $ticker, ?string $isin, InstrumentType $instrument_type, float $balance, ?float $blocked, int $lot, ?Currency $expected_yield_currency, ?float $expected_yield_value, string $name, ?Currency $average_position_currency, ?float $average_position_price, ?float $average_position_price_no_nkd)
    {
        $this->figi = $figi;
        $this->ticker = $ticker;
        $this->isin = (string)$isin;
        $this->instrument_type = $instrument_type;
        $this->balance = $balance;
        $this->blocked = $blocked ?: .0;
        $this->lot = $lot;
        $this->expected_yield_value = $expected_yield_value ?: .0;
        $this->expected_yield_currency = $expected_yield_currency ?: Currency::getCurrency('RUB');
        $this->name = $name;
        $this->average_position_currency = $average_position_currency ?: Currency::getCurrency('RUB');
        $this->average_position_price = $average_position_price ?: .0;
        $this->average_position_price_no_nkd = $average_position_price_no_nkd ?: .0;
    }

    /**
     * @return \TinkoffInvest\Currency
     */
    public function getAveragePositionCurrency(): Currency
    {
        return $this->average_position_currency;
    }

    /**
     * @return float
     */
    public function getAveragePositionPrice(): float
    {
        return $this->average_position_price;
    }

    /**
     * @return float
     */
    public function getAveragePositionPriceNoNkd(): float
    {
        return $this->average_position_price_no_nkd;
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
    function getExpectedYieldCurrency(): Currency
    {
        return $this->expected_yield_currency;
    }

    /**
     * @return float
     */
    function getExpectedYieldValue(): float
    {
        return $this->expected_yield_value;
    }

    /**
     * @return string
     */
    function getFigi(): string
    {
        return $this->figi;
    }

    /**
     * @return \TinkoffInvest\InstrumentType
     */
    function getInstrumentType(): InstrumentType
    {
        return $this->instrument_type;
    }

    /**
     * @return string
     */
    function getIsin(): string
    {
        return $this->isin;
    }

    /**
     * @return int
     */
    function getLot(): int
    {
        return $this->lot;
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
    function getTicker(): string
    {
        return $this->ticker;
    }
}
