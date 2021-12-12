<?php

namespace TinkoffInvest;

use Carbon\Carbon;

class Candle
{
    /**
     * @var float
     */
    private float $close;
    /**
     * @var string
     */
    private string $figi;
    /**
     * @var float
     */
    private float $high;
    /**
     * @var CandleInterval
     */
    private CandleInterval $interval;
    /**
     * @var float
     */
    private float $low;
    /**
     * @var float
     */
    private float $open;
    /**
     * @var Carbon
     */
    private Carbon $time;
    /**
     * @var int
     */
    private int $volume;

    /**
     * @param string $figi
     * @param \Carbon\Carbon $time
     * @param float $open
     * @param float $close
     * @param float $high
     * @param float $low
     * @param int $volume
     * @param \TinkoffInvest\CandleInterval $interval
     */
    public function __construct(string $figi, Carbon $time, float $open, float $close, float $high, float $low, int $volume, CandleInterval $interval)
    {
        $this->figi = $figi;
        $this->open = $open;
        $this->close = $close;
        $this->high = $high;
        $this->low = $low;
        $this->volume = $volume;
        $this->time = $time;
        $this->interval = $interval;
    }

    /**
     * @return float
     */
    public function getClose(): float
    {
        return $this->close;
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
    public function getHigh(): float
    {
        return $this->high;
    }

    /**
     * @return CandleInterval
     */
    public function getInterval(): CandleInterval
    {
        return $this->interval;
    }

    /**
     * @return float
     */
    public function getLow(): float
    {
        return $this->low;
    }

    /**
     * @return float
     */
    public function getOpen(): float
    {
        return $this->open;
    }

    /**
     * @return Carbon
     */
    public function getTime(): Carbon
    {
        return $this->time;
    }

    /**
     * @return int
     */
    public function getVolume(): int
    {
        return $this->volume;
    }
}
