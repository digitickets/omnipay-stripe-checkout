<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Stripe\Refund;

class RefundResponse extends AbstractResponse {

    /**
     * @var Refund|null $session
     */
    private $refund = null;

    public function __construct(RefundRequest $request, $data) {
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

    public function isPending() {
        return isset($this->refund) && isset($this->refund->status)
            && $this->refund->status == Refund::STATUS_PENDING;
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
        return isset($this->refund) && isset($this->refund->payment_intent)
            ? $this->refund->payment_intent : null;
    }
}
