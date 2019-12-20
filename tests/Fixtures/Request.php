<?php

namespace DigiTickets\StripeTests\Fixtures;

use Omnipay\Common\Message\RequestInterface;

/**
 * This is a mock object. It literally has to implement the interface and do nothing else.
 */
class Request implements RequestInterface
{
    public function initialize(array $parameters = array()) {
    }

    public function getParameters() {
        return [];
    }

    public function getResponse() {
    }

    public function send() {
    }

    public function sendData($data) {
    }

    public function getData() {
    }
}
