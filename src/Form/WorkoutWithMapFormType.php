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
            ->add('type', HiddenType::class, [
                'data' => 'Movement',
                'attr' => [
                    'readonly' => true,
                ],
            ])
            ;

        //Drawed on map route or real time tracking
        if ($options['is_drawing']) {
            $builder
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
            ;
        } else {
            $builder
                ->add('startAt', DateTimeType::class, [
                    'input'  => 'datetime',
                    'widget' => 'single_text',
                    'html5' => false,
                    'attr' => ['class' => 'd-none'],
                    'model_timezone' => 'UTC',
                    'view_timezone' => 'UTC',
                ])
                ->add('durationSecondsTotal', HiddenType::class)
            ;
        }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkoutSpecificFormModel::class,
            'validation_groups' => ['route_map'],
            'is_drawing' => true
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
