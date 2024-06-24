<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class UpdateMessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('datedebut', DateTimeType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'attr' => [
                    'style' => 'height: 30px; text-align: center;',
                ],
            ])

            ->add('datefin', DateTimeType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'attr' => [
                    'style' => 'height: 30px; text-align: center;',
                ]
            ])

            ->add('titre', TextareaType::class, [
                'attr' => [
                    'style' => 'resize: none;',
                    'rows' => 2,
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le titre ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'label' => 'Titre de l\'annonce',
            ])

            ->add('contenu', TextareaType::class, [
                'attr' => [
                    'style' => 'resize: none;',
                    'rows' => 5, 
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'Le contenu ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'trim' => false,
            ])

            ->add('priorite', ChoiceType::class, [
                'label' => 'Priorité',
                'choices' => [
                    'Important' => 'important',
                    'Urgent' => 'urgent',
                    'Normal' => 'normal',
                ],
                'expanded' => false,
                'multiple' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
