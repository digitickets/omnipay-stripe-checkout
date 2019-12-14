<?php

namespace DigiTickets\Stripe\Messages;

use Omnipay\Common\Message\AbstractRequest;

class PurchaseRequest extends AbstractRequest
{
    /**
     * Get the gateway API Key (the "secret key").
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * Set the gateway API Key.
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setApiKey($value): AbstractRequest
    {
        return $this->setParameter('apiKey', $value);
    }

    public function getData()
    {
        // Just validate the parameters.
        $this->validate('apiKey', 'transactionId', 'returnUrl', 'cancelUrl'/*, ???*/);

        return null;
    }

    public function sendData($data)
    {
        // We use Stripe's SDK to initialise a (Stripe) session. The session gets passed through the process and is
        // used to identify this transaction.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        // Initiate the session.
        $session = \Stripe\Checkout\Session::create(
            [
                'client_reference_id' => $this->getTransactionId(),
                'payment_method_types' => ['card'],
                'line_items' => array_map(
                    function(\Omnipay\Common\Item $item) {
                        return [
                            'name' => $item->getName(),
                            'description' => $item->getDescription(),
                            'amount' => 100*$item->getPrice(), // @TODO: The multiplier depends on the currency
                            'currency' => $this->getCurrency(),
                            'quantity' => $item->getQuantity(),
                        ];
                    },
                    $this->getItems()->all()
                ),
                'success_url' => $this->getReturnUrl().'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->getCancelUrl(),
            ]
        );

        return $this->response = new PurchaseResponse($this, ['session' => $session]);
    }
}
