<?php

namespace TinkoffInvest;

use Carbon\Carbon;
use GuzzleHttp\Client as HTTPClient;
use stdClass;
use Throwable;
use WebSocket\BadOpcodeException;
use WebSocket\Client as WSClient;

class Client
{
    private const EXCHANGE = 'https://api-invest.tinkoff.ru/openapi';
    private const SANDBOX = 'https://api-invest.tinkoff.ru/openapi/sandbox';
    /**
     * @var string|null
     */
    private ?string $broker_account_id;
    /**
     * @var \GuzzleHttp\Client|null
     */
    private ?HTTPClient $http_client = null;
    /**
     * @var bool
     */
    private bool $is_sandbox;
    /**
     * @var bool
     */
    private bool $startGetting = false;
    /**
     * @var string
     */
    private string $token;
    /**
     * @var string
     */
    private string $url;
    /**
     * @var \WebSocket\Client|null
     */
    private ?WSClient $ws_client = null;

    /**
     * @param string $token token from tinkoff.ru for specific site
     * @param bool $is_sandbox is sandbox or real exchange
     * @param int|null $broker_account_id
     */
    public function __construct(string $token, bool $is_sandbox = false, ?int $broker_account_id = null)
    {
        $this->token = $token;
        $this->is_sandbox = $is_sandbox;
        $this->url = self::getAddress($is_sandbox);
        $this->broker_account_id = $broker_account_id;
    }

    /**
     * Get website address
     * @param bool $is_sandbox
     * @return string
     */
    private static function getAddress(bool $is_sandbox = false): string
    {
        if ($is_sandbox) {
            return self::SANDBOX;
        }
        return self::EXCHANGE;
    }

    /**
     * Cancel order
     * @param string $orderId Order ID
     * @return \TinkoffInvest\ResponseStatus
     * @throws \TinkoffInvest\Exception
     */
    public function cancelOrder(string $orderId): ResponseStatus
    {
        $response = $this->sendRequest('/orders/cancel', 'POST', [
            'orderId' => $orderId,
            'brokerAccountId' => $this->getBrokerAccount(),
        ]);
        return $response->getStatus();
    }

    /**
     * Getting current user accounts
     * @return \TinkoffInvest\BrokerAccount[]
     * @throws \TinkoffInvest\Exception
     */
    public function getAccounts(): array
    {
        $response = $this->sendRequest('/user/accounts', 'GET');
        $accounts = [];
        foreach ($response->getPayload()->accounts as $account) {
            $accounts [] = new BrokerAccount(BrokerAccountType::getType($account->brokerAccountType), $account->brokerAccountId);
        }
        return $accounts;
    }

    /**
     * @param string[]|null $tickers
     * @return \TinkoffInvest\Instrument[]
     * @throws \TinkoffInvest\Exception
     */
    public function getBonds(?array $tickers = null): array
    {
        return $this->setUpLists($this->sendRequest('/market/bonds', 'GET'), $tickers);
    }

    /**
     * @return string|null
     */
    public function getBrokerAccount(): ?string
    {
        return $this->broker_account_id;
    }

    /**
     * @param string $figi
     * @param \TinkoffInvest\CandleInterval $interval
     * @return \TinkoffInvest\Candle
     * @throws \TinkoffInvest\Exception
     */
    public function getCandle(string $figi, CandleInterval $interval): Candle
    {
        $this->subscribeGettingCandle($figi, $interval);
        $response = $this->ws_client->receive();
        $this->unsubscribeGettingCandle($figi, $interval);
        if ($json = json_decode($response)) {
            return $this->setUpCandle($json->payload);
        }
        throw new Exception('Got empty response for candle');
    }

    /**
     * @param string $figi
     * @return \TinkoffInvest\InstrumentInfo
     * @throws \TinkoffInvest\Exception
     */
    public function getInstrumentInfo(string $figi): InstrumentInfo
    {
        $this->subscribeGettingInstrumentInfo($figi);
        $response = $this->ws_client->receive();
        $this->unsubscribeGettingInstrumentInfo($figi);
        if ($json = json_decode($response)) {
            return $this->setUpInstrumentInfo($json->payload);
        }
        throw new Exception('Got empty response for instrument info');
    }

