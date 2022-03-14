<?php

namespace App\Controller\Purchase;

use DateTimeImmutable;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseLine;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchaseConfirmationController extends AbstractController
{


    protected $cartService;
    protected $em;
    protected $persister;

    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister)
    {

        $this->cartService = $cartService;
        $this->em = $em;
        $this->persister = $persister;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @IsGranted('ROLE_USER', message="Vous devez être connecté pour confirmer une commande")
     */
    public function confirm(Request $request)
    {

        // 1. Nous voulons lire les données du formulaire -> FormFactoryInterface, Request
        //$form = $this->formFactory->create(CartConfirmationType::class);
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager
        if (!$form->isSubmitted()) {
            // Message Flash puis redirection -> flashBagInterface
            //$flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation');
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');
            //return new RedirectResponse($this->router->generate('cart_show'));
            return $this->redirectToRoute('cart_show');
        }

        // si on ne met pas isGranted
        // if (!$user) {
        //     throw new AccessDeniedException('Vous devez être connecté pour confirmer une commande');
        // }

        // 4. Si il n'y a pas de produit dans mon panier : dégager -> SessionInterface, CartService
        $cartItems = $this->cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            // $flashBag->add('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            // return new RedirectResponse($this->router->generate('cart_show'));
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer une commande avec un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        // 5. Nous allons créer une purchase . Ici c'est possible car on a dit à notre formulaire de nous donner les données 
        //sous la forme d'une Purchase (cf methode configureOptions dans CartConfirmationType)
        /** @var Purchase */
        $purchase = $form->getData(); // on récupère la purchase de notre formulaire

        $this->persister->storePurchase($purchase); // on demande à l'enregistrer

        $this->cartService->empty();


        //$flashBag->add('success', 'La commande a bien été enregistrée');
        // return new RedirectResponse($this->router->generate('purchase_index'));
        $this->addFlash('success', 'La commande a bien été enregistrée');
        return $this->redirectToRoute('purchase_index');
    }
}
