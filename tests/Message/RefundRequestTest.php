<?php

namespace Omnipay\StripeTests;

use Omnipay\Common\Http\Client;
use Omnipay\StripeCheckout\Messages\RefundRequest;
use Omnipay\StripeCheckout\Messages\RefundResponse;
use Omnipay\StripeTests\Mock\StripeMockClient;
use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{
    protected $request;
    protected $client;

    protected function setUp()
    {
        $this->client = new StripeMockClient();
        \Stripe\ApiRequestor::setHttpClient($this->client);
        $this->request = new RefundRequest(new Client, $this->getHttpRequest());
        $this->request->initialize([
            'apiKey'            => 'api_key_refund_test',
            'transactionId'     => 'transaction_id_refund_test'
        ]);
    }

    public function testRefundFailed()
    {
        $response_string = (string) $this->getMockHttpResponse('RefundFailed.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(RefundResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertSame('failed', $response->getMessage());
        $this->assertSame('payment_intent_id_refund_failed', $response->getTransactionId());
    }

    public function testRefundSuccess()
    {
        $response_string = (string) $this->getMockHttpResponse('RefundSuccess.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(RefundResponse::class, $response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertSame('payment_intent_id_refund_success', $response->getTransactionId());
    }

    public function testRefundCanceled()
    {
        $response_string = (string) $this->getMockHttpResponse('RefundCanceled.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(RefundResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isPending());
        $this->assertSame('canceled', $response->getMessage());
        $this->assertSame('payment_intent_id_refund_canceled', $response->getTransactionId());
    }

    public function testRefundPending()
    {
        $response_string = (string) $this->getMockHttpResponse('RefundPending.txt')->getBody();
        $this->client->setMockResponseBody($response_string);

        $response = $this->request->send();
        $this->assertInstanceOf(RefundResponse::class, $response);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isPending());
        $this->assertSame('payment_intent_id_refund_pending', $response->getTransactionId());
    }
}