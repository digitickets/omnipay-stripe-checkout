<?php

namespace DigiTickets\Stripe\Messages;

use Stripe\StripeClient;

class RegisterWebhookRequest extends AbstractCheckoutRequest
{

    private $webhookEvent = 'checkout.session.completed';

    public function getData()
    {
        // Just validate the parameters.
        $this->validate('apiKey', 'transactionId', 'notifyUrl');

        return null;
    }

    public function sendData($data)
    {
        $stripe = new StripeClient($this->getApiKey());

        $webhook = $this->getExistingWebhook($stripe);

        if (empty($webhook) && !empty($this->getNotifyUrl())) {
            $webhook = $stripe->webhookEndpoints->create([
                'url' => $this->getNotifyUrl(),
                'enabled_events' => [
                    $this->webhookEvent,
                ],
            ])->toArray();
        }

        return $this->response = new RegisterWebhookResponse($this, $webhook);
    }

    private function getExistingWebhook(StripeClient $stripe): array
    {
        $webhooks = $stripe->webhookEndpoints->all(['limit' => 100])->data;
        $webhook = [];
        foreach ($webhooks as $hook) {
            $hook = $hook->toArray();
            if ($hook['url'] === $this->getNotifyUrl() && in_array($this->webhookEvent, $hook['enabled_events'])) {
                $webhook = $hook;
                break;
            }
        }

        return $webhook;
    }
}