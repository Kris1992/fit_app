<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\MovementActivity;
use App\Repository\UserRepository;
use App\Repository\AbstractActivityRepository;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class WorkoutWithMapFormType extends AbstractType
{
    private $activityRepository;

    public function __construct(AbstractActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('activityName', ChoiceType::class, [ 
                'choices' => $this->getMovementActivityUniqueName(),
                'placeholder' => 'Choose an activity',
                'invalid_message' => 'Invalid activity!',
            ])
            ->add('startAt', DateTimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
            ])
            ->add('durationSecondsTotal', CustomTimeType::class, [
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second'
                ],
                'attr' => [
                    'class'=>'form-inline'
                    ]
                ])
            ->add('type', HiddenType::class, [
                'data' => 'Movement',
                'attr' => [
                    'readonly' => true,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkoutSpecificFormModel::class,
            'validation_groups' => ['route_map']
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    private function getMovementActivityUniqueName()
    {
        $uniqueNamesArray = $this->activityRepository->findUniqueNamesAlphabeticalByType('Movement');
        if ($uniqueNamesArray) {
            foreach ($uniqueNamesArray as $key => $value) {
                $uniqueName = $uniqueNamesArray[$key]['name'];
                $uniqueNames[$uniqueName] = $uniqueName;
            }
            return $uniqueNames;
        }
        
        return null;
    }

}
