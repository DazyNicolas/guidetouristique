<?php

namespace App\EventSubscriber;

use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    private $flashy;

    public function __construct( FlashyNotifier $flashy)
    {
        $this->flashy = $flashy;
    }
    public function onLogoutEvent(LogoutEvent $event): void
    {
        //message flash

     $this->flashy->warning('Déconnexion avec susccé');
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogoutEvent',
        ];
    }
}
