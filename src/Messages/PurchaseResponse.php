<?php

namespace DigiTickets\Stripe\Messages;

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
\DigiTickets\Applications\Commands\Personal\Debug::log('Creating a new purchase response');
        parent::__construct($request, $data);

        if (isset($data['session'])) {
            $this->setSession($data['session']);
        }
    }

    public function setSession(Session $session)
    {
\DigiTickets\Applications\Commands\Personal\Debug::log('Setting session in response; id is: '.$session->id);
        $this->session = $session;
    }

    public function getSessionID()
    {
\DigiTickets\Applications\Commands\Personal\Debug::log('Being asked for session id...');
if ($this->session) {
    \DigiTickets\Applications\Commands\Personal\Debug::log('It has id: '.$this->session->id);
} else {
    \DigiTickets\Applications\Commands\Personal\Debug::log('We do not seem to have one');
}

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
\DigiTickets\Applications\Commands\Personal\Debug::log('Purchase response - NOT redirecting');
        // We explicitly do nothing here.
    }

    public function getTransactionReference()
    {
        return $this->getSessionID();
    }
}