    /**
     * @param string[]|null $tickers
     * @return \TinkoffInvest\Instrument[]
     * @throws \TinkoffInvest\Exception
     */
    public function getCurrencies(?array $tickers = null): array
    {
        return $this->setUpLists($this->sendRequest('/market/currencies', 'GET'), $tickers);
    }

    /**
     * @param string[]|null $tickers
     * @return \TinkoffInvest\Instrument[]
     * @throws \TinkoffInvest\Exception
     */
    public function getEtfs(?array $tickers = null): array
    {
        return $this->setUpLists($this->sendRequest('/market/etfs', 'GET'), $tickers);
    }

    /**
     * Getting candles history
     * @param string $figi
     * @param \TinkoffInvest\CandleInterval $interval
     * @param \Carbon\Carbon|null $to default to now
     * @param \Carbon\Carbon|null $from default allowable request interval
     * @return \TinkoffInvest\Candle[]
     * @throws \TinkoffInvest\Exception
     */
    public function getHistoryCandles(string $figi, CandleInterval $interval, ?Carbon $to = null, ?Carbon $from = null): array
    {
        $to = $to ?? Carbon::now();
        $allowable_from = $interval->allowableRequestInterval($to);
        if (!$from) {
            $from = $allowable_from;
        } elseif ($from->lt($allowable_from)) {
            throw new Exception('Invalid allowable request interval');
        }
        $response = $this->sendRequest('/market/candles', 'GET', [
            'figi' => $figi,
            'from' => $from->format('c'),
            'to' => $to->format('c'),
            'interval' => $interval->getValue(),
        ]);
        $array = [];
        foreach ($response->getPayload()->candles as $candle) {
            $array[] = $this->setUpCandle($candle);
        }
        return $array;
    }

    /**
     * Getting orderbook history
     * @param string $figi
     * @param int $depth
     * @return \TinkoffInvest\Orderbook
     * @throws \TinkoffInvest\Exception
     */
    public function getHistoryOrderbook(string $figi, int $depth = 1): Orderbook
    {
        if ($depth < 1) {
            $depth = 1;
        }
        if ($depth > 20) {
            $depth = 20;
        }
        $response = $this->sendRequest('/market/orderbook', 'GET', ['figi' => $figi, 'depth' => $depth]);
        return $this->setUpOrderbook($response->getPayload());
    }

    /**
     * @param string $figi
     * @return \TinkoffInvest\Instrument
     * @throws \TinkoffInvest\Exception
     */
    public function getInstrumentByFigi(string $figi): Instrument
    {
        $response = $this->sendRequest('/market/search/by-figi', 'GET', ['figi' => $figi]);
        return new Instrument(
            $response->getPayload()->figi,
            $response->getPayload()->ticker,
            $response->getPayload()->isin ?? null,
            $response->getPayload()->minPriceIncrement ?? null,
            $response->getPayload()->lot,
            $response->getPayload()->minQuantity ?? null,
            Currency::getCurrency($response->getPayload()->currency),
            $response->getPayload()->name,
            InstrumentType::getType($response->getPayload()->type)
        );
    }

    /**
     * @param string $ticker
     * @return \TinkoffInvest\Instrument
     * @throws \TinkoffInvest\Exception
     */
    public function getInstrumentByTicker(string $ticker): Instrument
    {
        $response = $this->sendRequest('/market/search/by-ticker', 'GET', ['ticker' => $ticker]);
        if ($response->getPayload()->total === 0) {
            throw new Exception('Cannot find instrument by ticker ' . $ticker);
        }
        return new Instrument(
            $response->getPayload()->instruments[0]->figi,
            $response->getPayload()->instruments[0]->ticker,
            $response->getPayload()->instruments[0]->isin ?? null,
            $response->getPayload()->instruments[0]->minPriceIncrement ?? null,
            $response->getPayload()->instruments[0]->lot,
            $response->getPayload()->instruments[0]->minQuantity ?? null,
            Currency::getCurrency($response->getPayload()->instruments[0]->currency),
            $response->getPayload()->instruments[0]->name,
            InstrumentType::getType($response->getPayload()->instruments[0]->type)
        );
    }

