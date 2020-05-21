<?php
declare(strict_types=1);

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

use App\Form\Model\Activity\MovementActivityFormModel;
use App\Form\Model\Activity\AbstractActivityFormModel;
use App\Form\Model\Activity\BodyweightActivityFormModel;
use App\Form\Model\Activity\WeightActivityFormModel;

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
            'data_class' => null,
        ]);
    }


    private function setupSpecificActivityField(FormInterface $form, ?string $type)
    {   
        $form
            ->add('energy', IntegerType::class)
            ;
        switch ($type) {
            case 'Movement':
                $form
                    ->add('intensity', ChoiceType::class, [
                        'placeholder' => 'Choose intensity',
                        'choices' => $this->getChoices('movement_intensity'),
                    ])
                    ->add('speedAverageMin', NumberType::class)
                    ->add('speedAverageMax', NumberType::class)
                ;
                break;
            case 'MovementSet':
                $form
                    ->remove('energy')//this type of activity don't need energy (Energy from sets activities will be used)
                    ;
                break;
            case 'Bodyweight':
                $form
                    ->add('repetitionsAvgMin', IntegerType::class)
                    ->add('repetitionsAvgMax', IntegerType::class)
                    ->add('intensity', ChoiceType::class, [
                        'placeholder' => 'Choose intensity',
                        'choices' => $this->getChoices('bodyweight_intensity'),
                    ])
                    ;
                break;
            case 'Weight':
                $form
                    ->add('repetitionsAvgMin', IntegerType::class)
                    ->add('repetitionsAvgMax', IntegerType::class)
                    ->add('weightAvgMin', NumberType::class)   
                    ->add('weightAvgMax', NumberType::class)   
                ;
        }
    }

    private function getChoices(string $fieldName)
    {   
        switch ($fieldName) {
            case 'activity_type':
                return AbstractActivityFormModel::getAvaibleTypes();
            case 'movement_intensity':
                return MovementActivityFormModel::getAvaibleIntensities();
            case 'bodyweight_intensity':
                return BodyweightActivityFormModel::getAvaibleIntensities();
        }
    }

}
