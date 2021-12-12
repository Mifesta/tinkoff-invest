<?php

namespace TinkoffInvest\Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use TinkoffInvest\BrokerAccount;
use TinkoffInvest\BrokerAccountType;
use TinkoffInvest\Candle;
use TinkoffInvest\CandleInterval;
use TinkoffInvest\Client;
use TinkoffInvest\Commission;
use TinkoffInvest\Currency;
use TinkoffInvest\Instrument;
use TinkoffInvest\InstrumentInfo;
use TinkoffInvest\InstrumentType;
use TinkoffInvest\OperationStatus;
use TinkoffInvest\OperationTrade;
use TinkoffInvest\OperationType;
use TinkoffInvest\Orderbook;
use TinkoffInvest\OrderResponse;
use TinkoffInvest\OrderStatus;
use TinkoffInvest\OrderType;
use TinkoffInvest\Portfolio;
use TinkoffInvest\Order;
use TinkoffInvest\PortfolioCurrency;
use TinkoffInvest\PortfolioInstrument;
use TinkoffInvest\ResponseStatus;
use TinkoffInvest\TradeStatus;

class TinkoffInvestTest extends TestCase
{
    const TEST_FIGI = 'BBG004730N88';
    const TEST_CURRENCY = 'RUB';
    const TEST_BOND_TICKER = 'SU26227RMFS7';
    const TEST_STOCK_TICKER = 'SBER';
    const TEST_ETF_TICKER = 'FXTB';
    const TEST_CURRENCY_TICKER = 'EUR_RUB__TOM';
    /**
     * @var string|null
     */
    static protected ?string $broker_account_id = null;
    /**
     * @var Client|null
     */
    protected ?Client $fixture;

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testSandboxRegister()
    {
        $account = $this->fixture->sandboxRegister();
        $this->assertInstanceOf(BrokerAccount::class, $account, 'Broker account');
        $this->assertInstanceOf(BrokerAccountType::class, $account->getBrokerAccountType(), 'Broker account type');
        $this->assertIsString($account->getBrokerAccountType()->getValue(), 'Broker account type value');
        $this->assertIsString($account->getBrokerAccountId(), 'Broker account ID');
        static::$broker_account_id = $account->getBrokerAccountId();
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testSandboxClear()
    {
        $status = $this->fixture->sandboxClear();
        $this->assertEquals(ResponseStatus::OK, $status->getValue());
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testSandboxCurrencyBalance()
    {
        $status = $this->fixture->sandboxCurrencyBalance(5000000, Currency::getCurrency(static::TEST_CURRENCY));
        $this->assertEquals(ResponseStatus::OK, $status->getValue());
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testSandboxPositionBalance()
    {
        $status = $this->fixture->sandboxPositionBalance(100, static::TEST_FIGI);
        $this->assertEquals(ResponseStatus::OK, $status->getValue());
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetStocks()
    {
        $stocks = $this->fixture->getStocks();
        $this->assertGreaterThanOrEqual(1, count($stocks), 'Stocks empty');
        $this->assertContainsOnlyInstancesOf(Instrument::class, $stocks, 'Stock instrument');

        $stocks = $this->fixture->getStocks([static::TEST_STOCK_TICKER]);
        $this->assertCount(1, $stocks, 'Stock empty');
        $this->assertInstanceOf(Instrument::class, $stocks[0]);
        $this->assertInstanceOf(Currency::class, $stocks[0]->getCurrency(), 'Stock instrument parameter `currency`');
        $this->assertIsString($stocks[0]->getFigi(), 'Stock instrument parameter `figi`');
        $this->assertIsString($stocks[0]->getIsin(), 'Stock instrument parameter `isin`');
        $this->assertIsInt($stocks[0]->getLot(), 'Stock instrument parameter `lot`');
        $this->assertIsFloat($stocks[0]->getMinPriceIncrement(), 'Stock instrument parameter `min price`');
        $this->assertIsInt($stocks[0]->getMinQuantity(), 'Stock instrument parameter `min quantity`');
        $this->assertIsString($stocks[0]->getName(), 'Stock instrument parameter `name`');
        $this->assertIsString($stocks[0]->getTicker(), 'Stock instrument parameter `ticker`');
        $this->assertInstanceOf(InstrumentType::class, $stocks[0]->getInstrumentType(), 'Stock instrument parameter `type`');
        $this->assertIsString($stocks[0]->getInstrumentType()->getValue(), 'Stock instrument parameter `type value`');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetBonds()
    {
        $bonds = $this->fixture->getBonds();
        $this->assertGreaterThan(1, count($bonds), 'Bonds empty');
        $this->assertContainsOnlyInstancesOf(Instrument::class, $bonds, 'Bond instrument');

        $bonds = $this->fixture->getBonds([static::TEST_BOND_TICKER]);
        $this->assertCount(1, $bonds, 'Bond empty');
        $this->assertInstanceOf(Instrument::class, $bonds[0], 'Bond instrument');
        $this->assertInstanceOf(Currency::class, $bonds[0]->getCurrency(), 'Bond instrument parameter `currency`');
        $this->assertIsString($bonds[0]->getFigi(), 'Bond instrument parameter `figi`');
        $this->assertIsString($bonds[0]->getIsin(), 'Bond instrument parameter `isin`');
        $this->assertIsInt($bonds[0]->getLot(), 'Bond instrument parameter `lot`');
        $this->assertIsFloat($bonds[0]->getMinPriceIncrement(), 'Bond instrument parameter `min price`');
        $this->assertIsInt($bonds[0]->getMinQuantity(), 'Bond instrument parameter `min quantity`');
        $this->assertIsString($bonds[0]->getName(), 'Bond instrument parameter `name`');
        $this->assertIsString($bonds[0]->getTicker(), 'Bond instrument parameter `ticker`');
        $this->assertInstanceOf(InstrumentType::class, $bonds[0]->getInstrumentType(), 'Bond instrument parameter `type`');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetEtfs()
    {
        $etfs = $this->fixture->getEtfs();
        $this->assertGreaterThan(1, count($etfs), 'ETFs empty');
        $this->assertContainsOnlyInstancesOf(Instrument::class, $etfs, 'ETFs instrument');

        $etfs = $this->fixture->getEtfs([static::TEST_ETF_TICKER]);
        $this->assertCount(1, $etfs, 'ETF empty');
        $this->assertInstanceOf(Instrument::class, $etfs[0], 'ETF instrument');
        $this->assertInstanceOf(Currency::class, $etfs[0]->getCurrency(), 'ETF instrument parameter `currency`');
        $this->assertIsString($etfs[0]->getFigi(), 'ETF instrument parameter `figi`');
        $this->assertIsString($etfs[0]->getIsin(), 'ETF instrument parameter `isin`');
        $this->assertIsInt($etfs[0]->getLot(), 'ETF instrument parameter `lot`');
        $this->assertIsFloat($etfs[0]->getMinPriceIncrement(), 'ETF instrument parameter `min price`');
        $this->assertIsInt($etfs[0]->getMinQuantity(), 'ETF instrument parameter `min quantity`');
        $this->assertIsString($etfs[0]->getName(), 'ETF instrument parameter `name`');
        $this->assertIsString($etfs[0]->getTicker(), 'ETF instrument parameter `ticker`');
        $this->assertInstanceOf(InstrumentType::class, $etfs[0]->getInstrumentType(), 'ETF instrument parameter `type`');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetCurrencies()
    {
        $currencies = $this->fixture->getCurrencies();
        $this->assertGreaterThan(1, count($currencies), 'Currencies empty');
        $this->assertContainsOnlyInstancesOf(Instrument::class, $currencies, 'Currencies instrument');

        $currencies = $this->fixture->getCurrencies([static::TEST_CURRENCY_TICKER]);
        $this->assertCount(1, $currencies, 'Currency empty');
        $this->assertInstanceOf(Instrument::class, $currencies[0], 'Currency instrument');
        $this->assertInstanceOf(Currency::class, $currencies[0]->getCurrency(), 'Currency instrument parameter `currency`');
        $this->assertIsString($currencies[0]->getFigi(), 'Currency instrument parameter `figi`');
        $this->assertIsString($currencies[0]->getIsin(), 'Currency instrument parameter `isin`');
        $this->assertIsInt($currencies[0]->getLot(), 'Currency instrument parameter `lot`');
        $this->assertIsFloat($currencies[0]->getMinPriceIncrement(), 'Currency instrument parameter `min price`');
        $this->assertIsInt($currencies[0]->getMinQuantity(), 'Currency instrument parameter `min quantity`');
        $this->assertIsString($currencies[0]->getName(), 'Currency instrument parameter `name`');
        $this->assertIsString($currencies[0]->getTicker(), 'Currency instrument parameter `ticker`');
        $this->assertInstanceOf(InstrumentType::class, $currencies[0]->getInstrumentType(), 'Currency instrument parameter `type`');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetInstrumentByTicker()
    {
        $instrument = $this->fixture->getInstrumentByTicker(static::TEST_STOCK_TICKER);
        $this->assertInstanceOf(Instrument::class, $instrument);
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetInstrumentByFigi()
    {
        $instrument = $this->fixture->getInstrumentByFigi(static::TEST_FIGI);
        $this->assertInstanceOf(Instrument::class, $instrument);
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetCandle()
    {
        $candle = $this->fixture->getCandle(static::TEST_FIGI, CandleInterval::getInterval(CandleInterval::MIN15));
        $this->assertInstanceOf(Candle::class, $candle);
        $this->assertIsFloat($candle->getOpen(), 'Candle parameter `open`');
        $this->assertIsFloat($candle->getClose(), 'Candle parameter `close`');
        $this->assertIsFloat($candle->getHigh(), 'Candle parameter `high`');
        $this->assertIsFloat($candle->getLow(), 'Candle parameter `low`');
        $this->assertIsInt($candle->getVolume(), 'Candle parameter `volume`');
        $this->assertInstanceOf(Carbon::class, $candle->getTime(), 'Candle parameter `time`');
        $this->assertInstanceOf(CandleInterval::class, $candle->getInterval(), 'Candle parameter `interval`');
        $this->assertIsString($candle->getFigi(), 'Candle parameter `figi`');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetHistoryCandles()
    {
        $candles = $this->fixture->getHistoryCandles(static::TEST_FIGI, CandleInterval::getInterval(CandleInterval::HOUR));
        $this->assertGreaterThanOrEqual(1, count($candles), 'Candles empty');
        $this->assertContainsOnlyInstancesOf(Candle::class, $candles, 'Candles');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetInstrumentInfo()
    {
        $instrument_info = $this->fixture->getInstrumentInfo(static::TEST_FIGI);
        $this->assertInstanceOf(InstrumentInfo::class, $instrument_info);
        $this->assertIsFloat($instrument_info->getAccruedInterest(), 'Instrument info parameter `accrued interest`');
        $this->assertIsString($instrument_info->getFigi(), 'Instrument info parameter `figi`');
        $this->assertIsFloat($instrument_info->getLimitDown(), 'Instrument info parameter `limit down`');
        $this->assertIsFloat($instrument_info->getLimitUp(), 'Instrument info parameter `limit up`');
        $this->assertIsInt($instrument_info->getLot(), 'Instrument info parameter `lot`');
        $this->assertIsFloat($instrument_info->getMinPriceIncrement(), 'Instrument info parameter `min price increment`');
        $this->assertInstanceOf(TradeStatus::class, $instrument_info->getTradeStatus(), 'Instrument info parameter `trade status`');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetOrderbook()
    {
        // freezes without change
        if (!$this->fixture->isSandbox()) {
            $orderbook = $this->fixture->getOrderbook(static::TEST_FIGI);
            $this->assertInstanceOf(Orderbook::class, $orderbook);
        }
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testSubscription()
    {
        // freezes without change
        if (!$this->fixture->isSandbox()) {
            $this->fixture->subscribeGettingCandle(static::TEST_FIGI, CandleInterval::getInterval(CandleInterval::MIN1));
            $this->fixture->startGetting(static function ($obj) {
                echo 'action' . PHP_EOL;
                if ($obj instanceof Candle) {
                    echo 'Time: ' . $obj->getTime()->format('d.m.Y H:i:s') . ' Volume: ' . $obj->getVolume() . PHP_EOL;
                }
            }, 2, 2);
            $this->fixture->stopGetting();
            $this->fixture->unsubscribeGettingCandle(static::TEST_FIGI, CandleInterval::getInterval(CandleInterval::MIN1));
        }
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetPortfolio()
    {
        $portfolio = $this->fixture->getPortfolio();
        $this->assertInstanceOf(Portfolio::class, $portfolio, 'Portfolio');

        $this->assertIsFloat($portfolio->getCurrencyBalance(Currency::getCurrency(static::TEST_CURRENCY)), 'Portfolio currency balance');
        $this->assertIsInt($portfolio->getInstrumentLot(static::TEST_STOCK_TICKER), 'Portfolio stock lots');
        $this->assertIsInt($portfolio->getInstrumentLot(static::TEST_ETF_TICKER), 'Portfolio ETF lots');

        $portfolio_currencies = $portfolio->getAllCurrencies();
        $this->assertIsArray($portfolio_currencies, 'Portfolio currencies');
        $this->assertContainsOnlyInstancesOf(PortfolioCurrency::class, $portfolio_currencies, 'Portfolio currency');
        if ($portfolio_currencies) {
            $this->assertInstanceOf(Currency::class, $portfolio_currencies[0]->getCurrency(), 'Portfolio currency parameter `currency`');
            $this->assertIsFloat($portfolio_currencies[0]->getBalance(), 'Portfolio currency parameter `balance`');
            $this->assertIsFloat($portfolio_currencies[0]->getBlocked(), 'Portfolio currency parameter `blocked`');
        }
        $portfolio_instruments = $portfolio->getAllInstruments();
        $this->assertIsArray($portfolio_instruments);
        $this->assertContainsOnlyInstancesOf(PortfolioInstrument::class, $portfolio_instruments, 'Portfolio instrument');
        if ($portfolio_instruments) {
            $this->assertIsString($portfolio_instruments[0]->getFigi(), 'Portfolio instrument parameter `figi`');
            $this->assertIsString($portfolio_instruments[0]->getTicker(), 'Portfolio instrument parameter `ticker`');
            $this->assertIsString($portfolio_instruments[0]->getIsin(), 'Portfolio instrument parameter `isin`');
            $this->assertInstanceOf(InstrumentType::class, $portfolio_instruments[0]->getInstrumentType(), 'Portfolio instrument parameter `type`');
            $this->assertIsFloat($portfolio_instruments[0]->getBalance(), 'Portfolio instrument parameter `balance`');
            $this->assertIsFloat($portfolio_instruments[0]->getBlocked(), 'Portfolio instrument parameter `blocked`');
            $this->assertIsInt($portfolio_instruments[0]->getLot(), 'Portfolio instrument parameter `lot`');
            $this->assertInstanceOf(Currency::class, $portfolio_instruments[0]->getExpectedYieldCurrency(), 'Portfolio instrument parameter `expected yield currency`');
            $this->assertIsFloat($portfolio_instruments[0]->getExpectedYieldValue(), 'Portfolio instrument parameter `expected yield value`');
            $this->assertIsString($portfolio_instruments[0]->getName(), 'Portfolio instrument parameter `name`');
            $this->assertInstanceOf(Currency::class, $portfolio_instruments[0]->getAveragePositionCurrency(), 'Portfolio instrument parameter `average position currency`');
            $this->assertIsFloat($portfolio_instruments[0]->getAveragePositionPrice(), 'Portfolio instrument parameter `average position price`');
            $this->assertIsFloat($portfolio_instruments[0]->getAveragePositionPriceNoNkd(), 'Portfolio instrument parameter `average position price no NKD`');
        }
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testSendOrder()
    {
        $this->setBrokerAccount();
        $this->fixture->sandboxCurrencyBalance(5000000, Currency::getCurrency(static::TEST_CURRENCY));

        $order = $this->fixture->sendOrder(static::TEST_FIGI, 11, OperationType::getOperation(OperationType::BUY), 100);
        $this->assertInstanceOf(Order::class, $order, 'Order');
        $this->assertIsString($order->getOrderId(), 'Order parameter `order ID`');
        $this->assertInstanceOf(OperationType::class, $order->getOperationType(), 'Order parameter `operation`');
        $this->assertEquals(OrderStatus::FILL, $order->getStatus()->getValue());
        $this->assertIsString($order->getRejectReason(), 'Order parameter `reject reason`');
        $this->assertIsInt($order->getRequestedLots(), 'Order parameter `requested lots`');
        $this->assertIsInt($order->getExecutedLots(), 'Order parameter `executed lots`');
        $this->assertInstanceOf(Commission::class, $order->getCommission(), 'Order parameter `commission`');
        $this->assertInstanceOf(Currency::class, $order->getCommission()->getCurrency(), 'Commission parameter `currency`');
        $this->assertIsFloat($order->getCommission()->getValue(), 'Commission parameter `value`');
        $this->assertIsString($order->getFigi(), 'Order parameter `figi`');
        $this->assertInstanceOf(OrderType::class, $order->getOrderType(), 'Order parameter `order type`');
        $this->assertIsString($order->getOrderType()->getValue(), 'Order parameter `order type value`');
        $this->assertIsString($order->getMessage(), 'Order parameter `message`');
        $this->assertIsFloat($order->getPrice(), 'Order parameter `price`');

        $portfolio = $this->fixture->getPortfolio();
        if ($portfolio_instrument = $portfolio->getInstrumentByTicker(static::TEST_STOCK_TICKER)) {
            $this->assertInstanceOf(PortfolioInstrument::class, $portfolio_instrument, 'Portfolio instrument by ticker');
            $this->assertIsInt($portfolio_instrument->getLot(), 'Portfolio instrument lot');
        }
        $portfolio_instrument = $portfolio->getInstrumentByFigi(static::TEST_FIGI);
        $this->assertInstanceOf(PortfolioInstrument::class, $portfolio_instrument, 'Portfolio instrument by figi');
        $this->assertEquals(11, $portfolio_instrument->getLot(), 'Portfolio instrument lot correct');

        $this->assertIsString($portfolio_instrument->getFigi(), 'Portfolio instrument parameter `figi`');
        $this->assertIsString($portfolio_instrument->getTicker(), 'Portfolio instrument parameter `ticker`');
        $this->assertIsString($portfolio_instrument->getIsin(), 'Portfolio instrument parameter `isin`');
        $this->assertInstanceOf(InstrumentType::class, $portfolio_instrument->getInstrumentType(), 'Portfolio instrument type');
        $this->assertIsFloat($portfolio_instrument->getBalance(), 'Portfolio instrument parameter `balance`');
        $this->assertIsFloat($portfolio_instrument->getBlocked(), 'Portfolio instrument parameter `blocked`');
        $this->assertIsInt($portfolio_instrument->getLot(), 'Portfolio instrument parameter `lot`');
        $this->assertInstanceOf(Currency::class, $portfolio_instrument->getExpectedYieldCurrency(), 'Portfolio instrument parameter `expected yield currency`');
        $this->assertIsFloat($portfolio_instrument->getExpectedYieldValue(), 'Portfolio instrument parameter `expected yield value`');
        $this->assertIsString($portfolio_instrument->getName(), 'Portfolio instrument parameter `name`');
        $this->assertInstanceOf(Currency::class, $portfolio_instrument->getAveragePositionCurrency(), 'Portfolio instrument parameter `average position currency`');
        $this->assertIsFloat($portfolio_instrument->getAveragePositionPrice(), 'Portfolio instrument parameter `average position price`');
        $this->assertIsFloat($portfolio_instrument->getAveragePositionPriceNoNkd(), 'Portfolio instrument parameter `average position price no nkd`');

        $orders1 = $this->fixture->getOrders([$order->getOrderId()]);
        if ($orders1 && !$this->fixture->isSandbox()) {
            $status = $this->fixture->cancelOrder($order->getOrderId());
            $this->assertEquals(ResponseStatus::OK, $status->getValue());
            $orders2 = $this->fixture->getOrders([$order->getOrderId()]);
            $this->assertCount(count($orders1) - 1, $orders2);
        }
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetOperations()
    {
        $this->setBrokerAccount();
        $from = Carbon::now()->subDays(7);
        $to = Carbon::now();
        $operations = $this->fixture->getOperations($from, $to, static::TEST_FIGI);
        if ($operations) {
            $this->assertGreaterThanOrEqual(1, count($operations));
            $this->assertIsString($operations[0]->getId(), 'Operation parameter `ID`');
            $this->assertInstanceOf(OperationStatus::class, $operations[0]->getStatus(), 'Operation parameter `status`');
            $this->assertIsString($operations[0]->getStatus()->getValue(), 'Operation parameter `status value`');
            $this->assertInstanceOf(Commission::class, $operations[0]->getCommission(), 'Operation parameter `commission`');
            $this->assertInstanceOf(Currency::class, $operations[0]->getCurrency(), 'Operation parameter `currency`');
            $this->assertIsArray($operations[0]->getTrades(), 'getBestPricesToBuy');
            $this->assertContainsOnlyInstancesOf(OperationTrade::class, $operations[0]->getTrades());
            if ($operations[0]->getTrades()) {
                $this->assertGreaterThanOrEqual(1, count($operations[0]->getTrades()));
                $operation_trade = $operations[0]->getTrades()[0];
                $this->assertIsString($operation_trade->getTradeId(), 'Operation trade parameter `ID`');
                $this->assertInstanceOf(Carbon::class, $operation_trade->getDate(), 'Operation trade parameter `date`');
                $this->assertIsFloat($operation_trade->getPrice(), 'Operation trade parameter `price`');
                $this->assertIsInt($operation_trade->getQuantity(), 'Operation trade parameter `quantity`');
            }
            $this->assertIsFloat($operations[0]->getPayment(), 'Operation parameter `payment`');
            $this->assertIsFloat($operations[0]->getPrice(), 'Operation parameter `price`');
            $this->assertIsInt($operations[0]->getQuantity(), 'Operation parameter `quantity`');
            $this->assertIsString($operations[0]->getFigi(), 'Operation parameter `FIGI`');
            $this->assertInstanceOf(InstrumentType::class, $operations[0]->getInstrumentType(), 'Operation parameter `instrument type`');
            $this->assertIsBool($operations[0]->getIsMarginCall(), 'Operation parameter `is margin call`');
            $this->assertInstanceOf(Carbon::class, $operations[0]->getDate(), 'Operation parameter `date`');
            $this->assertInstanceOf(OperationType::class, $operations[0]->getOperationType(), 'Operation parameter `operation type`');
        }
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetBestPriceToBuy()
    {
        $this->setBrokerAccount();
        $orderbook = $this->fixture->getHistoryOrderbook(static::TEST_FIGI);
        $this->assertIsInt($orderbook->getDepth(), 'Orderbook parameter `depth`');
        $this->assertIsArray($orderbook->getBestPricesToBuy(), 'Orderbook parameter `best prices to buy`');
        $this->assertContainsOnlyInstancesOf(OrderResponse::class, $orderbook->getBestPricesToBuy(), 'Orderbook parameter `best prices to buy`');
        $this->assertIsArray($orderbook->getBestPricesToSell(), 'Orderbook parameter `best prices to sell`');
        $this->assertContainsOnlyInstancesOf(OrderResponse::class, $orderbook->getBestPricesToSell(), 'Orderbook parameter `best prices to sell`');
        $this->assertIsString($orderbook->getFigi(), 'Orderbook parameter `figi`');
        if ($orderbook->getBestPriceToBuy()) {
            $this->assertIsFloat($orderbook->getBestPriceToBuy(), 'Orderbook parameter `best price to buy`');
            $this->assertIsInt($orderbook->getBestPriceToBuyLotCount(), 'Orderbook parameter `best price to buy lot count`');
        }
        if ($orderbook->getBestPriceToSell()) {
            $this->assertIsFloat($orderbook->getBestPriceToSell(), 'Orderbook parameter `best price to sell`');
            $this->assertIsInt($orderbook->getBestPriceToSellLotCount(), 'Orderbook parameter `best price to sell lot count`');
        }
        $this->assertInstanceOf(TradeStatus::class, $orderbook->getTradeStatus(), 'Orderbook parameter `trade status`');
        $this->assertIsString($orderbook->getTradeStatus()->getValue(), 'Orderbook parameter `trade status value`');
        $this->assertIsFloat($orderbook->getMinPriceIncrement(), 'Orderbook parameter `min price increment`');
        $this->assertIsFloat($orderbook->getFaceValue(), 'Orderbook parameter `face value`');
        $this->assertIsFloat($orderbook->getLastPrice(), 'Orderbook parameter `last price`');
        $this->assertIsFloat($orderbook->getClosePrice(), 'Orderbook parameter `close price`');
        $this->assertIsFloat($orderbook->getLimitUp(), 'Orderbook parameter `limit up`');
        $this->assertIsFloat($orderbook->getLimitDown(), 'Orderbook parameter `limit down`');
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testGetAccounts()
    {
        $accounts = $this->fixture->getAccounts();
        $this->assertGreaterThan(1, $accounts);
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function testSandboxRemove()
    {
        $this->setBrokerAccount();
        $this->fixture->sandboxClear();
        $status = $this->fixture->sandboxRemove();
        $this->assertEquals(ResponseStatus::OK, $status->getValue());
        $this->fixture = null;
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    private function setBrokerAccount(): void
    {
        if (!$this->fixture->getBrokerAccount()) {
            if (!static::$broker_account_id) {
                $broker_accounts = $this->fixture->getAccounts();
                $this->assertIsArray($broker_accounts);
                $this->assertGreaterThanOrEqual(1, count($broker_accounts));
                $this->assertContainsOnlyInstancesOf(BrokerAccount::class, $broker_accounts);
                static::$broker_account_id = $broker_accounts[0]->getBrokerAccountId();
            }
            $this->fixture->setBrokerAccount(static::$broker_account_id);
        }
    }

    /**
     * @return void
     */
    protected function setUp(): void
    {
        global $argv;
        $this->assertGreaterThan(2, $argv, 'No sandbox token passed');
        $this->fixture = new Client($argv[2] ?? '', true);
    }

    /**
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    protected function tearDown(): void
    {
        if ($this->fixture) {
            $this->fixture->sandboxClear();
        }
        $this->fixture = null;
    }
}