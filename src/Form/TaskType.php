<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un titre',
                    ]),
                    new Length([
                              'min' => 3,
                              'minMessage' => "Le titre doit avoir minimum {{ limit }} caractères",
                    ]),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un contenu',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => "Le contenu doit avoir minimum {{ limit }} caractères",
                    ]),
                ],
            ])
        ;
    }
}
