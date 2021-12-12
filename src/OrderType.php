<?php

namespace TinkoffInvest;

class OrderType
{
    public const LIMIT = 'Limit';
    public const MARKET = 'Market';
    /**
     * @var string
     */
    private string $value;

    /**+
     * @param string $order_type
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $order_type)
    {
        $this->value = self::checkOrderTypeValue($order_type);
    }

    /**
     * Get order type value
     * @param string $order_type
     * @return \TinkoffInvest\OrderType
     * @throws \TinkoffInvest\Exception
     */
    public static function getType(string $order_type): self
    {
        return new self($order_type);
    }

    /**
     * Check order type value
     * @param string $order_type
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkOrderTypeValue(string $order_type): string
    {
        $order_type = strtolower($order_type);
        switch ($order_type) {
            case 'limit' :
                return self::LIMIT;
            case 'market' :
                return self::MARKET;
            default :
                throw new Exception('Undefined order type');
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
