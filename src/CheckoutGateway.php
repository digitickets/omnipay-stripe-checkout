<?php

namespace DigiTickets\Stripe;

use Omnipay\Common\AbstractGateway;

class CheckoutGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Stripe (Checkout)';
    }
}
