<?php

namespace Omnipay\StripeTests;

use Omnipay\Common\Http\Client;
use Omnipay\StripeCheckout\Messages\FetchTransactionRequest;
use Omnipay\StripeCheckout\Messages\FetchTransactionResponse;
use Omnipay\StripeTests\Mock\StripeMockClient;
use Omnipay\Tests\TestCase;

class FetchTransactionRequestTest extends TestCase
{
    protected $request;
    protected $client;

    protected function setUp()
    {
        $this->client = new StripeMockClient();
        \Stripe\ApiRequestor::setHttpClient($this->client);
        $this->request = new FetchTransactionRequest(new Client, $this->getHttpRequest());
        $this->request->initialize([
            'apiKey' => 'api_key_test',
            'transactionId' => 'transaction_id_fetch_transaction_request_test',
        ]);
    }

    public function testPaymentIntentPaymentAttemptFailed()
    {
        $response_string = (string) $this->getMockHttpResponse('PaymentIntentPaymentAttemptFailed.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(FetchTransactionResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertSame('payment_attempt_failed_error_message', $response->getMessage());
        $this->assertSame('payment_intent_payment_attempt_failed', $response->getCode());
        $this->assertSame('transaction_id_fetch_transaction_request_test', $response->getTransactionId());
    }

    public function testPaymentIntentPaymentRequiresAction()
    {
        $response_string = (string) $this->getMockHttpResponse('PaymentIntentPaymentRequiresAction.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(FetchTransactionResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());
        $this->assertSame('transaction_id_fetch_transaction_request_test', $response->getTransactionId());
    }

    public function testPaymentIntentPaymentCanceled()
    {
        $response_string = (string) $this->getMockHttpResponse('PaymentIntentPaymentCanceled.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(FetchTransactionResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isCancelled());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());
        $this->assertSame('transaction_id_fetch_transaction_request_test', $response->getTransactionId());
    }

    public function testPaymentIntentPaymentSuccess()
    {
        $response_string = (string) $this->getMockHttpResponse('PaymentIntentPaymentSuccess.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(FetchTransactionResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isCancelled());
        $this->assertNull($response->getMessage());
        $this->assertNull($response->getCode());
    }
}
