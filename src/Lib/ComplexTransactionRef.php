<?php

namespace DigiTickets\Stripe\Lib;

class ComplexTransactionRef
{
    /**
     * @var string
     */
    private $sessionID;

    /**
     * @var string|null
     */
    private $transactionReference;

    public function __construct(string $sessionID, string $transactionReference = null)
    {
        $this->sessionID = $sessionID;
        $this->transactionReference = $transactionReference;
    }

    public static function build($value): self
    {
        $refParts = json_decode($value, true);

        return new static($refParts['sessionId'] ?? null, $refParts['txRef'] ?? null);
    }

    public function getSessionID(): string
    {
        return $this->sessionID;
    }

    /**
     * @return string|null
     */
    public function getTransactionReference()
    {
        return $this->transactionReference;
    }

    public function asJson()
    {
        // We want the txRef first.
        // We don't want any elements (specifically the txRef) that have null values, hence the array_filter.
        return json_encode(
            array_filter(
                [
                    'txRef' => $this->transactionReference,
                    'sessionId' => $this->sessionID,
                ]
            )
        );
    }
}
