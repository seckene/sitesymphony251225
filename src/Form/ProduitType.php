<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control mb-3 mt-4',
                    'placeholder' => 'Entrez le nom du produit',
                ],
            ])

            ->add('descrptionlong', null, [
                'label' => 'Description longue',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Décrivez le produit…',
                    'rows' => 4,
                ],
            ])

            ->add('prix', null, [
                'label' => 'Prix (€)',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'placeholder' => 'Ex: 49.99',
                ],
            ])

            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Image du produit',
                'attr' => [
                    'class' => 'form-control mb-3',
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
                'constraints' => [
                    // ✅ IMPORTANT: plus de tableau ici
                    new File(
                        maxSize: '2M',
                        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                        mimeTypesMessage: 'Merci de choisir une image valide (JPEG, PNG, WebP).'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
