<?php

namespace DigiTickets\StripeTests;

use DigiTickets\Stripe\Lib\ComplexTransactionRef;
use Omnipay\Tests\TestCase;

class ComplexTransactionRefTest extends TestCase
{
    private function checkTransactionReferenceObject(
        ComplexTransactionRef $complexTransactionRef,
        string $expectedSessionID,
        string $expectedTxRef = null,
        string $expectedRefRef = null
    ) {
        $this->assertEquals($expectedSessionID, $complexTransactionRef->getSessionID());
        if (is_null($expectedTxRef)) {
            $this->assertNull($complexTransactionRef->getTransactionReference());
        } else {
            $this->assertEquals($expectedTxRef, $complexTransactionRef->getTransactionReference());
        }
        if (is_null($expectedRefRef)) {
            $this->assertNull($complexTransactionRef->getRefundReference());
        } else {
            $this->assertEquals($expectedRefRef, $complexTransactionRef->getRefundReference());
        }
    }

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

        // The checking of the complex tx ref is the same as in the other test, so there's a method that will do it.
        $this->checkTransactionReferenceObject(
            $transactionReference,
            $sessionID,
            $txRef,
            $refundReference
        );
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

        // The checking of the complex tx ref is the same as in the other test, so there's a method that will do it.
        $this->checkTransactionReferenceObject(
            $transactionReference,
            $expectedSessionID,
            $expectedTxRef,
            $expectedRefRef
        );
    }
}
