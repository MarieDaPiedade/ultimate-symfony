<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="homepage") 
     */
    public function homepage(ProductRepository $productRepository)
    {
        // on cherche à sélectionner 3 produits (sans critères particuliers)
        $products = $productRepository->findBy([], [], 3);

        return $this->render('home.html.twig', [
            'products' => $products    // on passe la variable products à notre template pour pouvoir utiliser les données
        ]);
    }
}