    /**
     * @param \Carbon\Carbon $from
     * @param \Carbon\Carbon $to
     * @param string|null $figi
     * @return \TinkoffInvest\Operation[]
     * @throws \TinkoffInvest\Exception
     */
    public function getOperations(Carbon $from, Carbon $to, ?string $figi = null): array
    {
        $operations = [];
        $request = [
            'from' => $from->format('c'),
            'to' => $to->format('c'),
            'brokerAccountId' => $this->getBrokerAccount(),
        ];
        if ($figi) {
            $request['figi'] = $figi;
        }
        $response = $this->sendRequest('/operations', 'GET', $request);
        foreach ($response->getPayload()->operations as $entry_operation) {
            $trades = [];
            foreach ($entry_operation->trades ?? [] as $entry_operation_trade) {
                $trades[] = new OperationTrade(
                    $entry_operation_trade->tradeId,
                    $this->getCarbon($entry_operation_trade->date),
                    $entry_operation_trade->price,
                    $entry_operation_trade->quantity
                );
            }
            if (isset($entry_operation->commission)) {
                $entry_operation_commission_currency = Currency::getCurrency($entry_operation->commission->currency);
                $entry_operation_commission_value = $entry_operation->commission->value;
            } else {
                $entry_operation_commission_currency = null;
                $entry_operation_commission_value = null;
            }
            $operations[] = new Operation(
                $entry_operation->id,
                OperationStatus::getStatus($entry_operation->status),
                $trades,
                new Commission($entry_operation_commission_currency, $entry_operation_commission_value),
                Currency::getCurrency($entry_operation->currency),
                $entry_operation->payment,
                $entry_operation->price,
                $entry_operation->quantity ?? 0,
                $entry_operation->figi,
                InstrumentType::getType($entry_operation->instrumentType),
                $entry_operation->isMarginCall,
                $this->getCarbon($entry_operation->date),
                OperationType::getOperation($entry_operation->operationType ?? null)
            );
        }
        return $operations;
    }

    /**
     * @param string $figi
     * @param int $depth
     * @return \TinkoffInvest\Orderbook
     * @throws \TinkoffInvest\Exception
     */
    public function getOrderbook(string $figi, int $depth = 1): Orderbook
    {
        if ($depth < 1) {
            $depth = 1;
        }
        if ($depth > 20) {
            $depth = 20;
        }
        $this->subscribeGettingOrderbook($figi, $depth);
        $response = $this->ws_client->receive();
        $this->unsubscribeGettingOrderbook($figi, $depth);
        if ($json = json_decode($response)) {
            return $this->setUpOrderbook($json->payload);
        }
        throw new Exception('Got empty response for orderbook');
    }

    /**
     * @param string[]|null $order_ids
     * @return \TinkoffInvest\Order[]
     * @throws \TinkoffInvest\Exception
     */
    public function getOrders(?array $order_ids = null): array
    {
        $orders = [];
        $response = $this->sendRequest('/orders', 'GET');
        foreach ($response->getPayload() as $order) {
            if ($order_ids === null || in_array($order->orderId, $order_ids, true)) {
                $orders[] = new Order(
                    $order->orderId,
                    OperationType::getOperation($order->operation),
                    OrderStatus::getStatus($order->status),
                    '',
                    $order->requestedLots,
                    $order->executedLots,
                    new Commission(null, null),
                    $order->figi,
                    OrderType::getType($order->type),
                    '',
                    $order->price
                );
            }
        }
        return $orders;
    }

