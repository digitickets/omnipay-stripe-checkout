<?php

use DigiTickets\Stripe\Lib\ComplexTransactionRef;
use Omnipay\Tests\GatewayTestCase;

class ComplexTransactionRefTest extends GatewayTestCase
{
    public function testCreate(string $sessionID, string $txRef = null, string $refundReference = null)
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
