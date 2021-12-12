<?php

namespace TinkoffInvest;

class BrokerAccount
{
    /**
     * @var string
     */
    private string $broker_account_id;
    /**
     * @var \TinkoffInvest\BrokerAccountType
     */
    private BrokerAccountType $broker_account_type;

    /**
     * @param \TinkoffInvest\BrokerAccountType $broker_account_type
     * @param string $broker_account_id
     * @return void
     */
    public function __construct(BrokerAccountType $broker_account_type, string $broker_account_id)
    {
        $this->broker_account_type = $broker_account_type;
        $this->broker_account_id = $broker_account_id;
    }

    /**
     * @return string
     */
    public function getBrokerAccountId(): string
    {
        return $this->broker_account_id;
    }

    /**
     * @return BrokerAccountType
     */
    public function getBrokerAccountType(): BrokerAccountType
    {
        return $this->broker_account_type;
    }
}
