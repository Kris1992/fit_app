<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\AbstractActivityRepository;
use App\Entity\MovementActivity;
use App\Entity\AbstractActivity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;


class MovementSetAverageFormType extends AbstractType
{
    private $activityRepository;

    public function __construct(AbstractActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('activity', EntityType::class, [ 
                'class' => AbstractActivity::class, 
                'choices' => $this->activityRepository->findByTypeNamesAlphabetical('Movement'),
                'placeholder' => 'Choose an activity',
                'invalid_message' => 'Invalid activity!',
                'choice_label' => 
                function(AbstractActivity $activity) {
                    return $this->getActivityName($activity);
                },
                'attr' => [
                    'class' => 'form-control-lg'
                ],
            ])
            ->add('durationSeconds', CustomTimeType::class, [
                'placeholder' => [
                    'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second'
                ],
                'attr' => [
                    'class'=>'form-inline'
                ]
            ])
        ;

       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MovementActivitySetFormModel::class
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
        } else {
            throw new Exception("Wrong activity inside form", 1);
        }
    }
}
