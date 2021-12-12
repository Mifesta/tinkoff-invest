<?php

namespace TinkoffInvest;

class Portfolio
{
    /**
     * @var PortfolioCurrency[]
     */
    private array $currencies;
    /**
     * @var PortfolioInstrument[]
     */
    private array $instruments;

    /**
     * @param PortfolioCurrency[] $currencies
     * @param PortfolioInstrument[] $instruments
     */
    function __construct(array $currencies, array $instruments)
    {
        $this->currencies = $currencies;
        $this->instruments = $instruments;
    }

    /**
     * Get all currencies in portfolio
     * @return PortfolioCurrency[]
     */
    public function getAllCurrencies(): array
    {
        return $this->currencies;
    }

    /**
     * Get all instruments in portfolio
     * @return PortfolioInstrument[]
     */
    public function getAllInstruments(): array
    {
        return $this->instruments;
    }

    /**
     * Get balance of currency
     * @param Currency $currency
     * @return float
     */
    public function getCurrencyBalance(Currency $currency): float
    {
        foreach ($this->currencies as $curr) {
            if ($currency->getValue() === $curr->getCurrency()->getValue())
                return $curr->getBalance();
        }
        return .0;
    }

    /**
     * Get portfolio instrument by figi
     * @param string $figi
     * @return \TinkoffInvest\PortfolioInstrument|null
     */
    public function getInstrumentByFigi(string $figi): ?PortfolioInstrument
    {
        foreach ($this->instruments as $portfolio_instrument) {
            if ($figi === $portfolio_instrument->getFigi())
                return $portfolio_instrument;
        }
        return null;
    }

    /**
     * Get portfolio instrument by ticker
     * @param string $ticker
     * @return \TinkoffInvest\PortfolioInstrument|null
     */
    public function getInstrumentByTicker(string $ticker): ?PortfolioInstrument
    {
        foreach ($this->instruments as $portfolio_instrument) {
            if ($ticker === $portfolio_instrument->getTicker())
                return $portfolio_instrument;
        }
        return null;
    }

    /**
     * Get Lots count of ticker
     * @param string $ticker
     * @return int
     */
    public function getInstrumentLot(string $ticker): int
    {
        foreach ($this->instruments as $portfolio_instrument) {
            if ($ticker === $portfolio_instrument->getTicker())
                return $portfolio_instrument->getLot();
        }
        return 0;
    }
}
