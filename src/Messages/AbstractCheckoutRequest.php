<?php

namespace DigiTickets\Stripe\Messages;

use Omnipay\Common\Message\AbstractRequest;

abstract class AbstractCheckoutRequest extends AbstractRequest
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

    /**
     * Get the customer email.
     *
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->getParameter('customerEmail');
    }

    /**
     * Set the customer email.
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setCustomerEmail($value): AbstractRequest
    {
        return $this->setParameter('customerEmail', $value);
    }
}
