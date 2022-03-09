<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {

        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas.");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     */
    public function show($slug, ProductRepository $productRepository)
    {

        $product = $productRepository->findOneBySlug([
            'slug' => $slug,
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas.");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {

        /* Utiliser Validator de façon simple
        $resultat = $validator->validate($age, [
            new LesserThanOrEqual( [
                'value' => 90,
                'message' => "L'âge doit être inférieur à {{compared_value}} mais vous avez donné {{value}}"
            ]),
            new GreaterThan( [
                'value' => 0,
                'message' => "L'âge doit être supérieur à 0"
            ])
            ]); */

        /* Utiliser validator avec des données complexes (ex: tableaux)
        $client = [
            'nom' => 'Chamla',
            'prenom' => 'Lior',
            'voiture' => [
                'marque' => 'Hyundai',
                'couleur' => 'Noire'
            ]
        ];

        $collection = new Collection([
            'nom' => new NotBlank(['message' => "Le nom ne doit pas être vide !"]),
            'prenom' => [
                new NotBlank(['message' => "Le prénom ne doit pas être vide !"]),
                new Length(['min' => 3, 'minMessage' =>"Le prénom ne doit pas faire moins de 3 caractères"])
            ],
            'voiture' => new Collection([
                'marque' => new NotBlank(['message' => "La marque de la voiture est obligatoire"]),
                'couleur' => new NotBlank(['message' => "La couleur de la voiture est obligatoire"])
            ])
        ]); 

        $resultat = $validator->validate($client, $collection); */

        /*      $product = new Product;
        $product->setName("Salut");
        $resultat = $validator->validate($product);
        
        if ($resultat->count() > 0) {
            dd("Il y a des erreurs", $resultat);
        }
        dd("Tout vas bien"); */



        $product = $productRepository->find($id);

        // avec la fonction createform, on peut passer un produit ($product) et le form travaillera sur ce produit là
        $form = $this->createForm(ProductType::class, $product);

        // permet de préremplir le formulaire avec les données du produit demandé (si on ne met pas $product plus haut)
        //$form->setData($product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // on créé l'url de redirection
            //$url = $urlGenerator->generate('product_show', [
            //    'category_slug' => $product->getCategory()->getSlug(),
            //   'slug' => $product->getSlug(),
            //]);
            // on veut renvoyer l'utilisateur sur la page du produit modifié
            //$response = new RedirectResponse($url);
            //return $response; Ou en dessous

            //prend en paramètre le nom de la route et les param nécessaires
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView,
        ]);
    }


    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        //raccourci des lignes 67 à 69
        $form = $this->createForm(ProductType::class);

        // on crée un configurateur pour le formulaire, la factory va créer un builder directement à partir du Type Product que nous avons créé
        //$builder = $factory->createBuilder(ProductType::class);
        //$form = $builder->getForm();


        //permet de vérifier la requête et de voir si il y a des infos qui nous intéressent ou pas
        $form->handleRequest($request);

        // si le form est soumis, on extrait les données sous forme de produit
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données du formulaire, le form a alors créé un nouvel objet product avec les données récupérées 
            // grâce à l'option mise dans le builder qui comprend que les données soumises sont celles de la classe Product
            $product = $form->getData();
            // on créé le slug
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            // on enregistre le produit dans la BDD
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView,
        ]);
    }
}
