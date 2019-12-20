<?php

namespace DigiTickets\Stripe\Tests\Fixtures;

/**
 * This emulates a Stripe Session (which has about a million elements). We only need a public property called "id"
 * for the tests, hence emulating it.
 */
class StripeSession
{
    const SESSION_ID_1 = 'S101';

    /**
     * @var string|null
     */
    public $id;

    public function __construct(string $sessionId = null)
    {
        $this->id = $sessionId;
    }
}
