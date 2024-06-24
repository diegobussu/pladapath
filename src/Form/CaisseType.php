<?php

namespace App\Form;

use App\Entity\Caisse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CaisseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_caisse', TextareaType::class, [
                'attr' => [
                    'style' => 'resize: none;',
                    'rows' => 1, 
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le contenu ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('num_caisse', TextareaType::class, [
                'attr' => [
                    'style' => 'resize: none;',
                    'rows' => 1, 
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Length([
                        'max' => 10,
                        'maxMessage' => 'Le contenu ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Caisse::class,
        ]);
    }

}
