<?php

namespace App\Controller;


use Twig\Environment;
use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HelloController
{

    /*     protected $logger = "";
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    } */

    /*     private $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    } */

    /**
     * @Route("/hello/{nom?World}", name="hello")
     */
    public function hello($nom, LoggerInterface $logger, Calculator $calculator, Slugify $slugify, Environment $twig)
    {
        dump($twig);
        dump($slugify->slugify("Hello World"));
        $logger->error("Mon message de log !");
        $tva = $calculator->calcul(100);
        dump($tva);
        return new Response("Hello $nom !");
    }
}
