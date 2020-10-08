<?php

namespace DigiTickets\Stripe\Messages;

class PurchaseRequest extends AbstractCheckoutRequest
{
    private function nullIfEmpty(string $value = null)
    {
        return empty($value) ? null : $value;
    }

    public function getData()
    {
        // Just validate the parameters.
        $this->validate('apiKey', 'transactionId', 'returnUrl', 'cancelUrl');

        return null;
    }

    public function sendData($data)
    {
        // We use Stripe's SDK to initialise a (Stripe) session. The session gets passed through the process and is
        // used to identify this transaction.
        \Stripe\Stripe::setApiKey($this->getApiKey());

        // Initiate the session.
        // Unfortunately (and very, very annoyingly), the API does not allow negative- or zero value items in the
        // cart, so we have to filter them out (and re-index them) before we build the line items array.
        // Beware because the amount the customer pays is the sum of the values of the remaining items, so if you
        // supply negative-valued items, they will NOT be deducted from the payment amount.
        $session = \Stripe\Checkout\Session::create(
            [
                'client_reference_id' => $this->getTransactionId(),
                'payment_method_types' => ['card'],
                'payment_intent_data' => [
                    'description' => $this->getDescription(),
                ],
                'line_items' => array_map(
                    function (\Omnipay\Common\Item $item) {
                        return [
                            'name' => $item->getName(),
                            'description' => $this->nullIfEmpty($item->getDescription()),
                            'amount' => (int)(100 * $item->getPrice()), // @TODO: The multiplier depends on the currency
                            'currency' => $this->getCurrency(),
                            'quantity' => $item->getQuantity(),
                        ];
                    },
                    array_values(
                        array_filter(
                            $this->getItems()->all(),
                            function (\Omnipay\Common\Item $item) {
                                return $item->getPrice() > 0;
                            }
                        )
                    )
                ),
                'success_url' => $this->getReturnUrl(),
                'cancel_url' => $this->getCancelUrl(),
            ]
        );

        return $this->response = new PurchaseResponse($this, ['session' => $session]);
    }
}
