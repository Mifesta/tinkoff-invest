<?php

namespace TinkoffInvest;

use Throwable;

class Response
{
    /**
     * @var \stdClass|array
     */
    private $payload;
    /**
     * @var \TinkoffInvest\ResponseStatus
     */
    private ResponseStatus $status;
    /**
     * @var string
     */
    private $trackingId;

    /**
     * @param string $response
     * @param int $status_code
     * @return void
     * @throws \TinkoffInvest\Exception
     */
    public function __construct(string $response, int $status_code)
    {
        if (empty($response)) {
            throw new Exception('Response is null');
        }
        try {
            $result = json_decode($response);
            if (isset($result->trackingId, $result->payload, $result->status)) {
                $this->payload = $result->payload;
                $this->trackingId = $result->trackingId;
                $this->status = ResponseStatus::getStatus($result->status);
            } else {
                throw new Exception('Required fields are empty');
            }
        } catch (Throwable $e) {
            switch ($status_code) {
                case 401:
                    $error_message = 'Authorization error';
                    break;
                case 429:
                    $error_message = 'Too Many Requests';
                    break;
                default:
                    $error_message = 'Unknown error [' . $status_code . ']';
                    break;
            }
            throw new Exception($error_message);
        }
        if ($this->status->getValue() === ResponseStatus::ERROR) {
            throw new Exception($this->getTrackingId() . ': ' . $this->payload->message . ' [' . $this->payload->code . ']');
        }
    }

    /**
     * @return \stdClass|array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return \TinkoffInvest\ResponseStatus
     */
    public function getStatus(): ResponseStatus
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getTrackingId(): string
    {
        return $this->trackingId;
    }
}
