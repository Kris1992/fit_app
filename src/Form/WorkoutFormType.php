<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Workout;
use App\Entity\Activity;
use App\Repository\UserRepository;
use App\Repository\ActivityRepository;

use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;



class WorkoutFormType extends AbstractType
{
    private $activityRepository;

    public function __construct(ActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $workout = $options['data'] ?? null;
        $isEdit = $workout && $workout->getId();
        

        $builder
            ->add('activity', EntityType::class, [ 
                'class' => Activity::class, 
                'choices' => $this->activityRepository->findAllNamesAlphabetical(),
                'placeholder' => 'Choose an activity',
                'invalid_message' => 'Invalid activity!',
                'choice_label' => function(Activity $activity) {
                    return sprintf('(%d) %s', $activity->getId(), $activity->getName());
                }
            ])
            ->add('duration', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => false,
                'model_timezone' => 'UTC',//because use timestamp
                'view_timezone' => 'UTC',
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute'
                ]
            ])
            /*->add('durationSeconds', TimeType::class, [
                'input'  => 'timestamp',
                'widget' => 'choice',
                'with_seconds' => true,
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second'
                ]
            ])*/
            
            ->add('startAt', DateTimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
            ])

            ->add('durationSeconds', CustomTimeType::class, [
                //'mapped' => false,
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second'
                ],
                'attr' => [
                    'class'=>'form-inline'
                ]
            ])
        ;


        if ($options['is_admin']) {
            $builder
                ->add('user', UserSelectTextType::class, [
                    'disabled' => $isEdit
                ]);
            }
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Workout::class,
            'is_admin' => false
        ]);
    }

     public function getBlockPrefix()
    {
        return '';
    }
}
