<?php

namespace DigiTickets\Stripe\Messages;

class CompletePurchaseRequest extends AbstractCheckoutRequest
{
    /**
     * @var string|null
     */
    private $sessionID;

    /**
     * @var string|null
     */
    private $transactionReference; // @TODO: Not sure if this is needed now.

    /**
     * @return string|null
     */
    public function getSessionID()
    {
        return $this->sessionID;
    }

    /**
     * Because we have to pass the session id around, but also save the transaction ref in the same place, they are
     * combined, in JSON format, so this setter has to split them.
     *
     * @param string $value
     */
    public function setTransactionReference($value)
    {
        // @TODO: Again, have common code that will extract the values out.
        $refParts = json_decode($value, true);
        $this->sessionID = $refParts['sessionId'] ?? null;
        // We do this so that we can use the parent's getter (and therefore don't have to write our own).
        parent::setTransactionReference($refParts['txRef'] ?? null); // @TODO: Check whether we actually need this.
    }

    public function getData()
    {
        return null; // The data we need (the session id) is already in the request object.
    }

    public function sendData($data)
    {
        // Retrieve the session that would have been started earlier.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        $session = \Stripe\Checkout\Session::retrieve($this->sessionID);
        $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

        return $this->response = new CompletePurchaseResponse($this, ['paymentIntent' => $paymentIntent]);
    }
}
