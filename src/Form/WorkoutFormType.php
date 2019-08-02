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
            ->add('user', UserSelectTextType::class, [
                'disabled' => $isEdit
            ])
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
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute'
                ]
            ])
        ;
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Workout::class
        ]);
    }
}
