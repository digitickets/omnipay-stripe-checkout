<?php

namespace DigiTickets\Stripe\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Stripe\PaymentIntent;

class CompletePurchaseResponse extends AbstractResponse
{
    const STATUS_SUCCESS = 'succeeded';

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

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        if (isset($data['paymentIntent'])) {
            /** @var \Stripe\PaymentIntent $paymentIntent */
            $paymentIntent = $data['paymentIntent'];

            $this->internalTransactionRef = $paymentIntent->id;
            $this->successful = $paymentIntent->status === self::STATUS_SUCCESS;
            // Amazingly there doesn't seem to be a simple code nor message when the payment succeeds.
            // For now, just use the status for the message, and leave the code blank.
            $this->message = $paymentIntent->status;
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
        // @TODO: Again, common code.
        return json_encode(['txRef' => $this->internalTransactionRef, 'sessionId' => $this->request->getSessionID()]);
    }

    public function getTransactionId()
    {
        return $this->request->getTransactionId();
    }
}
