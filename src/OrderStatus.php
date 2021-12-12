<?php

namespace TinkoffInvest;

class OrderStatus
{
    public const CANCELLED = 'Cancelled';
    public const FILL = 'Fill';
    public const NEW = 'New';
    public const PARTIALLY_FILL = 'PartiallyFill';
    public const PENDING_CANCEL = 'PendingCancel';
    public const PENDING_NEW = 'PendingNew';
    public const PENDING_REPLACE = 'PendingReplace';
    public const REJECTED = 'Rejected';
    public const REPLACED = 'Replaced';
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $order_status
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $order_status)
    {
        $this->value = self::checkOrderStatusValue($order_status);
    }

    /**
     * Get order status value
     * @param string $order_status
     * @return \TinkoffInvest\OrderStatus
     * @throws \TinkoffInvest\Exception
     */
    public static function getStatus(string $order_status): self
    {
        return new self($order_status);
    }

    /**
     * Check order status value
     * @param string $order_status
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkOrderStatusValue(string $order_status): string
    {
        $order_status = strtolower($order_status);
        switch ($order_status) {
            case 'new' :
                return self::NEW;
            case 'partiallyfill' :
            case 'partially_fill' :
                return self::PARTIALLY_FILL;
            case 'fill' :
                return self::FILL;
            case 'cancelled' :
                return self::CANCELLED;
            case 'replaced' :
                return self::REPLACED;
            case 'pendingcancel' :
            case 'pending_cancel' :
                return self::PENDING_CANCEL;
            case 'rejected' :
                return self::REJECTED;
            case 'pendingreplace' :
            case 'pending_replace' :
                return self::PENDING_REPLACE;
            case 'pendingnew' :
            case 'pending_new' :
                return self::PENDING_NEW;
            default :
                throw new Exception('Undefined order status');
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
