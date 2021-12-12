<?php

namespace TinkoffInvest;

class OperationType
{
    public const BROKER_COMMISSION = 'BrokerCommission';
    public const BUY = 'Buy';
    public const BUY_CARD = 'BuyCard';
    public const COUPON = 'Coupon';
    public const DIVIDEND = 'Dividend';
    public const EXCHANGE_COMMISSION = 'ExchangeCommission';
    public const MARGIN_COMMISSION = 'MarginCommission';
    public const OTHER_COMMISSION = 'OtherCommission';
    public const PART_REPAYMENT = 'PartRepayment';
    public const PAY_IN = 'PayIn';
    public const PAY_OUT = 'PayOut';
    public const REPAYMENT = 'Repayment';
    public const SECURITY_IN = 'SecurityIn';
    public const SECURITY_OUT = 'SecurityOut';
    public const SELL = 'Sell';
    public const SERVICE_COMMISSION = 'ServiceCommission';
    public const TAX = 'Tax';
    public const TAX_BACK = 'TaxBack';
    public const TAX_COUPON = 'TaxCoupon';
    public const TAX_DIVIDEND = 'TaxDividend';
    public const TAX_LUCRE = 'TaxLucre';
    /**
     * @var string|null
     */
    private ?string $value;

    /**
     * @param string|null $operation
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(?string $operation)
    {
        $this->value = self::checkOperationValue($operation);
    }

    /**
     * Get operation
     * @param string|null $operation
     * @return \TinkoffInvest\OperationType
     * @throws \TinkoffInvest\Exception
     */
    public static function getOperation(?string $operation): OperationType
    {
        return new self($operation);
    }

    /**
     * Check operation value
     * @param string|null $operation
     * @return string|null
     * @throws \TinkoffInvest\Exception
     */
    private static function checkOperationValue(?string $operation): ?string
    {
        $operation = strtolower($operation);
        switch ($operation) {
            case 'buy' :
                return self::BUY;
            case 'sell' :
                return self::SELL;
            case 'buycard' :
            case 'buy_card' :
                return self::BUY_CARD;
            case 'brokercommission' :
            case 'broker_commission' :
                return self::BROKER_COMMISSION;
            case 'exchangecommission' :
            case 'exchange_commission' :
                return self::EXCHANGE_COMMISSION;
            case 'servicecommission' :
            case 'service_commission' :
                return self::SERVICE_COMMISSION;
            case 'margincommission' :
            case 'margin_commission' :
                return self::MARGIN_COMMISSION;
            case 'othercommission' :
            case 'other_commission' :
                return self::OTHER_COMMISSION;
            case 'payin' :
            case 'pay_in' :
                return self::PAY_IN;
            case 'payout' :
            case 'pay_out' :
                return self::PAY_OUT;
            case 'tax' :
                return self::TAX;
            case 'taxlucre' :
            case 'tax_lucre' :
                return self::TAX_LUCRE;
            case 'taxdividend' :
            case 'tax_dividend' :
                return self::TAX_DIVIDEND;
            case 'taxcoupon' :
            case 'tax_coupon' :
                return self::TAX_COUPON;
            case 'taxback' :
            case 'tax_back' :
                return self::TAX_BACK;
            case 'repayment' :
                return self::REPAYMENT;
            case 'partrepayment' :
            case 'part_repayment' :
                return self::PART_REPAYMENT;
            case 'coupon' :
                return self::COUPON;
            case 'dividend' :
                return self::DIVIDEND;
            case 'securityin' :
            case 'security_in' :
                return self::SECURITY_IN;
            case 'securityout' :
            case 'security_out' :
                return self::SECURITY_OUT;
            case null:
                return null;
            default :
                throw new Exception('Undefined operation');
        }
    }

    /**
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }
}
