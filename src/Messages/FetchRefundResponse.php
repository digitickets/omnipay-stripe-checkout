<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Stripe\PaymentIntent;
use Stripe\Refund;

class FetchRefundResponse extends AbstractResponse {

    /**
     * @var Refund|null
     */
    protected $refund = null;

    public function __construct(FetchTransactionRequest $request, $data) {
        parent::__construct($request, $data);

        if (isset($data) && isset($data['refund']) && $data['refund'] instanceof Refund) {
            $this->setRefund($data['refund']);
        } else {
            throw new \InvalidArgumentException('A valid Refund must be supplied');
        }
    }

    public function setRefund(Refund $refund) {
        $this->refund = $refund;
    }

    public function isSuccessful() {
        return isset($this->refund) && isset($this->refund->status)
            && $this->refund->status == Refund::STATUS_SUCCEEDED;
    }

    public function isCancelled() {
        return isset($this->refund) && isset($this->refund->status)
            && $this->refund->status == Refund::STATUS_CANCELED;
    }

    public function getMessage() {
        return isset($this->refund) && isset($this->refund->status)
            ? $this->refund->status : null;
    }

    public function getTransactionId() {
        return $this->request->getTransactionId();
    }
}
