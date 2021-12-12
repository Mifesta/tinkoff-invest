<?php

namespace TinkoffInvest;

class BrokerAccountType
{
    public const BROKER = 'Tinkoff';
    public const IIS = 'TinkoffIis';
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $account_type
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $account_type)
    {
        $this->value = self::checkAccountTypeValue($account_type);
    }

    /**
     * Get account type value
     * @param string $account_type
     * @return \TinkoffInvest\BrokerAccountType
     * @throws \TinkoffInvest\Exception
     */
    public static function getType(string $account_type): self
    {
        return new self($account_type);
    }

    /**
     * Check account type value
     * @param string $account_type
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkAccountTypeValue(string $account_type): string
    {
        $account_type = strtolower($account_type);
        switch ($account_type) {
            case 'tinkoff' :
                return self::BROKER;
            case 'tinkoffiis' :
            case 'tinkoff_iis' :
                return self::IIS;
            default :
                throw new Exception('Undefined broker account type');
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
