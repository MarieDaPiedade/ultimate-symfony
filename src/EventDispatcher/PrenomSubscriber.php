<?php

namespace App\EventDispatcher;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PrenomSubscriber implements EventSubscriberInterface{

    public static function getSubscribedEvents() {

        return [
            'kernel.request' => 'addPrenomToAttributes',
        ];
    }


    public function addPrenomToAttributes(RequestEvent $requestEvent) {
        $requestEvent->getRequest()->attributes->set('prenom','Marie');
    }

    public function test1() {
        return;
    }

    public function test2() {
        return;
    }
}