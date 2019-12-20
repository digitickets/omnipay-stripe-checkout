<?php

namespace DigiTickets\Stripe\Messages;

use DigiTickets\Stripe\Lib\ComplexTransactionRef;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Stripe\PaymentIntent;

class CompletePurchaseResponse extends AbstractResponse
{
    const STATUS_SUCCESS = 'succeeded';
    const STATUS_CANCELED = 'requires_payment_method'; // As far as I can tell this is what we receive when the customer cancels the card form.

    /**
     * @var CompletePurchaseRequest
     */
    private $request;

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
    private $internalTransactionRef;

    public function __construct(CompletePurchaseRequest $request, $data)
    {
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

    public function isSuccessful()
    {
        return $this->successful;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getTransactionReference()
    {
        return (new ComplexTransactionRef($this->request->getSessionID(), $this->internalTransactionRef))->asJson();
    }

    public function getTransactionId()
    {
        return $this->request->getTransactionId();
    }
}
