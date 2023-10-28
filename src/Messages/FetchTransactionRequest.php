<?php

namespace Omnipay\StripeCheckout\Messages;

/**
 * Class FetchTransactionRequest
 *
 * @package Omnipay\AdyenPos\Message\Request
 *
 * @method FetchTransactionResponse send()
 */
class FetchTransactionRequest extends AbstractCheckoutRequest {

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData() {

        $this->validate('apiKey', 'transactionId');

        $data = [
            'payment_intent' => $this->getTransactionId(),
        ];

        return $data;
    }

    public function sendData($data) {
        // Retrieve the session that would have been started earlier.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        $paymentIntent = \Stripe\PaymentIntent::retrieve($data['payment_intent']);

        return $this->response = new FetchTransactionResponse($this, ['paymentIntent' => $paymentIntent]);
    }
}
