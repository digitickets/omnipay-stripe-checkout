<?php

namespace DigiTickets\Stripe\Lib;

/**
 * This is a value object that handles the translation between a transaction reference and its components.
 * Because the transaction reference is comprised of the session and the transaction ref, it's useful to have this class
 * that can translate both ways between them (separate values and JSON).
 * The constructor accepts the separate values, and you can call asJson() to get the JSON.
 * The buildFromJson() method takes a JSON string, extracts the values and instantiates the class.
 * The class has a getter for each componant.
 */
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

    public static function buildFromJson($value): self
    {
        $refParts = json_decode($value, true);

        return new static($refParts['sessionId'] ?? '', $refParts['txRef'] ?? null);
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
