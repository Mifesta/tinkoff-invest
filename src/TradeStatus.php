<?php

namespace TinkoffInvest;

class TradeStatus
{
    public const NORMAL_TRADING = 'NormalTrading';
    public const NOT_AVAILABLE_FOR_TRADING = 'NotAvailableForTrading';
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $trade_status
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $trade_status)
    {
        $this->value = self::checkTradeStatusValue($trade_status);
    }

    /**
     * Get trade status value
     * @param string $trade_status
     * @return \TinkoffInvest\TradeStatus
     * @throws \TinkoffInvest\Exception
     */
    public static function getStatus(string $trade_status): self
    {
        return new self($trade_status);
    }

    /**
     * Check trade status value
     * @param string $trade_status
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkTradeStatusValue(string $trade_status): string
    {
        $trade_status = strtolower($trade_status);
        switch ($trade_status) {
            case 'normaltrading':
            case 'normal_trading':
                return self::NORMAL_TRADING;
            case 'notavailablefortrading':
            case 'not_available_for_trading':
                return self::NOT_AVAILABLE_FOR_TRADING;
            default :
                throw new Exception('Undefined trade status');
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
