<?php

namespace Omnipay\StripeCheckout\Messages;

/**
 * Class FetchRefundRequest
 *
 * @package Omnipay\AdyenPos\Message\Request
 *
 * @method FetchRefundRequest send()
 */
class FetchRefundRequest extends AbstractCheckoutRequest {

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData() {

        $this->validate('apiKey', 'transactionId');

        $data = [
            'refund' => $this->getTransactionId(),
        ];

        return $data;
    }

    public function sendData($data) {
        // Retrieve the session that would have been started earlier.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        $refund = \Stripe\Refund::retrieve($data['refund']);

        return $this->response = new FetchRefundResponse($this, ['refund' => $refund]);
    }
}
