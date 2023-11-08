<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Stripe\PaymentIntent;

class FetchTransactionResponse extends AbstractResponse {

    /**
     * @var PaymentIntent|null
     */
    protected $payment_intent = null;

    public function __construct(FetchTransactionRequest $request, $data) {
        parent::__construct($request, $data);

        if (isset($data) && isset($data['paymentIntent']) && $data['paymentIntent'] instanceof PaymentIntent) {
            $this->setPaymentIntent($data['paymentIntent']);
        } else {
            throw new \InvalidArgumentException('A valid Payment Intent must be supplied');
        }
    }

    public function setPaymentIntent(PaymentIntent $payment_intent) {
        $this->payment_intent = $payment_intent;
    }

    public function isSuccessful() {
        return isset($this->payment_intent) && isset($this->payment_intent->status)
            && $this->payment_intent->status == PaymentIntent::STATUS_SUCCEEDED;
    }

    public function isCancelled() {
        return isset($this->payment_intent) && isset($this->payment_intent->status)
            && $this->payment_intent->status == PaymentIntent::STATUS_CANCELED;
    }

    public function getMessage() {
        return isset($this->payment_intent) && isset($this->payment_intent->last_payment_error)
            && isset($this->payment_intent->last_payment_error->message)
            ? $this->payment_intent->last_payment_error->message : null;
    }

    public function getCode() {
        return isset($this->payment_intent) && isset($this->payment_intent->last_payment_error)
            && isset($this->payment_intent->last_payment_error->code)
            ? $this->payment_intent->last_payment_error->code : null;
    }

    public function getTransactionId() {
        return $this->request->getTransactionId();
    }
}
