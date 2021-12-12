<?php

namespace TinkoffInvest;

class Orderbook
{
    /**
     * price|count array of bids
     * @var \TinkoffInvest\OrderResponse[]
     */
    private array $asks;
    /**
     * price|count array of asks
     * @var \TinkoffInvest\OrderResponse[]
     */
    private array $bids;
    /**
     * @var float
     */
    private float $close_price;
    /**
     * Depth of orderbook
     * @var integer
     */
    private int $depth;
    /**
     * @var float
     */
    private float $face_value;
    /**
     * @var string
     */
    private string $figi;
    /**
     * @var float
     */
    private float $last_price;
    /**
     * @var float
     */
    private float $limit_down;
    /**
     * @var float
     */
    private float $limit_up;
    /**
     * @var float
     */
    private float $min_price_increment;
    /**
     * @var \TinkoffInvest\TradeStatus
     */
    private TradeStatus $trade_status;

    /**
     * @param int $depth
     * @param \TinkoffInvest\OrderResponse[] $bids
     * @param \TinkoffInvest\OrderResponse[] $asks
     * @param string $figi
     * @param \TinkoffInvest\TradeStatus $trade_status
     * @param float $min_price_increment
     * @param float $face_value
     * @param float $last_price
     * @param float $close_price
     * @param int $limit_up
     * @param int $limit_down
     */
    public function __construct(int $depth, array $bids, array $asks, string $figi, TradeStatus $trade_status, float $min_price_increment, float $face_value, float $last_price, float $close_price, int $limit_up, int $limit_down)
    {
        $this->depth = $depth;
        $this->asks = $asks;
        $this->bids = $bids;
        $this->figi = $figi;
        $this->trade_status = $trade_status;
        $this->min_price_increment = $min_price_increment;
        $this->face_value = $face_value;
        $this->last_price = $last_price;
        $this->close_price = $close_price;
        $this->limit_up = $limit_up;
        $this->limit_down = $limit_down;
    }

    /**
     * @return float|null
     */
    function getBestPriceToBuy(): ?float
    {
        return $this->asks[0]->price ?? null;
    }

    /**
     * @return float|null
     */
    function getBestPriceToBuyLotCount(): ?float
    {
        return $this->asks[0]->quantity ?? null;
    }

    /**
     * @return float|null
     */
    function getBestPriceToSell(): ?float
    {
        return (count($this->bids) > 0) ? $this->bids[0]->price : null;
    }

    /**
     * @return float|null
     */
    function getBestPriceToSellLotCount(): ?float
    {
        return (count($this->bids) > 0) ? $this->bids[0]->quantity : null;
    }

    /**
     * @return array
     */
    function getBestPricesToBuy(): array
    {
        return $this->asks;
    }

    /**
     * @return array
     */
    function getBestPricesToSell(): array
    {
        return $this->bids;
    }

    /**
     * @return float
     */
    public function getClosePrice(): float
    {
        return $this->close_price;
    }

    /**
     * @return int
     */
    function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @return float
     */
    public function getFaceValue(): float
    {
        return $this->face_value;
    }

    /**
     * @return string
     */
    function getFigi(): string
    {
        return $this->figi;
    }

    /**
     * @return float
     */
    public function getLastPrice(): float
    {
        return $this->last_price;
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
}
