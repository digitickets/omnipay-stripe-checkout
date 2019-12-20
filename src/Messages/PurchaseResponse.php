<?php

namespace DigiTickets\Stripe\Messages;

use DigiTickets\Stripe\Lib\ComplexTransactionRef;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\Common\Message\RequestInterface;
use Stripe\Checkout\Session;

/**
 * This payment gateway is essentially a "soft" redirect. In other words, the client makes a purchase request, then
 * displays a page whch uses JavaScript to redirect.
 * The methods in this class have to reflect that.
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @var Session|null $session
     */
    private $session = null;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        if (isset($data['session']) && $data['session'] instanceof Session) {
            $this->setSession($data['session']);
        } else {
            throw new \InvalidArgumentException('A valid Session must be supplied');
        }
    }

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function getSessionID()
    {
        return $this->session ? $this->session->id : null;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectUrl(): string
    {
        return '';
    }

    public function getRedirectMethod(): string
    {
        return 'GET';
    }

    public function getRedirectData(): array
    {
        return [];
    }

    public function redirect()
    {
        // We explicitly do nothing here.
    }

    /**
     * The transation ref has to include the session and (later) the actual transaction ref. This is because the session
     * is the only thing we have that will allow us to retrieve the payment later in the process (and therefore get the
     * transaction ref.
     * We encode it as JSON.
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        return (new ComplexTransactionRef($this->getSessionID()))->asJson();
    }
}
