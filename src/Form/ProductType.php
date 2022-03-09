<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Tapez le nom du produit'],
                'required' => false,
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => [
                    'placeholder' => 'Tapez une description assez courte mais parlante pour le visiteur'
                ]
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => ['placeholder' => 'Tapez le prix du produit en euros'],
                'divisor' => 100,
                'required' => false,
                // pour faire la même chose qu'avec l'évènement en bcp plus rapide ; , 'divisor' => 100
            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Tapez une URL d\'image !']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Category',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class, // on explique que les données que l'on veut récupérer viennent de la classe category
                'choice_label' => function (Category $category) { // ou 'name' qui indique que l'on veut récupérer le nom des catégories pour affichage
                    return strtoupper($category->getName()); // ici on créé une fonction pour faire un traitement sur les données (mettre en majuscule ici)
                }
            ]);
        // On utilise l'évènement crée dans la classe CentimesTransformer pour agir sur le prix
        //$builder->get('price')->addModelTransformer(new CentimesTransformer);

        // Lorsque l'on veut rajouter un évènement

        /*     $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $product = $event->getData();
                if ($product->getPrice() !== null) {
                    $product->setPrice($product->getPrice() * 100);
                }
           }); 

                $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();

                /**@var Product */

        /*
                $product = $event->getData();

                if($product->getPrice() !== null) {
                    $product->setPrice($product->getPrice() / 100);
                }

                 
                if($product->getId() === null) {       //si le produit n'existe pas déjà, on rajoute la catégorie, sinon non
                    $form->add('category', EntityType::class, [
                        'label' => 'Category',
                        'placeholder' => '-- Choisir une catégorie --',
                        'class' => Category::class, // on explique que les données que l'on veut récupérer viennent de la classe category
                        'choice_label' => function (Category $category) { // ou 'name' qui indique que l'on veut récupérer le nom des catégories pour affichage
                            return strtoupper($category->getName()); // ici on créé une fonction pour faire un traitement sur les données (mettre en majuscule ici)
                        }
                    ]);
                }
            ); */
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
