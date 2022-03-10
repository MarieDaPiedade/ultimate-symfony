<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/create.html.twig', [
            'formView' => $formView,
        ]);
    }

    // @IsGranted("ROLE_ADMIN", message="Vous n'avez pas le droit d'accéder à cette ressource")

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit($id, CategoryRepository $categoryRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Security $security)
    {
        //pour gérer l'accès à la ressource. Ici si on est pas admin, impossible d'éditer une catégory, renvoie une exception
        /*  $user = $security->getUser();
        if ($user === null) {
            return $this->redirectToRoute('security_login');
        }
        if ($security->isGranted("ROLE_ADMIN") === false) {
            throw new AccessDeniedHttpException("Vous n'avez pas le droit d'accéder à cette ressource !");
        } */

        // on retrouve le code ci-dessus, de façon plus courte en dessous : 
        //$this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez pas le droit d'accéder à cette ressource");
        // le plus simple est d'utiliser l'annotation isGranted au niveau de la route


        $category = $categoryRepository->find($id);

        if (!$category) {
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        // pour permettre de modifier une catégorie que lorsque l'on est le propriétaire de cette catégorie
        /* $user = $this->getUser();
        if(!$user) {
            return $this->redirectToRoute('security_login');
        }
        if($user !== $category->getOwner()) {
            throw new AccessDeniedHttpException("Vous n'êtes pas le propriétaire de cette catégorie");
        } */

        // $security->isGranted('CAN_EDIT', $category);
        //$this->denyAccessUnlessGranted('CAN_EDIT', $category, "Vous n'êtes pas le propriétaire de cette catégorie");

        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();
            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }
}
