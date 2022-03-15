<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Event\PurchaseSuccessEvent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{

    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * @IsGranted("ROLE_USER")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService, EventDispatcherInterface $dispatcher)
    {

        // 1. Je récupère la commande 
        $purchase = $purchaseRepository->find($id);

        // si pas de purchase ou si l'utilisateur de la commande n'est pas le même que celui qui est connecté
        // ou si la purchase a déjà un status PAID
        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "La commande n'existe pas");
            return $this->redirectToRoute('purchase_index');
        }

        // 2. Je la fais passer au status PAYEE (PAID)
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        // 3. je vide le panier 
        $cartService->empty();

        // lancer un évènement qui permette aux autres développeurs de réagir à la prise d'une commande
        $purchaseEvent = new PurchaseSuccessEvent($purchase);
        $dispatcher->dispatch($purchaseEvent, 'purchase.success');

        //4. je redirige avec un flash vers la liste des commandes
        $this->addFlash('success', "La commande a été payée et confirmée !");
        return $this->redirectToRoute('purchase_index');


    }
}
