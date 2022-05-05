<?php

namespace DigiTickets\Stripe\Messages;

use DigiTickets\Stripe\Lib\DomainNameExtractor;
use Stripe\Exception\ApiErrorException;
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
        $webhook = [];
        if (!empty($this->getNotifyUrl())) {
            $stripe = new StripeClient($this->getApiKey());
            $webhook = $this->getExistingWebhookAndRemoveOthers($stripe);

            if (empty($webhook)) {
                $webhook = $stripe->webhookEndpoints->create([
                    'url' => $this->getNotifyUrl(),
                    'enabled_events' => [
                        $this->webhookEvent,
                    ],
                ])->toArray();
            }
        }

        return $this->response = new RegisterWebhookResponse($this, $webhook);
    }

    /**
     * Retrieve the existing webhook set up on the account (if it exists).
     * It will remove any old webhooks with the same base domain (so any that are no longer valid).
     * It will only remove these webhooks properly if your notify URL follow the following conditions (otherwise you have to remove them outside the driver if you need to):
     *  - Your base domain is 4 or more chars long (excluding tld).
     *  - You only have one subdomain element. If you have more, only the first is removed for matching purposes.
     * It will also remove any duplicate webhooks that match the domain.
     *
     * @param StripeClient $stripe
     *
     * @return array
     * @throws ApiErrorException
     */
    private function getExistingWebhookAndRemoveOthers(StripeClient $stripe): array
    {
        $newWebhookUrl = $this->getNotifyUrl();

        // Extract the base domain from the url (excluding any subdomain)
        $baseDomain = DomainNameExtractor::extractBaseDomain($newWebhookUrl);

        $webhooks = $stripe->webhookEndpoints->all(['limit' => 100])->data;
        $existingWebhook = [];
        foreach ($webhooks as $hook) {
            $hook = $hook->toArray();
            $hookUrl = $hook['url'] ?? '';
            if (in_array($this->webhookEvent, $hook['enabled_events'])) {
                if ($hookUrl === $newWebhookUrl && empty($existingWebhook)) {
                    $existingWebhook = $hook;
                } elseif (strpos($hookUrl, $baseDomain) !== false && isset($hook['id'])) {
                    // Delete any checkout.session.completed webhooks that have the same base domain as the notify URL, as they are out-of-date
                    // It will also get here if this is a duplicate webhook
                    $stripe->webhookEndpoints->delete($hook['id']);
                }
            }
        }

        return $existingWebhook;
    }
}