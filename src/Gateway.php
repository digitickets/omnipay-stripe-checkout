<?php

namespace Omnipay\StripeCheckout;

use Omnipay\StripeCheckout\Messages\CompletePurchaseRequest;
use Omnipay\StripeCheckout\Messages\FetchRefundRequest;
use Omnipay\StripeCheckout\Messages\FetchTransactionRequest;
use Omnipay\StripeCheckout\Messages\PurchaseRequest;
use Omnipay\StripeCheckout\Messages\RefundRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

class Gateway extends AbstractGateway {

    /**
     * Get the gateway API Key (the "secret key").
     *
     * @return string
     */
    public function getApiKey(): string {
        return $this->getParameter('apiKey');
    }

    /**
     * Set the gateway API Key.
     *
     * @return AbstractGateway provides a fluent interface.
     */
    public function setApiKey($value): AbstractGateway {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * Get the gateway public Key (the "publishable key").
     *
     * @return string
     */
    public function getPublic(): string {
        return $this->getParameter('public');
    }

    /**
     * Set the gateway public Key.
     *
     * @return AbstractGateway provides a fluent interface.
     */
    public function setPublic($value): AbstractGateway {
        return $this->setParameter('public', $value);
    }

    public function getName() {
        return 'Stripe (Checkout)';
    }

    public function purchase(array $parameters = []): RequestInterface {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    public function completePurchase(array $parameters = []): RequestInterface {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }

    public function fetchTransaction(array $parameters = []): RequestInterface {
        return $this->createRequest(FetchTransactionRequest::class, $parameters);
    }

    public function fetchRefund(array $parameters = []): RequestInterface {
        return $this->createRequest(FetchRefundRequest::class, $parameters);
    }

    public function refund(array $parameters = []): RequestInterface {
        return $this->createRequest(RefundRequest::class, $parameters);
    }

    public function getPaymentMethodTypes() {
        return $this->getParameter('paymentMethodTypes');
    }

    public function setPaymentMethodTypes($value): AbstractGateway {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (!is_array($value)) {
            throw new \Exception('PaymentMethodTypes must be an array or a comma-separated string');
        }

        return $this->setParameter('paymentMethodTypes', $value);
    }
}
