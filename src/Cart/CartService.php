<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart()
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }

    public function add(int $id)
    {

        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. Si il n'existe pas encore, alors prendre un tableau vide
        //$cart = $this->session->get('cart', []);
        $cart = $this->getCart();

        // 3. Voir si le produit ($id) existe déjà dans le tableau 
        // 4. Si c'est le cas, simplement augmenter la quantité 
        // 5. Sinon, ajouter le produit avec la quantité 1
        if (array_key_exists($id, $cart)) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // 6. Enregistrer le tableau mis à jour dans la session
        $this->session->set('cart', $cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();

        //pour supprimer l'élément avec l'id sélectionné
        unset($cart[$id]);

        //on met à jour notre panier dans la session
        $this->session->set('cart', $cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        // Soit le produit est à 1 alors il faut simplement le supprimer 
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        // Soit le produit est à plus de 1, alors il faut décrémenter
        $cart[$id]--;

        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            // si il n'y a pas de produit, on veut continuer la boucle (pour éviter d'avoir une exception)
            if (!$product) {
                continue;
            }
            $total += $product->getPrice() * $qty;
        }
        return $total;
    }

    /**
     * Undocumented function
     *
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        // pour chaque produit, on aura un tab associatif : [12 => ['product' => ..., 'quantity' => qté]]
        foreach ($this->session->get('cart', []) as $id => $qty) {
            $product = $this->productRepository->find($id);
            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        return $detailedCart;
    }
}
