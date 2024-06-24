<?php

namespace App\Form;

use App\Entity\Scenario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UpdateScenarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_scenario', TextareaType::class, [
                'attr' => [
                    'style' => 'resize: none;',
                    'rows' => 2,
                    'class' => 'form-control',
                ],
                'trim' => false,
                'constraints' => [
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'label' => 'Nom du scénario',
                'help' => 'Si le nom contient la chaîne "puma", cela activera les étapes 2, 3 et 4.
                            Les délais en jours vont calculer les prochains traitements.'
            ])
            ->add('delai1', null, [
                'label' => 'Étape 2 (en jours)',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 365,
                        'notInRangeMessage' => 'Le délai doit être compris entre {{ min }} et {{ max }} jours.',
                    ]),
                ],
                'help' => 'Uniquement pour PUMA',
                'attr' => [
                    'style' => 'text-align: center;',
                ]
            ])
            ->add('delai2', null, [
                'label' => 'Étape 3 (en jours)',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 365,
                        'notInRangeMessage' => 'Le délai doit être compris entre {{ min }} et {{ max }} jours.',
                    ]),
                ],
                'help' => 'Uniquement pour PUMA',
                'attr' => [
                    'style' => 'text-align: center;',
                ]
            ])
            ->add('delai3', null, [
                'label' => 'Étape 4 (en jours)',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 365,
                        'notInRangeMessage' => 'Le délai doit être compris entre {{ min }} et {{ max }} jours.',
                    ]),
                ],
                'help' => 'Uniquement pour PUMA',
                'attr' => [
                    'style' => 'text-align: center;',
                ]
            ])
            ->add('periodicity', ChoiceType::class, [
                'label' => 'Périodicité',
                'choices' => [
                    'Aucune' => 'Aucune',
                    'Hebdomadaire' => 'Hebdomadaire',
                    'Mensuel' => 'Mensuel',
                    'Trimestriel' => 'Trimestriel',
                ],
                'help' => 'Uniquement pour les scénarios non PUMA.',
                'attr' => [
                    'style' => 'text-align: center;',
                ]
            ])
            ->add('until_when', null, [
                'label' => 'Sur une période (en année)',
                'constraints' => [
                    new Range([
                        'min' => 0,
                        'max' => 10,
                        'notInRangeMessage' => 'Le délai doit être compris entre {{ min }} et {{ max }} années.',
                    ]),
                ],
                'help' => 'Uniquement pour les scénarios non PUMA.',
                'attr' => [
                    'style' => 'text-align: center;',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Scenario::class,
        ]);
    }
}
