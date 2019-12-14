<?php

namespace DigiTickets\Stripe;

use DigiTickets\Stripe\Messages\PurchaseRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

class CheckoutGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Stripe (Checkout)';
    }

    public function purchase(array $parameters = []): RequestInterface
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }
}
