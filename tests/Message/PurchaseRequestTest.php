<?php

namespace Omnipay\StripeTests;

use Omnipay\Common\Http\Client;
use Omnipay\StripeCheckout\Messages\PurchaseRequest;
use Omnipay\StripeCheckout\Messages\PurchaseResponse;
use Omnipay\StripeTests\Mock\StripeMockClient;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    protected $request;
    protected $client;

    protected function setUp()
    {
        $this->client = new StripeMockClient();
        \Stripe\ApiRequestor::setHttpClient($this->client);
        $this->request = new PurchaseRequest(new Client, $this->getHttpRequest());
        $this->request->initialize([
            'apiKey' => 'api_key_test',
            'transactionId' => 'transaction_id',
            'returnUrl' => 'return_url',
            'cancelUrl' => 'cancel_url',
            'customer' => 'customer',
        ]);
        $this->request->setItems([
            [
                'price' => 10,
                'name' => 'name_item_1',
                'description' => 'description_item_1',
                'quantity' => 'quantity_item_1',
                'currency' => 'EUR'
            ]
        ]);
    }

    public function testCheckoutSessionCreatedSuccess()
    {
        $response_string = (string) $this->getMockHttpResponse('CheckoutSessionCreatedSuccess.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(PurchaseResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('redirect_url_session_created', $response->getRedirectUrl());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('payment_intent_id_session_created', $response->getTransactionReference());
        $this->assertSame('session_id_session_created', $response->getSessionID());
    }


    public function testCheckoutSessionComplete()
    {
        $response_string = (string) $this->getMockHttpResponse('CheckoutSessionComplete.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(PurchaseResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('redirect_url_session_complete', $response->getRedirectUrl());
        $this->assertSame('payment_intent_id_session_complete', $response->getTransactionReference());
        $this->assertSame('session_id_session_complete', $response->getSessionID());
    }


    public function testCheckoutSessionCreatedEmpty()
    {
        $response_string = (string) $this->getMockHttpResponse('ErrorResponse.txt')->getBody();
        $this->client->setMockResponseBody($response_string);
        $this->setExpectedException(\InvalidArgumentException::class);

        $response = $this->request->send();
        $this->assertInstanceOf(PurchaseResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }
}
