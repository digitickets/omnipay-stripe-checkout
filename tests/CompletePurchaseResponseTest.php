<?php

namespace DigiTickets\StripeTests;

use DigiTickets\Stripe\Messages\CompletePurchaseRequest;
use DigiTickets\Stripe\Messages\CompletePurchaseResponse;
use DigiTickets\StripeTests\Fixtures\PaymentIntent;
use Mockery;
use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    const SESSION_ID = 'complete-purchase-request-id';

    public function creationProvider()
    {
        $request = Mockery::mock(CompletePurchaseRequest::class);
        $request->shouldReceive('getSessionID')->andReturn(self::SESSION_ID);
        $successfulTxRef = sprintf('{"txRef":"%s","sessionId":"%s"}', PaymentIntent::PI_ID, self::SESSION_ID);

        return [
            'no payment intent' => [
                $request,
                [],
                false,
                'Could not retrieve payment',
                sprintf('{"sessionId":"%s"}', self::SESSION_ID),
            ],
            'successful' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, CompletePurchaseResponse::STATUS_SUCCESS)],
                true,
                CompletePurchaseResponse::STATUS_SUCCESS,
                $successfulTxRef,
            ],
            'canceled' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, CompletePurchaseResponse::STATUS_CANCELED)],
                false,
                'Canceled by customer',
                $successfulTxRef,
            ],
            'unknown' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, 'Other')],
                false,
                'Unknown error',
                $successfulTxRef,
            ],
        ];
    }

    /**
     * @param CompletePurchaseRequest $request
     * @param $data
     * @param bool $expectSuccess
     * @param string $expectMessage
     *
     * @dataProvider creationProvider
     */
    public function testCreation(
        CompletePurchaseRequest $request,
        $data,
        bool $expectSuccess,
        string $expectMessage,
        string $expectedTransactionReference
    ) {
        $completePurchaseResponse = new CompletePurchaseResponse($request, $data);

        $this->assertEquals($expectSuccess, $completePurchaseResponse->isSuccessful());
        $this->assertEquals($expectMessage, $completePurchaseResponse->getMessage());
        $this->assertNull($completePurchaseResponse->getCode());

        $this->assertEquals($expectedTransactionReference, $completePurchaseResponse->getTransactionReference());
        $this->assertEquals('What is the correct value 2?', $completePurchaseResponse->getTransactionId());
    }
}
