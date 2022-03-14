<?php

namespace App\Controller\Exercice;


use Twig\Environment;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HelloController extends AbstractController
{
    protected $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }


    /**
     * @Route("/hello/{prenom?World}", name="hello")
     */
    public function hello($prenom)
    {
        return $this->render('hello.html.twig', [
            'prenom' => $prenom,
        ]);
    }

    /**
     * @Route("/example", name="example")
     */
    public function example() {
        return $this->render('example.html.twig', ['age' => 33]);
    }

}
