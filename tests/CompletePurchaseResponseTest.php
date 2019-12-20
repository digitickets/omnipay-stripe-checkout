<?php

namespace DigiTickets\StripeTests;

use DigiTickets\Stripe\Messages\CompletePurchaseRequest;
use DigiTickets\Stripe\Messages\CompletePurchaseResponse;
use DigiTickets\StripeTests\Fixtures\PaymentIntent;
use Mockery;
use Omnipay\Tests\TestCase;

class CompletePurchaseResponseTest extends TestCase
{
    public function creationProvider()
    {
        $request = Mockery::mock(CompletePurchaseRequest::class);

        return [
            'no payment intent' => [
                $request,
                [],
                false,
                'Could not retrieve payment'
            ],
            'successful' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, CompletePurchaseResponse::STATUS_SUCCESS)],
                true,
                CompletePurchaseResponse::STATUS_SUCCESS
            ],
            'canceled' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, CompletePurchaseResponse::STATUS_CANCELED)],
                false,
                'Canceled by customer'
            ],
            'unknown' => [
                $request,
                ['paymentIntent' => new PaymentIntent(PaymentIntent::PI_ID, 'Other')],
                false,
                'Unknown error'
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
    public function testCreation(CompletePurchaseRequest $request, $data, bool $expectSuccess, string $expectMessage)
    {
        $completePurchaseResponse = new CompletePurchaseResponse($request, $data);

        $this->assertEquals($expectSuccess, $completePurchaseResponse->isSuccessful());
        $this->assertEquals($expectMessage, $completePurchaseResponse->getMessage());
        $this->assertNull($completePurchaseResponse->getCode());

        $this->assertEquals('What is the correct value 1?', $completePurchaseResponse->getTransactionReference());
        $this->assertEquals('What is the correct value 2?', $completePurchaseResponse->getTransactionId());
    }
}
