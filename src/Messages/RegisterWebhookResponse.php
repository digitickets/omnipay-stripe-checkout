<?php

namespace DigiTickets\Stripe\Messages;

use Omnipay\Common\Message\AbstractResponse;

class RegisterWebhookResponse extends AbstractResponse
{
    /**
     * @return string|null
     */
    public function getUrl()
    {
        $webhook = $this->getData();

        return $webhook["url"];
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        $webhook = $this->getData();

        return $webhook && is_array($webhook) && !empty($webhook['id']);
    }
}