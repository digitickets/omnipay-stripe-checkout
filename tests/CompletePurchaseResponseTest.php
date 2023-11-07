<?php

namespace Omnipay\StripeTests;

use Omnipay\StripeCheckout\Messages\CompletePurchaseRequest;
use Omnipay\StripeCheckout\Messages\CompletePurchaseResponse;
use Omnipay\StripeTests\Fixtures\PaymentIntent;
use Mockery;
use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    const SESSION_ID = 'complete-purchase-request-session-id';
    const TX_ID = 'complete-purchase-request-id';

    public function creationProvider()
    {
        $request = Mockery::mock(CompletePurchaseRequest::class);
        $request->shouldReceive('getSessionID')->andReturn(self::SESSION_ID);
        $request->shouldReceive('getTransactionId')->andReturn(self::TX_ID);
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
     * @param string $expectedTransactionReference
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
        $this->assertEquals(self::TX_ID, $completePurchaseResponse->getTransactionId());
    }
}
