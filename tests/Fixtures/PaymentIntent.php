<?php

namespace DigiTickets\StripeTests\Fixtures;

/**
 * This emulates a Stripe Payment Intent (which has about a million elements). We only need two public properties,
 * called "id" and "status" for the tests, hence emulating it.
 */
class PaymentIntent
{
    const PI_ID = 'PI201';

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $status;

    public function __construct(string $id, string $status)
    {
        $this->id = $id;
        $this->status = $status;
    }
}
