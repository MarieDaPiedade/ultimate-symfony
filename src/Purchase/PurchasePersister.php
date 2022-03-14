<?php

namespace App\Purchase;

use DateTimeImmutable;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseLine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister {

    protected $security;
    protected $cartService;
    protected $em;

    function __construct(Security $security, CartService $cartService, EntityManagerInterface $em) {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase) {

        // Intégrer tout ce qu'il faut et persister la purchase 

                // 6. Nous allons la lier avec l'utilisateur actuellement connecté -> Security -> SessionInterface
                $purchase->setUser($this->security->getUser())
                ->setPurchasedAt(new DateTimeImmutable())
                ->setTotal($this->cartService->getTotal());
    
            $this->em->persist($purchase);
    
    
            // 7. Nous allons la lier avec les produits qui sont dans le panier -> CartService $cartService
            foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
                $purchaseItem = new PurchaseLine;
                $purchaseItem->setPurchase($purchase)
                    ->setProduct($cartItem->product)
                    ->setProductName($cartItem->product->getName())
                    ->setQuantity($cartItem->qty)
                    ->setTotal($cartItem->getTotal())
                    ->setProductPrice($cartItem->product->getPrice());
    
                $this->em->persist($purchaseItem);
            }
    
            // 8. Nous allons enregistrer la commande -> EntityManagerInterface
            $this->em->flush();
    }
}

