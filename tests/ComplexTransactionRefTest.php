<?php

use DigiTickets\Stripe\Lib\ComplexTransactionRef;
use Omnipay\Tests\TestCase;

class ComplexTransactionRefTest extends TestCase
{
    public function creationProvider()
    {
        $sessionID = 'S100';
        $txRef = 'T200';
        $refRef = 'R300';
        return [
            'just session' => [$sessionID, null, null],
            'session and txref' => [$sessionID, $txRef, null],
            'session and refref' => [$sessionID, null, $refRef],
            'all three' => [$sessionID, $txRef, $refRef],
        ];
    }

    /**
     * @param string $sessionID
     * @param string|null $txRef
     * @param string|null $refundReference
     *
     * @dataProvider creationProvider
     */
    public function testCreation(string $sessionID, string $txRef = null, string $refundReference = null)
    {
        $transactionReference = new ComplexTransactionRef($sessionID, $txRef, $refundReference);

        $this->assertEquals($sessionID, $transactionReference->getSessionID());
        if (is_null($txRef)) {
            $this->assertNull($transactionReference->getTransactionReference());
        } else {
            $this->assertEquals($txRef, $transactionReference->getTransactionReference());
        }
        if (is_null($refundReference)) {
            $this->assertNull($transactionReference->getRefundReference());
        } else {
            $this->assertEquals($refundReference, $transactionReference->getRefundReference());
        }
    }

    public function buildFromJsonProvider()
    {
        $sessionID = 'S500';
        $txRef = 'T600';
        $refRef = 'R700';
        return [
            'just session' => [
                sprintf('{"sessionId":"%s"}', $sessionID),
                $sessionID,
                null,
                null,
            ],
            'session and txref' => [
                sprintf('{"txRef":"%s","sessionId":"%s"}', $txRef, $sessionID),
                $sessionID,
                $txRef,
                null,
            ],
            'session and refref' => [
                sprintf('{"refundRef":"%s","sessionId":"%s"}', $refRef, $sessionID),
                $sessionID,
                null,
                $refRef,
            ],
            'all three' => [
                sprintf('{"refundRef":"%s","txRef":"%s","sessionId":"%s"}', $refRef, $txRef, $sessionID),
                $sessionID,
                $txRef,
                $refRef,
            ],
        ];
    }

    /**
     * @param string $jsonString
     * @param string $expectedSessionID
     * @param string|null $expectedTxRef
     * @param string|null $expectedRefRef
     *
     * @dataProvider buildFromJsonProvider
     */
    public function testBuildFromJson(
        string $jsonString,
        string $expectedSessionID,
        string $expectedTxRef = null,
        string $expectedRefRef = null
    ) {
        $transactionReference = ComplexTransactionRef::buildFromJson($jsonString);

        // @TODO: I should be able to combine these assertions with the ones in the other test. Ie have a separate method that does the tests.
        $this->assertEquals($expectedSessionID, $transactionReference->getSessionID());
        if (is_null($expectedTxRef)) {
            $this->assertNull($transactionReference->getTransactionReference());
        } else {
            $this->assertEquals($expectedTxRef, $transactionReference->getTransactionReference());
        }
        if (is_null($expectedRefRef)) {
            $this->assertNull($transactionReference->getRefundReference());
        } else {
            $this->assertEquals($expectedRefRef, $transactionReference->getRefundReference());
        }
    }
}
