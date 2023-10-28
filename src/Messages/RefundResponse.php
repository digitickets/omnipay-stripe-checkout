<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractResponse;

class RefundResponse extends AbstractResponse {

    /**
     * @var RefundRequest
     */
    protected $request;
    /**
     * @var bool
     */
    private $success = false;
    private $pending = false;
    /**
     * @var string|null
     */
    private $message = null;
    /**
     * @var string|null
     */
    private $transactionId = null;

    public function __construct(RefundRequest $request, $data) {
        parent::__construct($request, $data);

        $refund = $data['refund'] ?? null;
        // $refund will be an exception if something went badly wrong, in which case we assume the refund failed with the
        // failure reason coming from the exception message. If it's not an exception... @TODO: finish this.
        // In theory, the exception will be a subclass of \Stripe\Exception\ApiErrorException, but we need to handle
        // any exception being thrown, and there's no point separating them because we'd do exactly the same thing with
        // a stripe and a non-stripe exception.
        if ($refund instanceof \Exception) {
            $this->success = false;
            $this->message = $refund->getMessage();
        } else if ($refund instanceof \Stripe\Refund) {
            if ($refund->status === \Stripe\Refund::STATUS_SUCCEEDED) {
                // Looks like it was okay.
                $this->success = true;
                $this->message = $refund->status;
            }
            else if ($refund->status === \Stripe\Refund::STATUS_PENDING) {
                // Looks like it was okay, but still pending
                $this->pending = true;
                $this->message = $refund->status;
            }
            else {
                // Looks like it failed for some reason. "Status" seems to be the only field we can use to convey any error.
                $this->success = false;
                $this->message = $refund->status;
            }
            $this->transactionId = $refund->id;
        } else {
            // Something unexpected happened
            $this->success = false;
            $this->message = 'Unexpected refund data received';
        }
    }

    public function isSuccessful() {
        return $this->success;
    }

    public function isPending() {
        return $this->pending;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getTransactionId() {
        return $this->transactionId;
    }
}
