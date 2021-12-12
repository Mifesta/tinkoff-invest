# PHP client for Tinkoff invest API (PHP клиент для API Тинькофф инвестиции)

## How to install
```
composer require mifesta/tinkoff-invest
```
or
add to your compose.json
```json
{
  "require": {
	"mifesta/tinkoff-invest": "*"
  }
}
```
and then
```
composer install
```

## How to get tokens

Create token to use tinkoff invest on [Tinkoff invest setting page](https://www.tinkoff.ru/invest/settings/)

## How to test

```shell
phpunit tests/TinkoffInvestTest.php <YOUR SANDBOX TOKEN>
```

## How to use

### Connection

Create client instance for sandbox 

```php
$client = new \TinkoffInvest\Client('<YOUR SANDBOX TOKEN>', true);
```

or real exchange

```php
$client = new \TinkoffInvest\Client('<YOUR PRODUCTION TOKEN>', false);
```

example:

```php
// sandbox
$client = new \TinkoffInvest\Client('t.cVSIfpICAmapMfOrUDAvJqiqv_2c71Mlh9ET8nUm5op6YbiMYvYVzMCdNJ4Ix6MnD3PHkS6pTFVmTy-yTaEI4N', true);
// production
$client = new \TinkoffInvest\Client('t.aykZTltZxVxOGu8dZLxGd9vvCjPw9xza2qd5hagI4M-uHhF7v92SbLcCeXuoI_o6Ghjnb1xdJx0G1mUfR2EUtD');
```

Get accounts

```php
$accounts = $client->getAccounts(); 
```

Set client broker account

```php
$client->setBrokerAccount('<BROKER ACCOUNT ID>');
```

example:

```php
// sandbox
$client->setBrokerAccount('SB26585218');
// production
$client->setBrokerAccount('2426585218');
```

------------------

### Sandbox

Register broker account on sandbox (sandbox only)

```php
$client->sandboxRegister();
```

Put money to your sandbox account (sandbox only)

```php
$client->sandboxCurrencyBalance(500, \TinkoffInvest\Currency::getCurrency('USD'));
```
or

```php
$client->sandboxCurrencyBalance(500, \TinkoffInvest\Currency::getCurrency(\TinkoffInvest\Currency::USD));
```

Put stocks, bonds, ETFs or currency instrument by FIGI to your sandbox account (sandbox only)

```php
$client->sandboxPositionBalance(10.4, 'BBG000BR37X2');
```

Clear all positions on sandbox (sandbox only)

```php
$client->sandboxClear();
```

Remove account on sandbox (sandbox only)

```php
$client->sandboxRemove();
```

------------------

### Instrument information

Get all stocks, bonds, ETFs or currencies instrument from market

```php
$stocks = $client->getStocks();
$instr = $client->getBonds();
$instr = $client->getEtfs();
$instr = $client->getCurrencies();
```

or with filter

```php
$stocks = $client->getStocks(['SBER','LKOH']);
$instr = $client->getBonds(['RU000A0JX3X7']);
$instr = $client->getEtfs(['FXRU']);
$instr = $client->getCurrencies(['USD000UTSTOM']);
```

Get instrument by ticker

```php
$instr = $client->getInstrumentByTicker('AMZN');
```

or by FIGI

```php
$instr = $client->getInstrumentByFigi('BBG000BR37X2');
```

Get history orderbook
```php
$book = $client->getHistoryOrderbook('BBG000BR37X2', 1); 
```

Get historical candles
```php
$interval = \TinkoffInvest\CandleInterval::getInterval(\TinkoffInvest\CandleInterval::MIN15);
$to = \Carbon\Carbon::now();
$from = $interval->allowableRequestInterval($to);
$candles = $client->getHistoryCandles('BBG000BR37X2', $interval, $to, $from);
```

Getting instrument status

```php
$status = $client->getInstrumentInfo('BBG000BR37X2');
echo 'Instrument status: ' . $status->getTradeStatus()->getValue() . PHP_EOL;
```

Get candles and orderbook

```php
if ($status->getTradeStatus() === 'normal_trading') {
    $candle = $client->getCandle('BBG000BR37X2', CandleInterval::DAY);
    echo 'Low: ' . $candle->getLow() . ' High: ' . $candle->getHigh() . ' Open: ' . $candle->getOpen() . ' Close: ' . $candle->getClose() . ' Volume: ' . $candle->getVolume() . PHP_EOL;
    $orderbook = $client->getOrderbook('BBG000BR37X2', 2);
    echo 'Price to buy: ' . $orderbook->getBestPriceToBuy() . ' Available lots: ' . $orderbook->getBestPriceToBuyLotCount() . ' Price to Sell: ' . $orderbook->getBestPriceToSell() . ' Available lots: ' . $orderbook->getBestPriceToSellLotCount() . PHP_EOL;
}
```

You can also to subscribe on changes orderbook, candles or instrument info:

- make a callback function to manage events:

```php
function action($obj)
{
    echo 'action' . PHP_EOL;
    if ($obj instanceof Candle) {
        echo 'Time: ' . $obj->getTime()->format('d.m.Y H:i:s') . ' Volume: ' . $obj->getVolume() . PHP_EOL;
    }
    if ($obj instanceof Orderbook) {
        echo 'Price to Buy: ' . $obj->getBestPriceToBuy() . ' Price to Sell: ' . $obj->getBestPriceToSell() . PHP_EOL;
    }
}
```

- subscribe to events

```php
$client->subscribeGettingCandle($sber->getFigi(), \TinkoffInvest\CandleInterval::getInterval(\TinkoffInvest\CandleInterval::MIN1));
$client->subscribeGettingOrderbook($sber->getFigi(), 2);
```

- start listening new events

```php
// waiting for a maximum of 20 responses and a maximum of 60 seconds
$client->startGetting('action', 20, 60);
// no limits
$client->startGetting('action');
// waiting maximum of 600 seconds
$client->startGetting('action', null, 600);
// waiting maximum of 1000 responses
$client->startGetting('action', 1000, null);
```

- stop listening

```php
$client->stopGetting();
```

#### CAUTION
If you are using a subscription, you should check FIGI when you reply, because you get all signed instruments in one queue.

------------------

### Portfolio

Get portfolio (if null, used default Tinkoff account) 

```php
$portfolio = $client->getPortfolio();
```

Get portfolio balance

```php
echo $portfolio->getCurrencyBalance(\TinkoffInvest\Currency::getCurrency(\TinkoffInvest\Currency::EUR));
```

Get instrument lots count

```php
echo $portfolio->getinstrumentLot('PGR');
```

------------------

### Orders and operations

Send limit order (default brokerAccountId = Tinkoff)

```php
$order = $client->sendOrder('BBG000BVPV84', 1, OperationType::getOperation('buy'), 1.2);
echo $order->getOrderId();
```

Send market order (default brokerAccountId = Tinkoff)

```php
$order = $client->sendOrder('BBG000BVPV84', 1, OperationType::getOperation('buy'));
echo $order->getOrderId();
```

Get orders

```php
$orders = $client->getOrders();
```

or with filter

```php
$orders = $client->getOrders([$order->getOrderId()]);
```

Cancel order

```php
$client->cancelOrder($order->getOrderId());
```

List of operations from 10 days ago to 30 days period

```php
$from = \Carbon\Carbon::now()->subDays(7);
$to = \Carbon\Carbon::now();
$operations = $client->getOperations($from, $to);
foreach ($operations as $operation) {
    echo $operation->getId() . ' ' . $operation->getFigi() . ' ' . $operation->getPrice() . ' ' . $operation->getOperationType() . ' ' . $operation->getDate()->format('d.m.Y H:i') . PHP_EOL;
}
```

## Licence 
MIT
