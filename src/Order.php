<?php

namespace TinkoffInvest;

class Order
{
    /**
     * @var \TinkoffInvest\Commission
     */
    private Commission $commission;
    /**
     * @var int
     */
    private int $executed_lots;
    /**
     * @var string
     */
    private string $figi;
    /**
     * @var string
     */
    private string $message;
    /**
     * @var \TinkoffInvest\OperationType
     */
    private OperationType $operation_type;
    /**
     * @var string
     */
    private string $order_id;
    /**
     * @var \TinkoffInvest\OrderType
     */
    private OrderType $order_type;
    /**
     * @var float
     */
    private float $price;
    /**
     * @var string
     */
    private string $reject_reason;
    /**
     * @var int
     */
    private int $requested_lots;
    /**
     * @var \TinkoffInvest\OrderStatus
     */
    private OrderStatus $status;

    /**
     * @param string $order_id
     * @param \TinkoffInvest\OperationType $operation_type
     * @param \TinkoffInvest\OrderStatus $status
     * @param string $reject_reason
     * @param int $requested_lots
     * @param int $executed_lots
     * @param \TinkoffInvest\Commission $commission
     * @param string $figi
     * @param \TinkoffInvest\OrderType $order_type
     * @param string $message
     * @param float $price
     */
    function __construct(string $order_id, OperationType $operation_type, OrderStatus $status, string $reject_reason, int $requested_lots, int $executed_lots, Commission $commission, string $figi, OrderType $order_type, string $message, float $price)
    {
        $this->order_id = $order_id;
        $this->operation_type = $operation_type;
        $this->status = $status;
        $this->reject_reason = $reject_reason;
        $this->requested_lots = $requested_lots;
        $this->executed_lots = $executed_lots;
        $this->commission = $commission;
        $this->figi = $figi;
        $this->order_type = $order_type;
        $this->message = $message;
        $this->price = $price;
    }

    /**
     * @return Commission
     */
    function getCommission(): Commission
    {
        return $this->commission;
    }

    /**
     * @return int
     */
    function getExecutedLots(): int
    {
        return $this->executed_lots;
    }

    /**
     * @return string
     */
    function getFigi(): string
    {
        return $this->figi;
    }

    /**
     * @return string
     */
    function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return \TinkoffInvest\OperationType
     */
    function getOperationType(): OperationType
    {
        return $this->operation_type;
    }

    /**
     * @return string
     */
    function getOrderId(): string
    {
        return $this->order_id;
    }

    /**
     * @return \TinkoffInvest\OrderType
     */
    function getOrderType(): OrderType
    {
        return $this->order_type;
    }

    /**
     * @return float
     */
    function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    function getRejectReason(): string
    {
        return $this->reject_reason;
    }

    /**
     * @return int
     */
    function getRequestedLots(): int
    {
        return $this->requested_lots;
    }

    /**
     * @return \TinkoffInvest\OrderStatus
     */
    function getStatus(): OrderStatus
    {
        return $this->status;
    }
}
