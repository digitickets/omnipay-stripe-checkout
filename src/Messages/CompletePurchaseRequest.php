<?php

namespace Omnipay\StripeCheckout\Messages;

class CompletePurchaseRequest extends AbstractCheckoutRequest {

    public function getData() {
        // Just validate the parameters.
        $this->validate('apiKey');

        $data = [
            'payment_intent' => $this->getTransactionId(),
        ];

        return $data; // The data we need (the session id) is already in the request object.
    }

    public function sendData($data) {
        // Retrieve the session that would have been started earlier.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        $paymentIntent = \Stripe\PaymentIntent::retrieve($data['payment_intent']);

        return $this->response = new CompletePurchaseResponse($this, ['paymentIntent' => $paymentIntent]);
    }
}