    /**
     * Get client portfolio
     * @return \TinkoffInvest\Portfolio
     * @throws \TinkoffInvest\Exception
     */
    public function getPortfolio(): Portfolio
    {
        $portfolio_currencies = [];
        $params = [
            'brokerAccountId' => $this->getBrokerAccount(),
        ];

        $response = $this->sendRequest('/portfolio/currencies', 'GET', $params);

        foreach ($response->getPayload()->currencies as $portfolio_currency) {
            $portfolio_currencies[] = new PortfolioCurrency(
                $portfolio_currency->balance,
                $portfolio_currency->blocked ?? .0,
                Currency::getCurrency($portfolio_currency->currency)
            );
        }

        $portfolio_instruments = [];
        $response = $this->sendRequest('/portfolio', 'GET', $params);

        foreach ($response->getPayload()->positions as $position) {
            if (isset($position->expectedYield)) {
                $expected_yield_currency = Currency::getCurrency($position->expectedYield->currency);
                $expected_yield_value = $position->expectedYield->value;
            } else {
                $expected_yield_currency = null;
                $expected_yield_value = null;
            }
            if (isset($position->averagePositionPrice)) {
                $average_position_currency = Currency::getCurrency($position->averagePositionPrice->currency);
                $average_position_price = $position->averagePositionPrice->value;
            } else {
                $average_position_currency = null;
                $average_position_price = null;
            }
            if (isset($position->averagePositionPriceNoNkd)) {
                $average_position_currency = Currency::getCurrency($position->averagePositionPriceNoNkd->currency);
                $average_position_price_no_nkd = $position->averagePositionPriceNoNkd->value;
            } else {
                $average_position_price_no_nkd = null;
            }
            $portfolio_instruments[] = new PortfolioInstrument(
                $position->figi,
                $position->ticker,
                $position->isin ?? null,
                InstrumentType::getType($position->instrumentType),
                $position->balance,
                $position->blocked ?? null,
                $position->lots,
                $expected_yield_currency,
                $expected_yield_value,
                $position->name,
                $average_position_currency,
                $average_position_price,
                $average_position_price_no_nkd
            );
        }

        return new Portfolio($portfolio_currencies, $portfolio_instruments);
    }

    /**
     * @param string[]|null $tickers
     * @return \TinkoffInvest\Instrument[]
     * @throws \TinkoffInvest\Exception
     */
    public function getStocks(?array $tickers = null): array
    {
        return $this->setUpLists($this->sendRequest('/market/stocks', 'GET'), $tickers);
    }

    /**
     * Removing all positions in the sandbox
     * @return \TinkoffInvest\ResponseStatus
     * @throws \TinkoffInvest\Exception
     */
    public function sandboxClear(): ResponseStatus
    {
        $response = $this->sendRequest('/sandbox/clear', 'POST', [
            'brokerAccountId' => $this->getBrokerAccount(),
        ]);
        return $response->getStatus();
    }

    /**
     * Set currency balance on sandbox
     * @param float $balance
     * @param \TinkoffInvest\Currency $currency
     * @return \TinkoffInvest\ResponseStatus
     * @throws \TinkoffInvest\Exception
     */
    public function sandboxCurrencyBalance(float $balance, Currency $currency): ResponseStatus
    {
        $response = $this->sendRequest('/sandbox/currencies/balance', 'POST', [
            'brokerAccountId' => $this->getBrokerAccount(),
        ], json_encode(['currency' => $currency->getValue(), 'balance' => $balance], JSON_NUMERIC_CHECK));
        return $response->getStatus();
    }

    /**
     * Set instrument positions on sandbox
     * @param float $balance
     * @param string $figi
     * @return \TinkoffInvest\ResponseStatus
     * @throws \TinkoffInvest\Exception
     */
    public function sandboxPositionBalance(float $balance, string $figi): ResponseStatus
    {
        $response = $this->sendRequest('/sandbox/positions/balance', 'POST', [
            'brokerAccountId' => $this->getBrokerAccount(),
        ], json_encode(['figi' => $figi, 'balance' => $balance], JSON_NUMERIC_CHECK));
        return $response->getStatus();
    }

    /**
     * Registering a sandbox account
     * @return \TinkoffInvest\BrokerAccount
     * @throws \TinkoffInvest\Exception
     */
    public function sandboxRegister(): BrokerAccount
    {
        $response = $this->sendRequest('/sandbox/register', 'POST');
        return new BrokerAccount(BrokerAccountType::getType($response->getPayload()->brokerAccountType), $response->getPayload()->brokerAccountId);
    }

    /**
     * Removing a sandbox account
     * @return \TinkoffInvest\ResponseStatus
     * @throws \TinkoffInvest\Exception
     */
    public function sandboxRemove(): ResponseStatus
    {
        $response = $this->sendRequest('/sandbox/remove', 'POST', [
            'brokerAccountId' => $this->getBrokerAccount(),
        ]);
        return $response->getStatus();
    }

