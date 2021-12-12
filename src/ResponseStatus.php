<?php

namespace TinkoffInvest;

class ResponseStatus
{
    public const ERROR = 'Error';
    public const OK = 'Ok';
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $response_status
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $response_status)
    {
        $this->value = self::checkResponseStatusValue($response_status);
    }

    /**
     * Get status value
     * @param string $response_status
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    public static function checkResponseStatusValue(string $response_status): string
    {
        $response_status = strtolower($response_status);
        switch ($response_status) {
            case 'ok':
                return self::OK;
            case 'error':
                return self::ERROR;
            default:
                throw new Exception('Undefined response status');
        }
    }

    /**
     * Get response status value
     * @param string $response_status
     * @return \TinkoffInvest\ResponseStatus
     * @throws \TinkoffInvest\Exception
     */
    public static function getStatus(string $response_status): self
    {
        return new self($response_status);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
