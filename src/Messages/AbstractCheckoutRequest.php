<?php

namespace DigiTickets\Stripe\Messages;

use Omnipay\Common\Message\AbstractRequest;

abstract class AbstractCheckoutRequest extends AbstractRequest
{
    const SUPPORTED_API_VERSION = '2019-12-03';

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
     * Get this request's allowAllPaymentMethods setting
     *
     * @return bool|null
     */
    public function getAllowAllPaymentMethods()
    {
        return $this->getParameter('allowAllPaymentMethods');
    }

    /**
     * Set the allowAllPaymentMethods setting, allowing for alternative payment methods such as Google Pay or Klarna
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setAllowAllPaymentMethods(bool $value = false): AbstractRequest
    {
        return $this->setParameter('allowAllPaymentMethods', $value);
    }
}
