<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        dd("Ca fonctionne");  // dd regroupe les fonctions dump and die
    }

    /**
     * @Route("/test/{age<\d+>?0}", name="test", methods={"GET", "POST"}, host="localhost", schemes={"http", "https"})
     */
    public function test(Request $request, $age) // on rentre la requête en paramètre Symfony la créé pour nous
    {
        // $request = Request::createFromGlobals(); si l'on veut créer la request

        // ici on fait une requête GET avec le parametre age. si il n'y a pas d'age de saisie dans l'url, il se met à 0 en valeur par défaut
        //$age = $request->query->get('age', 0);
        // chaque fonction du controller qui prend en charge une requête doit tjrs retourner une instance de la classe response, 
        // qui vient elle aussi du package http-foundation

        // $age = $request->attributes->get('age'); cette ligne est remplaçable par le paramètre $age
        return new Response("Vous avez $age ans !");
    }
}
