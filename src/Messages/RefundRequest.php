<?php

namespace Omnipay\StripeCheckout\Messages;

/**
 * This is the request to refund a payment.
 * The Stripe API allows you to specify an amount, but this functionality hasn't been implemented; currently, it will
 * always refund a payment in full.
 */
class RefundRequest extends AbstractCheckoutRequest {

    public function getData() {
        $this->validate('apiKey', 'transactionId');

        $data = [
            'payment_intent' => $this->getTransactionId(),
            'amount'         => $this->getAmountInteger() ?: null,
        ];

        return $data;
    }

    public function sendData($data) {
        \Stripe\Stripe::setApiKey($this->getApiKey());
        $refund = \Stripe\Refund::create($data);
        return $this->response = new RefundResponse($this, ['refund' => $refund]);
    }
}