    /**
     * Send order
     * @param string $figi
     * @param int $lots
     * @param \TinkoffInvest\OperationType $operation_type
     * @param float|null $price
     * @return \TinkoffInvest\Order
     * @throws \TinkoffInvest\Exception
     */
    public function sendOrder(string $figi, int $lots, OperationType $operation_type, ?float $price = null): Order
    {
        $request_array = [
            'lots' => $lots,
            'operation' => $operation_type->getValue(),
        ];
        if (empty($price)) {
            $order_type = OrderType::getType(OrderType::MARKET);
            $order_type_address = 'market-order';
        } else {
            $order_type = OrderType::getType(OrderType::LIMIT);
            $order_type_address ='limit-order';
            $request_array['price'] = $price;
        }

        $request_body = json_encode($request_array);

        $response = $this->sendRequest('/orders/' . $order_type_address, 'POST', [
            'figi' => $figi,
            'brokerAccountId' => $this->getBrokerAccount(),
        ], $request_body);

        return $this->setUpOrder($response, $figi, $order_type);
    }

    /**
     * @return void
     */
    public function isSandbox(): bool
    {
        return $this->is_sandbox;
    }

    /**
     * @param string|null $broker_account_id
     * @return void
     */
    public function setBrokerAccount(?string $broker_account_id): void
    {
        $this->broker_account_id = $broker_account_id;
    }

    /**
     * @param callable $callback
     * @param int|null $max_response
     * @param int|null $max_time_sec
     * @throws \TinkoffInvest\Exception
     */
    public function startGetting(callable $callback, ?int $max_response = null, ?int $max_time_sec = null)
    {
        $this->startGetting = true;
        $response_now = 0;
        $response_start_time = time();
        echo 0;
        while (true) {
            echo 1;
            $response = $this->ws_client->receive();
            echo 2;
            $json = json_decode($response);
            if (isset($json->event, $json->payload)) {
                echo 3;
                try {
                    switch ($json->event) {
                        case 'candle':
                            $object = $this->setUpCandle($json->payload);
                            break;
                        case 'orderbook':
                            $object = $this->setUpOrderbook($json->payload);
                            break;
                        case 'instrument_info':
                            $object = $this->setUpInstrumentInfo($json->payload);
                            break;
                        default:
                            $object = null;
                    }
                    if ($object !== null) {
                        $callback($object);
                    }
                } catch (Exception $exception) {
                }
                $response_now++;
            }
            echo 4;
            if (($this->startGetting === false) || (($max_response !== null) && ($response_now >= $max_response)) || (($max_time_sec !== null) && (time() > $response_start_time + $max_time_sec))) {
                break;
            }
            echo 5;
        }
    }

    /**
     * @return void
     */
    public function stopGetting(): void
    {
        $this->startGetting = false;
    }

    /**
     * @param string $figi
     * @param \TinkoffInvest\CandleInterval $interval
     * @throws \TinkoffInvest\Exception
     */
    public function subscribeGettingCandle(string $figi, CandleInterval $interval)
    {
        $this->candleSubscription($figi, $interval);
    }

    /**
     * @param string $figi
     * @throws \TinkoffInvest\Exception
     */
    public function subscribeGettingInstrumentInfo(string $figi)
    {
        $this->instrumentInfoSubscription($figi);
    }

    /**
     * @param string $figi
     * @param int $depth
     * @throws \TinkoffInvest\Exception
     */
    public function subscribeGettingOrderbook(string $figi, int $depth)
    {
        $this->orderbookSubscription($figi, $depth);
    }

    /**
     * @param string $figi
     * @param \TinkoffInvest\CandleInterval $interval
     * @throws \TinkoffInvest\Exception
     */
    public function unsubscribeGettingCandle(string $figi, CandleInterval $interval)
    {
        $this->candleSubscription($figi, $interval, false);
    }

    /**
     * @param string $figi
     * @throws \TinkoffInvest\Exception
     */
    public function unsubscribeGettingInstrumentInfo(string $figi)
    {
        $this->instrumentInfoSubscription($figi, false);
    }

