<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;


class ActivityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $activity = $options['data'] ?? null;
        $isEdit = $activity && $activity->getId();

        $builder
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Choose type',
                'choices' => $this->getChoices('activity_type'),
                'disabled' => $isEdit
            ])
            ->add('name', TextType::class)
            ->add('energy', IntegerType::class)
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var BasicActivityFormModel|null $data */
                $data = $event->getData();
                if (!$data) {
                    return;
                }
                
                $this->setupSpecificActivityField(
                    $event->getForm(),
                    $data->getType()
                );
            }
        );

        $builder->get('type')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                $form = $event->getForm();
                $this->setupSpecificActivityField(
                    $form->getParent(),
                    $form->getData()
                );
            }
        );
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null
        ]);
    }


    private function setupSpecificActivityField(FormInterface $form, ?string $type)
    {   
        switch ($type) {
            case 'Movement':
                $form
                    ->add('intensity', ChoiceType::class, [
                        'placeholder' => 'Choose intensity',
                        'choices' => $this->getChoices('movement_instensity'),
                    ])
                    ->add('speedAverage', NumberType::class, [
                        'constraints' => [
                            new NotBlank(),
                            new Range([
                                'min' => 1,
                                'max' => 20
                            ]),
                        ],
                    ])
                ;
                break;
            case 'Weight':
                $form
                    ->add('repetitions', IntegerType::class)
                    ->add('weight', NumberType::class)   
                ;
        }
    }


    private function getChoices(string $fieldName)
    {   
        switch ($fieldName) {
            case 'activity_type':
                return $choices = [
                    'Weight' => 'Weight',
                    'Movement (running, cycling etc.)' => 'Movement',
                ];
                break;
            case 'movement_instensity':
                return $choices = [
                    'Very slow' => 'Very slow',
                    'Slow' => 'Slow',
                    'Normal' => 'Normal',
                    'Fast' => 'Fast',
                    'Very fast' => 'Very fast',
                ];
                break;
        }
    }

}
