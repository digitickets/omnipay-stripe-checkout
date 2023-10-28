<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractResponse;

class FetchTransactionResponse extends AbstractResponse {

    const STATUS_SUCCESS  = 'succeeded';
    const STATUS_CANCELED = 'requires_payment_method'; // As far as I can tell this is what we receive when the customer cancels the card form.
    /**
     * @var CompletePurchaseRequest
     */
    protected $request;
    /**
     * @var bool
     */
    private $successful = false;
    /**
     * @var string|null
     */
    private $message = null;
    /**
     * @var string|null
     */
    private $code = null;

    /**
     * @var string|null
     */

    public function __construct(FetchTransactionRequest $request, $data) {
        parent::__construct($request, $data);

        if (isset($data['paymentIntent'])) {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = $data['paymentIntent'];

            $this->internalTransactionRef = $paymentIntent->id;

            // Amazingly there doesn't seem to be a simple code nor message when the payment succeeds (or fails).
            // For now, just use the status for the message, and leave the code blank.
            switch ($paymentIntent->status) {
                case self::STATUS_SUCCESS:
                    $this->successful = true;
                    $this->message = $paymentIntent->status;
                    break;
                case self::STATUS_CANCELED:
                    $this->successful = false;
                    $this->message = 'Canceled by customer';
                    break;
                default:
                    // We don't know what happened, so act accordingly. Would be nice to make this better over time.
                    $this->successful = false;
                    $this->message = 'Unknown error';
                    break;
            }
        } else {
            $this->successful = false; // Just make sure.
            $this->message = 'Could not retrieve payment';
        }
    }

    public function isSuccessful() {
        return $this->successful;
    }

    public function isCancelled() {
        return !$this->successful;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getCode() {
        return $this->code;
    }

    public function getTransactionId() {
        return $this->request->getTransactionId();
    }
}
