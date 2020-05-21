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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use App\Form\Model\Challenge\ChallengeFormModel;
use App\Form\Model\Activity\AbstractActivityFormModel;
use App\Repository\AbstractActivityRepository;


class ChallengeFormType extends AbstractType
{
    private $activityRepository;

    public function __construct(AbstractActivityRepository $activityRepository)
    {
        $this->activityRepository = $activityRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $challenge = $options['data'] ?? null;
        $isEdit = $challenge && $challenge->getId();
        $activityType = $challenge ? $challenge->getActivityType() : null;

        $builder
            ->add('title', TextType::class)
            ->add('activityType', ChoiceType::class, [
                'placeholder' => 'Choose activity type',
                'choices' => $this->getChoices('activity_type'),
                'disabled' => $isEdit
            ])
            ->add('startAt', DateTimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
            ])
            ->add('stopAt', DateTimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
            ])
        ;

        if ($activityType) {
            $builder
                ->add('activityName', ChoiceType::class, [
                    'placeholder' => 'Choose activity name',
                    'choices' => $this->getChoices('activity_name', $activityType),
                ])
                ->add('goalProperty', ChoiceType::class, [
                    'placeholder' => 'Choose challenge goal',
                    'choices' => $this->getChoices('goal_property', $activityType),
                ])
                ;
        }
        

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var ChallengeFormModel|null $data */
                $data = $event->getData();
                if (!$data) {
                    return;
                }
                
                $this->setupSelectActivityField(
                    $event->getForm(),
                    $data->getActivityType()
                );
            }
        );

        $builder->get('activityType')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                $form = $event->getForm();
                $this->setupSelectActivityField(
                    $form->getParent(),
                    $form->getData()
                );
            }
        );
    }

    private function setupSelectActivityField(FormInterface $form, string $type)
    {   
        $form
            ->add('activityName', ChoiceType::class, [
                'placeholder' => 'Choose activity name',
                'choices' => $this->getChoices('activity_name', $type),
            ])
            ->add('goalProperty', ChoiceType::class, [
                'placeholder' => 'Choose challenge goal',
                'choices' => $this->getChoices('goal_property', $type),
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChallengeFormModel::class,
        ]);
    }    

    private function getChoices(string $fieldName, $type = null)
    {   
        switch ($fieldName) {
            case 'activity_type':
                return AbstractActivityFormModel::getAvaibleTypes();
            case 'activity_name':
                if ($type) {
                    return $this->getActivityUniqueNamesAlphabetical($type);
                }
            case 'goal_property':
                if ($type) {
                    return $this->getGoals($type);
                }
                          
        }
    }

    private function getActivityUniqueNamesAlphabetical($type)
    {   
        $uniqueNamesArray = $this->activityRepository->findUniqueNamesAlphabeticalByType($type);
        if ($uniqueNamesArray) {
            foreach ($uniqueNamesArray as $key => $value) {
                $uniqueName = $uniqueNamesArray[$key]['name'];
                $uniqueNames[$uniqueName] = $uniqueName;
            }
            return $uniqueNames;
        }

        return null;
    }

    private function getGoals($type)
    {   
        //$className = sprintf('App\Entity\%sActivity', $type);
        //$metadata = $this->entityManager->getClassMetadata($className);
        //$properties = $metadata->getFieldNames();
        $goalsArray = [ 
            'Most time spent' => 'durationSecondsTotal',
            'Most burnout calories' => 'burnoutEnergyTotal' 
        ];
        
        switch ($type) {
            case 'Movement':
            case 'MovementSet':
                $goalsArray['Most distance traveled'] = 'distanceTotal';
                return $goalsArray;
            case 'Weight':
            case 'Bodyweight':
                $goalsArray['Most number of repetitions'] = 'repetitionsTotal';
                return $goalsArray;
        }

        return null;
    }

}


