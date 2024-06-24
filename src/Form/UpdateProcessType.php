<?php

namespace App\Form;

use App\Entity\Process;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Doctrine\ORM\EntityRepository;

class UpdateProcessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_process', TextareaType::class, [
                'attr' => [
                    'style' => 'resize: none;',
                    'rows' => 2,
                    'class' => 'form-control',
                ],
                'trim' => false,
                'constraints' => [
                    new Length([
                        'max' => 30,
                        'maxMessage' => 'Le nom ne doit pas dépasser {{ limit }} caractères.',
                    ]),
                ],
                'label' => 'Nom du process',
            ])
            ->add('temps_minute_dossier', null, [
                'label' => 'Temps/min par dossiers',
                'constraints' => [
                    new Range([
                        'min' => 1,
                        'notInRangeMessage' => 'Le délai doit être supérieur ou égal à {{ min }} minute.',
                    ]),
                ],
                'help' => '(facultatif)'
            ])

            ->add('id_scenario', EntityType::class, [
                'class' => 'App\Entity\Scenario',
                'choice_label' => 'nom_scenario',
                'label' => 'Scenario(s) associé(s)',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Veuillez sélectionner au moins un scénario.',
                    ]),
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.nom_scenario', 'ASC');
                }
            ])

            ->add('vm_max', CheckboxType::class, [
                'label' => 'Utilisable uniquement sur une seule VM',
                'required' => false,
                'help' => '(facultatif)'
            ])

            ->add('weekdsends_and_holidays', CheckboxType::class, [
                'label' => 'Ne pas tourne pas les weeks-end et jours fériés',
                'required' => false,
                'help' => '(facultatif)'
            ])

            ->add('start_time', TimeType::class, [
                'label' => '(Facultatif) Tourne uniquement entre',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'style' => 'height: 30px; text-align: center;',
                ],
            ])

            ->add('end_time', TimeType::class, [
                'label' => 'et',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'style' => 'height: 30px; text-align: center;',
                ],
                'help' => 'Si un process tourne 24/24, ne rien saisir.'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Process::class,
        ]);
    }
}