    /**
     * @param string $figi
     * @param int $depth
     * @throws \TinkoffInvest\Exception
     */
    public function unsubscribeGettingOrderbook(string $figi, int $depth)
    {
        $this->orderbookSubscription($figi, $depth, false);
    }

    /**
     * @param string $figi
     * @param \TinkoffInvest\CandleInterval $interval
     * @param bool $subscribe
     * @throws \TinkoffInvest\Exception
     */
    private function candleSubscription(string $figi, CandleInterval $interval, bool $subscribe = true)
    {
        $request = [
            'event' => 'candle:' . ($subscribe ? 'subscribe' : 'unsubscribe'),
            'figi' => $figi,
            'interval' => $interval->getValue(),
        ];
        if (!$this->ws_client || !$this->ws_client->isConnected()) {
            $this->wsConnect();
        }
        try {
            $this->ws_client->send(json_encode($request));
        } catch (BadOpcodeException $exception) {
            throw new Exception('Can\'t send websocket request. Message: ' . $exception->getMessage());
        }
    }

    /**
     * @param string $figi
     * @param bool $subscribe
     * @throws \TinkoffInvest\Exception
     */
    private function instrumentInfoSubscription(string $figi, bool $subscribe = true)
    {
        $request = [
            'event' => 'instrument_info:' . ($subscribe ? 'subscribe' : 'unsubscribe'),
            'figi' => $figi,
        ];
        if (!$this->ws_client || !$this->ws_client->isConnected()) {
            $this->wsConnect();
        }
        try {
            $this->ws_client->send(json_encode($request));
        } catch (BadOpcodeException $exception) {
            throw new Exception('Can\'t send websocket request. Message: ' . $exception->getMessage());
        }
    }

    /**
     * @param string $figi
     * @param int $depth
     * @param bool $subscribe
     * @throws \TinkoffInvest\Exception
     */
    private function orderbookSubscription(string $figi, int $depth, bool $subscribe = true)
    {
        $request = [
            'event' => 'orderbook:' . ($subscribe ? 'subscribe' : 'unsubscribe'),
            'figi' => $figi,
            'depth' => $depth,
        ];
        if (!$this->ws_client || !$this->ws_client->isConnected()) {
            $this->wsConnect();
        }
        try {
            $this->ws_client->send(json_encode($request));
        } catch (BadOpcodeException $exception) {
            throw new Exception('Can\'t send websocket request. Message: ' . $exception->getMessage());
        }
    }

