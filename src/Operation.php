<?php

namespace TinkoffInvest;

use Carbon\Carbon;

class Operation
{
    /**
     * @var \TinkoffInvest\Commission
     */
    private Commission $commission;
    /**
     * @var \TinkoffInvest\Currency
     */
    private Currency $currency;
    /**
     * @var \Carbon\Carbon
     */
    private Carbon $date;
    /**
     * @var string
     */
    private string $figi;
    /**
     * @var string
     */
    private string $id;
    /**
     * @var \TinkoffInvest\InstrumentType
     */
    private InstrumentType $instrument_type;
    /**
     * @var bool
     */
    private bool $is_margin_call;
    /**
     * @var \TinkoffInvest\OperationType
     */
    private OperationType $operation_type;
    /**
     * @var float
     */
    private float $payment;
    /**
     * @var float
     */
    private float $price;
    /**
     * @var int
     */
    private int $quantity;
    /**
     * @var \TinkoffInvest\OperationStatus
     */
    private OperationStatus $status;
    /**
     * @var \TinkoffInvest\OperationTrade[]
     */
    private array $trades;

    /**
     * @param string $id
     * @param \TinkoffInvest\OperationStatus $status
     * @param array $trades
     * @param \TinkoffInvest\Commission $commission
     * @param \TinkoffInvest\Currency $currency
     * @param float $payment
     * @param float $price
     * @param int $quantity
     * @param string $figi
     * @param \TinkoffInvest\InstrumentType $instrument_type
     * @param bool $is_margin_call
     * @param \Carbon\Carbon $date
     * @param \TinkoffInvest\OperationType $operation_type
     */
    public function __construct(string $id, OperationStatus $status, array $trades, Commission $commission, Currency $currency, float $payment, float $price, int $quantity, string $figi, InstrumentType $instrument_type, bool $is_margin_call, Carbon $date, OperationType $operation_type)
    {
        $this->id = $id;
        $this->status = $status;
        $this->trades = $trades;
        $this->commission = $commission;
        $this->currency = $currency;
        $this->payment = $payment;
        $this->price = $price;
        $this->quantity = $quantity;
        $this->figi = $figi;
        $this->instrument_type = $instrument_type;
        $this->is_margin_call = $is_margin_call;
        $this->date = $date;
        $this->operation_type = $operation_type;
    }

    /**
     * @return \TinkoffInvest\Commission
     */
    public function getCommission(): Commission
    {
        return $this->commission;
    }

    /**
     * @return \TinkoffInvest\Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getFigi(): string
    {
        return $this->figi;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return \TinkoffInvest\InstrumentType
     */
    public function getInstrumentType(): InstrumentType
    {
        return $this->instrument_type;
    }

    /**
     * @return bool
     */
    public function getIsMarginCall(): bool
    {
        return $this->is_margin_call;
    }

    /**
     * @return \TinkoffInvest\OperationType
     */
    public function getOperationType(): OperationType
    {
        return $this->operation_type;
    }

    /**
     * @return float
     */
    public function getPayment(): float
    {
        return $this->payment;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return \TinkoffInvest\OperationStatus
     */
    public function getStatus(): OperationStatus
    {
        return $this->status;
    }

    /**
     * @return \TinkoffInvest\OperationTrade[]
     */
    public function getTrades(): array
    {
        return $this->trades;
    }
}
