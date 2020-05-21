<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\AbstractActivityRepository;
use App\Entity\MovementActivity;
use App\Entity\AbstractActivity;
use App\Form\Model\Workout\WorkoutSet\MovementActivitySetFormModel;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class MovementSetSpecificFormType extends AbstractType
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
                'attr' => [
                    'class' => 'form-control-lg'
                ],
            ])
            ->add('distance', NumberType::class)
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
     * getMovementActivityUniqueName Gets all uniques movement activities names
     * @return array
     */
    private function getMovementActivityUniqueName()
    {
        $uniqueNamesArray = $this->activityRepository->findByTypeUniqueNamesAlphabetical('movement');
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
