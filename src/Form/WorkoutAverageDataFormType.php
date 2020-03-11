<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\AbstractActivity;
use App\Entity\MovementActivity;
use App\Repository\UserRepository;
use App\Repository\AbstractActivityRepository;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Form\Model\Workout\WorkoutAverageFormModel;



class WorkoutAverageDataFormType extends AbstractType
{
    private $activityRepository;

    public function __construct(AbstractActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $workout = $options['data'] ?? null;
        $isEdit = $workout && $workout->getId();
        
        $builder
            ->add('activity', EntityType::class, [ 
                'class' => AbstractActivity::class, 
                'choices' => $this->activityRepository->findAllNamesAlphabetical(),
                'placeholder' => 'Choose an activity',
                'invalid_message' => 'Invalid activity!',
                'choice_label' => 
                function(AbstractActivity $activity) {
                    return $this->getActivityName($activity);
                }
            ])
            /*->add('duration', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'required' => false,
                'model_timezone' => 'UTC',//because use timestamp
                'view_timezone' => 'UTC',
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute'
                ]
            ])*/
            ->add('durationSecondsTotal', CustomTimeType::class, [
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second'
                ],
                'attr' => [
                    'class'=>'form-inline'
                ]
            ])
            ->add('startAt', DateTimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
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
            'data_class' => WorkoutAverageFormModel::class,
            'is_admin' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    /**
     * getActivityName  Function which returns string for choice activity with specific
     * for this activity data (e.g running [fast 10km/h]) 
     * @param  AbstractActivity $activity Activity to sprintf name
     * @return string
     */
    private function getActivityName(AbstractActivity $activity): string
    {
        if($activity instanceof MovementActivity){
            return sprintf('%s [%s (%d - %d km/h)]',
                $activity->getName(), 
                $activity->getIntensity(), 
                $activity->getSpeedAverageMin(),
                $activity->getSpeedAverageMax()
            );
        } 
        
        return sprintf('(%d) %s', $activity->getId(), $activity->getName());
    }
}
