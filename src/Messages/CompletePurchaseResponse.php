<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Stripe\PaymentIntent;

class CompletePurchaseResponse extends FetchTransactionResponse {
    public function getTransactionReference()
    {
        return $this->payment_intent;
    }
}
