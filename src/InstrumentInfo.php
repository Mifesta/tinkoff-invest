<?php

namespace TinkoffInvest;

class InstrumentInfo
{
    /**
     * @var float
     */
    private float $accrued_interest;
    /**
     * @var string
     */
    private string $figi;
    /**
     * @var float
     */
    private float $limit_down;
    /**
     * @var float
     */
    private float $limit_up;
    /**
     * @var int
     */
    private int $lot;
    /**
     * @var float
     */
    private float $min_price_increment;
    private TradeStatus $trade_status;

    /**
     * @param \TinkoffInvest\TradeStatus $trade_status
     * @param float $min_price_increment
     * @param int $lot
     * @param string $figi
     */
    public function __construct(TradeStatus $trade_status, float $min_price_increment, int $lot, string $figi)
    {
        $this->trade_status = $trade_status;
        $this->min_price_increment = $min_price_increment;
        $this->lot = $lot;
        $this->figi = $figi;
    }

    /**
     * @return float
     */
    public function getAccruedInterest(): float
    {
        return $this->accrued_interest;
    }

    /**
     * @return string
     */
    public function getFigi(): string
    {
        return $this->figi;
    }

    /**
     * @return float
     */
    public function getLimitDown(): float
    {
        return $this->limit_down;
    }

    /**
     * @return float
     */
    public function getLimitUp(): float
    {
        return $this->limit_up;
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
     * @return \TinkoffInvest\TradeStatus
     */
    public function getTradeStatus(): TradeStatus
    {
        return $this->trade_status;
    }

    /**
     * @param float $accrued_interest
     * @return void
     */
    public function setAccruedInterest(float $accrued_interest)
    {
        $this->accrued_interest = $accrued_interest;
    }

    /**
     * @param float $limit_down
     * @return void
     */
    public function setLimitDown(float $limit_down)
    {
        $this->limit_down = $limit_down;
    }

    /**
     * @param float $limit_up
     * @return void
     */
    public function setLimitUp(float $limit_up)
    {
        $this->limit_up = $limit_up;
    }
}
