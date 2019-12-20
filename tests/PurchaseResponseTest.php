<?php

use DigiTickets\Stripe\Messages\PurchaseRequest;
use DigiTickets\Stripe\Messages\PurchaseResponse;
use DigiTickets\StripeTests\Fixtures\Request;
use DigiTickets\StripeTests\Fixtures\StripeSession;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    public function creationProvider()
    {
        $request = new Request();
        return [
            'no session' => [$request, [], null],
            'with session' => [$request, new StripeSession(StripeSession::SESSION_ID_1), StripeSession::SESSION_ID_1],
        ];
    }
    /**
     * @param RequestInterface $request
     * @param $data
     * @param string|null $expectedSessionID
     *
     * @dataProvider creationProvider
     */
    public function testCreation(RequestInterface $request, $data, string $expectedSessionID = null)
    {
        $purchaseResponse = new PurchaseResponse($request, $data);

        // Check the session id.
        if (is_null($expectedSessionID)) {
            $this->assertNull($purchaseResponse->getSessionID());
        } else {
            $this->assertEquals($expectedSessionID, $purchaseResponse->getSessionID());
        }

        // Check the responses to various other methods, that are all return hard-coded values.
        $this->assertFalse($purchaseResponse->isSuccessful());
        $this->assertTrue($purchaseResponse->isRedirect());
        $this->assertEquals('', $purchaseResponse->getRedirectUrl());
        $this->assertEquals('GET', $purchaseResponse->getRedirectMethod());
        $this->assertEquals([], $purchaseResponse->getRedirectData());

        // Check the transaction reference.
        // @TODO: I think that if the $expectedSessionID is null, we'll get an exception. Check which exception is it.
        if (is_null($expectedSessionID)) {
//            $this->expectException(???);
        }
        $txRef = $purchaseResponse->getTransactionReference();
        $this->assertEquals(sprintf('{"sessionId":"%s"}', $expectedSessionID), $txRef);
    }
}
