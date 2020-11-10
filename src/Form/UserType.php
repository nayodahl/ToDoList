<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\IsValidPassword;
use App\Validator\IsValidUsername;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'empty_data' => '',
                'constraints' => [
                    new IsValidUsername(),
                ],
                'attr' => ['autofocus' => true],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => true,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Merci d\'entrer un mot de passe',
                        ]),
                        new IsValidPassword(),
                    ],
                ],
                'invalid_message' => 'Les mots de passe ne sont pas identiques.',
                'mapped' => false,
                'second_options' => ['label' => 'Tapez le mot de passe à nouveau'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                  'Utilisateur' => 'ROLE_USER',
                  'Administrateur' => 'ROLE_ADMIN',
                ],
                'label' => 'Profil',
            ])
        ;

        // Data transformer, to convert array to string and string to array
        $builder->get('roles')
        ->addModelTransformer(new CallbackTransformer(
            function ($rolesAsArray) {
                // transform the array to a string
                if (null === $rolesAsArray) {
                    return implode(', ', []);
                }

                return implode(', ', $rolesAsArray);
            },
            function ($rolesAsString) {
                // transform the string back to an array
                return explode(', ', $rolesAsString);
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
