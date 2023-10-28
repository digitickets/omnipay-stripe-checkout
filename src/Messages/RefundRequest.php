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
        // We use Stripe's SDK to initialise a (Stripe) session.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        try {
            $refund = \Stripe\Refund::create($data);
        } catch (\Exception $e) {
            // Stripe wasn't happy about something. In theory, the exception will be a subclass of
            // \Stripe\Exception\ApiErrorException, but there's no harm in catching every exception, because the
            // response object only needs the message.
            $refund = $e;
        }

        return $this->response = new RefundResponse($this, ['refund' => $refund]);
    }
}
