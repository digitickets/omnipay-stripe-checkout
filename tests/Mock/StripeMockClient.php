<?php

namespace Omnipay\StripeTests\Mock;

use \Stripe\HttpClient\ClientInterface;

class StripeMockClient implements ClientInterface
{
    private $response_body = "";

    public function request($method, $url, $headers, $params, $hasFile)
    {
        return [$this->response_body, 200, ['Content-Type' => 'application/json']];
    }

    public function setMockResponseBody($response_body) {
        $this->response_body = $response_body;
    }
}