<?php

namespace TinkoffInvest;

use Carbon\Carbon;

class CandleInterval
{
    public const DAY = 'day';
    public const HOUR = 'hour';
    public const MIN1 = '1min';
    public const MIN10 = '10min';
    public const MIN15 = '15min';
    public const MIN2 = '2min';
    public const MIN3 = '3min';
    public const MIN30 = '30min';
    public const MIN5 = '5min';
    public const MONTH = 'month';
    public const WEEK = 'week';
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $interval
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $interval)
    {
        $this->value = self::checkIntervalValue($interval);
    }

    /**
     * Get interval value
     * @param string $interval
     * @return \TinkoffInvest\CandleInterval
     * @throws \TinkoffInvest\Exception
     */
    public static function getInterval(string $interval): self
    {
        return new self($interval);
    }

    /**
     * Check interval value
     * @param string $interval
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkIntervalValue(string $interval): string
    {
        $interval = strtolower($interval);
        switch ($interval) {
            case 'day':
                return self::DAY;
            case 'hour':
                return self::HOUR;
            case '1min':
                return self::MIN1;
            case '10min':
                return self::MIN10;
            case '15min':
                return self::MIN15;
            case '2min':
                return self::MIN2;
            case '3min':
                return self::MIN3;
            case '30min':
                return self::MIN30;
            case '5min':
                return self::MIN5;
            case 'month':
                return self::MONTH;
            case 'week':
                return self::WEEK;
            default:
                throw new Exception('Undefined interval');
        }
    }

    /**
     * @param \Carbon\Carbon $to
     * @return \Carbon\Carbon
     * @throws \TinkoffInvest\Exception
     */
    public function allowableRequestInterval(Carbon $to): Carbon
    {
        switch ($this->value) {
            case 'day':
                return $to->clone()->subYear();
            case 'hour':
                return $to->clone()->subWeek();
            case '1min':
            case '10min':
            case '15min':
            case '2min':
            case '3min':
            case '30min':
            case '5min':
                return $to->clone()->subDay();
            case 'month':
                return $to->clone()->subYears(10);
            case 'week':
                return $to->clone()->subYears(2);
            default:
                throw new Exception('Undefined interval');
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
