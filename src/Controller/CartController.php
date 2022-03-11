<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $productRepository, CartService $cartService, FlashBagInterface $flashBag, Request $request)
    {
        // 0. Sécurisation : est ce que le produit existe ?
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $cartService->add($id);

        // mise en place de flashBag
        $flashBag->add('success', "Le produit a bien été ajouté au panier");
        // ou si on utilise pas la FlashBagInterface, on peut faire directement (grâce à l'héritage de l'AbstractController) : 
        // $this->addFlash('success', "Le produit a bien été ajouté au panier");

        // l'info returnToCart a été mise dans index.html.twig de Cart
        if($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }


        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug(),
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(CartService $cartService)
    {

        $detailedCart = $cartService->getDetailedCartItems();
        $total = $cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id": "\d+"})
     */
    public function delete($id, ProductRepository $productRepository, CartService $cartService)
    {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut donc pas être supprimé !");
        }

        $cartService->remove($id);

        $this->addFlash('success', "Le produit a bien été supprimé du panier");

        return $this->redirectToRoute("cart_show");
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id": "\d+"})
     */
    public function decrement($id, CartService $cartService, ProductRepository $productRepository) {

        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas et ne peut donc pas être supprimé !");
        }
        $cartService->decrement($id);

        $this->addFlash('success', "Le produit a bien été décrémenté");

        return $this->redirectToRoute("cart_show");
    }
}
