<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\User\UserInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('EMAIL', EmailType::class, [  // ⚠️ correspond à ton entité
                'label' => 'Adresse e-mail',
                'attr' => ['class' => 'form-control'],
                'row_attr' => ['class' => 'mb-3'],
            ])
           
            
            // 🔴 Champ mot de passe sécurisé
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false, // important pour ne pas écraser directement le mot de passe
                'required' => false,
                'first_options' => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => ['class' => 'form-control'],
                    'row_attr' => ['class' => 'mb-3'],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['class' => 'form-control'],
                    'row_attr' => ['class' => 'mb-3'],
                ],
            ]);

        // ✅ Ajout du champ "roles" uniquement si l'utilisateur connecté est admin
        /** @var UserInterface|null $currentUser */
        $currentUser = $options['current_user'] ?? null;

        if ($currentUser && in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,
                'multiple' => true,
                'row_attr' => ['class' => 'mb-3'],
                'label_attr' => ['class' => 'form-check-label'],
                'choice_attr' => fn () => ['class' => 'form-check-input'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'current_user' => null,
        ]);
    }
}
