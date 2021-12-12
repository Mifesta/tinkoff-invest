<?php

namespace TinkoffInvest;

class OperationStatus
{
    public const DECLINE = 'Decline';
    public const DONE = 'Done';
    public const PROGRESS = 'Progress';
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $operation_status
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $operation_status)
    {
        $this->value = self::checkOperationStatusValue($operation_status);
    }

    /**
     * Get operation status
     * @param string $operation_status
     * @return \TinkoffInvest\OperationStatus
     * @throws \TinkoffInvest\Exception
     */
    public static function getStatus(string $operation_status): OperationStatus
    {
        return new self($operation_status);
    }

    /**
     * Check operation status value
     * @param string $operation_status
     * @return string
     * @throws \TinkoffInvest\Exception
     */
    private static function checkOperationStatusValue(string $operation_status): ?string
    {
        $operation_status = strtolower($operation_status);
        switch ($operation_status) {
            case 'decline' :
                return self::DECLINE;
            case 'done' :
                return self::DONE;
            case 'progress' :
                return self::PROGRESS;
            default :
                throw new Exception('Undefined operation status');
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
