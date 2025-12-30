<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            // ================= INFOS PRINCIPALES =================
            ->add('nom', null, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control mb-3 mt-4',
                    'placeholder' => 'Nom du produit',
                ],
            ])

            ->add('descrptionlong', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'rows' => 4,
                    'placeholder' => 'Description détaillée du produit…',
                ],
            ])

            ->add('prix', null, [
                'label' => 'Prix (€)',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ex : 49.99',
                ],
            ])

            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Choisir une catégorie',
                'attr' => [
                    'class' => 'form-select mb-3',
                ],
            ])

            // ================= DETAILS PRODUIT =================
            ->add('matiere', null, [
                'label' => 'Matière',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ex : Coton premium',
                ],
            ])

            ->add('coupe', null, [
                'label' => 'Coupe',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ex : Droite / Ample',
                ],
            ])

            ->add('longueur', null, [
                'label' => 'Longueur',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ex : 145 cm',
                ],
            ])

            ->add('entretien', TextareaType::class, [
                'label' => 'Entretien',
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'rows' => 3,
                    'placeholder' => 'Ex : Lavage à 30°, pas de sèche-linge',
                ],
            ])

            // ================= IMAGE PRINCIPALE =================
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image principale',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Formats autorisés : JPEG, PNG, WebP.'
                    ),
                ],
            ])

            // ================= GALERIE (PLUSIEURS IMAGES) =================
            ->add('gallery', FileType::class, [
                'label' => 'Images supplémentaires',
                'mapped' => false,
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'form-control mb-3',
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
                'constraints' => [
                    new All(
                        constraints: [
                            new File(
                                maxSize: '5M',
                                mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                                mimeTypesMessage: 'Merci de choisir uniquement des images (jpg/png/webp).'
                            ),
                        ]
                    ),
                ],
            ])

            // ================= TAILLES / VARIANTS =================
            ->add('variants', CollectionType::class, [
                'label' => 'Tailles / Stock',
                'entry_type' => ProduitVariantType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, // IMPORTANT
                'prototype' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
