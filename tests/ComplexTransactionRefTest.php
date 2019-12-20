<?php

use DigiTickets\Stripe\Lib\ComplexTransactionRef;
use Omnipay\Tests\GatewayTestCase;

class ComplexTransactionRefTest extends GatewayTestCase
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
}
