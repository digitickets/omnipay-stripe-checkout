<?php

namespace Omnipay\StripeTests;

use Omnipay\StripeCheckout\Messages\PurchaseResponse;
use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{

    public function testCheckoutSessionAsyncPaymentFailed()
    {
        $httpResponse = $this->getMockHttpResponse('CheckoutSessionAsyncPaymentFailed.txt');
        $response = new PurchaseResponse($this->getMockRequest(), (string) $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('payment_intent_id_session_async_failed', $response->getTransactionReference());
        $this->assertSame('session_id_session_async_failed', $response->getSessionID());
    }

    public function testCheckoutSessionAsyncPaymentSuccess()
    {
        $httpResponse = $this->getMockHttpResponse('CheckoutSessionAsyncPaymentSuccess.txt');
        $response = new PurchaseResponse($this->getMockRequest(), (string) $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('payment_intent_id_session_async_success', $response->getTransactionReference());
        $this->assertSame('session_id_session_async_success', $response->getSessionID());
    }

    public function testCheckoutSessionComplete()
    {
        $httpResponse = $this->getMockHttpResponse('CheckoutSessionComplete.txt');
        var_dump((string) $httpResponse->getBody());
        $response = new PurchaseResponse($this->getMockRequest(), (string) $httpResponse->getBody());

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('payment_intent_id_session_complete', $response->getTransactionReference());
        $this->assertSame('session_id_session_complete', $response->getSessionID());
    }

    public function testCheckoutSessionCreated()
    {
        $httpResponse = $this->getMockHttpResponse('CheckoutSessionCreated.txt');
        $response = new PurchaseResponse($this->getMockRequest(), (string) $httpResponse->getBody());

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('redirect_url_session_created', $response->getRedirectUrl());
        $this->assertSame('GET', $response->getRedirectMethod());
        $this->assertSame('payment_intent_id_session_created', $response->getTransactionReference());
        $this->assertSame('session_id_session_created', $response->getSessionID());
    }
}