    /**
     * Sending an API request
     * @param string $action
     * @param string $method
     * @param array $request_params
     * @param string|null $request_body
     * @return \TinkoffInvest\Response
     * @throws \TinkoffInvest\Exception
     */
    private function sendRequest(string $action, string $method, array $request_params = [], ?string $request_body = null): Response
    {
        if (!$this->http_client) {
            $this->http_client = new HTTPClient([
                'allow_redirects' => true,
                'cookies' => true,
                'http_errors' => false,
                'verify' => false,
            ]);
        }
        $method = strtolower($method);
        $url = $this->url . $action;
        if (count($request_params) > 0) {
            $url .= '?' . http_build_query($request_params);
        }
        $options = [
            'connect_timeout' => 5,
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json; charset=utf-8',
            ],
        ];
        if ($request_body && ($method !== 'get')) {
            $options['body'] = $request_body;
        }
        $response = $this->http_client->$method($url, $options);
        return new Response((string)$response->getBody(), $response->getStatusCode());
    }

    /**
     * @param \stdClass $payload
     * @return \TinkoffInvest\Candle
     * @throws \TinkoffInvest\Exception
     */
    private function setUpCandle(stdClass $payload): Candle
    {
        return new Candle(
            $payload->figi,
            $this->getCarbon($payload->time),
            $payload->o,
            $payload->c,
            $payload->h,
            $payload->l,
            $payload->v,
            CandleInterval::getInterval($payload->interval)
        );
    }

    /**
     * @param \stdClass $payload
     * @return \TinkoffInvest\InstrumentInfo
     * @throws \TinkoffInvest\Exception
     */
    private function setUpInstrumentInfo(stdClass $payload): InstrumentInfo
    {
        $object = new InstrumentInfo(
            TradeStatus::getStatus($payload->trade_status),
            $payload->min_price_increment,
            $payload->lot,
            $payload->figi
        );
        $object->setAccruedInterest($payload->accrued_interest ?? .0);
        $object->setLimitUp($payload->limit_up ?? .0);
        $object->setLimitDown($payload->limit_down ?? .0);
        return $object;
    }

    /**
     * @param \TinkoffInvest\Response $response
     * @param string[] $tickers
     * @return \TinkoffInvest\Instrument[]
     * @throws \TinkoffInvest\Exception
     */
    private function setUpLists(Response $response, ?array $tickers = null): array
    {
        $array = [];
        foreach ($response->getPayload()->instruments as $instrument) {
            if (($tickers === null) || in_array($instrument->ticker, $tickers, true)) {
                $array[] = new Instrument(
                    $instrument->figi,
                    $instrument->ticker,
                    $instrument->isin ?? null,
                    $instrument->minPriceIncrement ?? null,
                    $instrument->lot,
                    $instrument->minQuantity ?? null,
                    Currency::getCurrency($instrument->currency),
                    $instrument->name,
                    InstrumentType::getType($instrument->type)
                );
            }
        }
        return $array;
    }

    /**
     * @param \TinkoffInvest\Response $response
     * @param string $figi
     * @param \TinkoffInvest\OrderType $order_type
     * @return \TinkoffInvest\Order
     * @throws \TinkoffInvest\Exception
     */
    private function setUpOrder(Response $response, string $figi, OrderType $order_type): Order
    {
        $payload = $response->getPayload();
        if (isset($payload->commission)) {
            $commission_value = $payload->commission->value;
            $commission_currency = Currency::getCurrency($payload->commission->currency);
        } else {
            $commission_value = null;
            $commission_currency = null;
        }
        return new Order(
            $payload->orderId,
            OperationType::getOperation($payload->operation),
            OrderStatus::getStatus($payload->status),
            $payload->rejectReason ?? '',
            empty($payload->requestedLots) ? null : $payload->requestedLots,
            empty($payload->executedLots) ? null : $payload->executedLots,
            new Commission($commission_currency, $commission_value),
            $figi,
            $order_type,
            $payload->message ?? '',
            $payload->price ?? .0
        );
    }

    /**
     * @param \stdClass $payload
     * @return \TinkoffInvest\Orderbook
     * @throws \TinkoffInvest\Exception
     */
    private function setUpOrderbook(stdClass $payload): Orderbook
    {
        return new Orderbook(
            $payload->depth,
            array_map(function ($order_response) {
                return new OrderResponse($order_response->price, $order_response->quantity);
            }, $payload->bids),
            array_map(function ($order_response) {
                return new OrderResponse($order_response->price, $order_response->quantity);
            }, $payload->asks),
            $payload->figi,
            TradeStatus::getStatus($payload->tradeStatus),
            $payload->minPriceIncrement,
            $payload->faceValue ?? .0,
            $payload->lastPrice ?? .0,
            $payload->closePrice ?? .0,
            $payload->limitUp ?? .0,
            $payload->limitDown ?? .0
        );
    }

    /**
     * @throws \TinkoffInvest\Exception
     */
    private function wsConnect()
    {
        try {
            $this->ws_client = new WSClient('wss://api-invest.tinkoff.ru/openapi/md/v1/md-openapi/ws', [
                'timeout' => 60,
                'headers' => ['authorization' => 'Bearer ' . $this->token],
            ]);
        } catch (Throwable $e) {
            throw new Exception(
                'Can\'t connect to stream API . ' . $e->getCode() . ' ' . $e->getMessage()
            );
        }
    }

    /**
     * @param string $datetime_string
     * @return \Carbon\Carbon
     * @throws \TinkoffInvest\Exception
     */
    private function getCarbon(string $datetime_string): Carbon
    {
        try {
            return Carbon::parse($datetime_string)->setTimezone('Europe/Moscow');
        } catch (Throwable $throwable) {
            throw new Exception('Can\'t create date from string [' . $datetime_string . ']');
        }
    }
}
