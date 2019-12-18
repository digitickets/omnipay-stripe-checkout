<?php

namespace DigiTickets\Stripe\Messages;

use DigiTickets\Stripe\Lib\ComplexTransactionRef;

/**
 * This is the request to refund a payment.
 * The Stripe API allows you to specify an amount, but this functionality hasn't been implemented; currently, it will
 * always refund a payment in full.
 */
class RefundRequest extends AbstractCheckoutRequest
{
    public function getData()
    {
        // Just validate the parameters.
        $this->validate('apiKey', 'transactionReference');

        return null;
    }

    public function sendData($data)
    {
        // We use Stripe's SDK to initialise a (Stripe) session.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        // Initiate the refund. The payment intent id is the transaction reference from the original payment.
        // Now, the transaction reference is assumed to be a JSON string containing the session and actual transaction
        // ref, so we use the ComplexTransactionRef object to extract it.
        try {
            $refund = \Stripe\Refund::create(
                [
                    'payment_intent' => ComplexTransactionRef::buildFromJson(
                            $this->getTransactionReference()
                        )->getTransactionReference(),
                ]
            );
        } catch (\Exception $e) {
            // Stripe wasn't happy about something. In theory, the exception will be a subclass of
            // \Stripe\Exception\ApiErrorException, but there's no harm in catching every exception, because the
            // response object only needs the message.
            $refund = $e;
        }

        return $this->response = new RefundResponse($this, ['refund' => $refund]);
    }
}
