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
        $session = \Stripe\Checkout\Session::create(
            [
                'client_reference_id' => $this->getTransactionId(),
                'payment_method_types' => ['card'],
                'line_items' => array_map(
                    function(\Omnipay\Common\Item $item) {
                        return [
                            'name' => $item->getName(),
                            'description' => $this->nullIfEmpty($item->getDescription()),
                            'amount' => 100*$item->getPrice(), // @TODO: The multiplier depends on the currency
                            'currency' => $this->getCurrency(),
                            'quantity' => $item->getQuantity(),
                        ];
                    },
                    $this->getItems()->all()
                ),
                'success_url' => $this->getReturnUrl(),
                'cancel_url' => $this->getCancelUrl(),
            ]
        );

        return $this->response = new PurchaseResponse($this, ['session' => $session]);
    }
}
