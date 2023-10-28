<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractRequest;

abstract class AbstractCheckoutRequest extends AbstractRequest {

    /**
     * Get the gateway API Key (the "secret key").
     *
     * @return string
     */
    public function getApiKey(): string {
        return $this->getParameter('apiKey');
    }

    /**
     * Set the gateway API Key.
     *
     * @return AbstractRequest provides a fluent interface.
     */
    public function setApiKey($value): AbstractRequest {
        return $this->setParameter('apiKey', $value);
    }

    public function getPaymentMethodTypes() {
        return $this->getParameter('paymentMethodTypes');
    }

    public function setPaymentMethodTypes($value): AbstractRequest {
        if (is_string($value)) {
            $value = explode(',', $value);
        }

        if (!is_array($value)) {
            throw new \Exception('PaymentMethodTypes must be an array or a comma-separated string');
        }

        return $this->setParameter('paymentMethodTypes', $value);
    }

    public function getCustomer() {
        return $this->getParameter('customer');
    }

    public function setCustomer($value): AbstractRequest {
        return $this->setParameter('customer', $value);
    }
}
