<?php

namespace Omnipay\StripeCheckout\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Stripe\Checkout\Session;

/**
 * This payment gateway is essentially a "soft" redirect. In other words, the client makes a purchase request, then
 * displays a page which uses JavaScript to redirect.
 * The methods in this class have to reflect that.
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface {

    /**
     * @var Session|null $session
     */
    private $session = null;

    public function __construct(RequestInterface $request, $data) {
        parent::__construct($request, json_decode($data, true));

        if (isset($this->data['data']) && isset($this->data['data']['object'])
            && isset($this->data['data']['object']['object'])
            && $this->data['data']['object']['object'] == Session::OBJECT_NAME) {
            $session = new Session();
            $session->updateAttributes($this->data['data']['object']);
            $this->setSession($session);
        } else {
            throw new \InvalidArgumentException('A valid Session must be supplied');
        }
    }

    public function setSession(Session $session) {
        $this->session = $session;
    }

    public function getSessionID() {
        return $this->session ? $this->session->id : null;
    }

    public function isSuccessful() {
        return ($this->session->payment_status == $this->session::PAYMENT_STATUS_PAID
            || $this->session->payment_status == $this->session::PAYMENT_STATUS_NO_PAYMENT_REQUIRED)
            && $this->session->status == $this->session::STATUS_COMPLETE;
    }

    public function isRedirect() {
        return !empty($this->session->url);
    }

    public function getRedirectUrl(): string {
        return $this->session ? ($this->session->url ?? '') : '';
    }

    public function getRedirectMethod(): string {
        return 'GET';
    }

    public function getRedirectData(): array {
        return [];
    }

    public function redirect() {
        // We explicitly do nothing here.
    }

    public function getTransactionReference() {
        return $this->session->payment_intent;
    }
}
