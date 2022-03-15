<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class ProductViewEmailSubscriber implements EventSubscriberInterface
{

    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents()
    {
        return [
            'product.view' => 'sendProductViewEmail'
        ];
    }

    public function sendProductViewEmail(ProductViewEvent $productViewEvent)
    {
        $this->logger->info("Email envoyé concernant la vue du produit " . $productViewEvent->getProduct()->getId());
    }
}
