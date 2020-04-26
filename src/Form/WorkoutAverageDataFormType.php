<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\AbstractActivity;
use App\Entity\MovementActivity;
use App\Entity\BodyweightActivity;
use App\Entity\WeightActivity;
use App\Repository\AbstractActivityRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Form\Model\Workout\WorkoutAverageFormModel;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Image;

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

        $imageConstraints = [
            new Image([
                'maxSize' => '5M',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                    'image/gif'
                ]

            ])
        ];

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
            ->add('startAt', DateTimeType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker'],
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstraints
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var WorkoutAverageFormModel|null $data */
                $data = $event->getData();

                if (!$data) {
                    return;
                }

                $this->setupSetsField(
                    $event->getForm(),
                    $data->getType()
                );
            }
        );
        
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $this->setupSetsField(
                    $form,
                    $data['type']
                );
            }
        );

        if ($isEdit) {
            $builder
                ->add('id', HiddenType::class)
                ;
        }
        if ($options['is_admin']) {
            $builder
                ->add('user', UserSelectTextType::class, [
                    'disabled' => $isEdit
                ])
                ;
        }
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkoutAverageFormModel::class,
            'is_admin' => false,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                switch ($data->getType()) {
                    case 'MovementSet':
                        return ['average_sets'];         
                    default:
                        return ['Default'];
                }
            },
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

    private function setupSetsField(FormInterface $form, ?string $type)
    {   
        switch ($type) {
            case 'MovementSet':
                $form
                    ->add('movementSets', CollectionType::class, [
                        'entry_type' => MovementSetAverageFormType::class,
                        'entry_options' => [
                            'label' => false
                        ],
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true
                    ])
                ;
                //If we edit from other activities to movement set we need to remove that field
                if($form->has('durationSecondsTotal')) {
                    $form
                        ->remove('durationSecondsTotal')
                        ;
                }

                break;
            default:
            //Activities with sets don't needed that field
                $form
                    ->add('durationSecondsTotal', CustomTimeType::class, [
                        'placeholder' => [
                            'hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second'
                        ],
                        'attr' => [
                            'class'=>'form-inline'
                        ]
                    ])
                    ;
                break;
        }

        $form
            ->add('type', HiddenType::class)
        ;
    }

    /**
     * getActivityName  Function which returns string for choice activity with specific
     * for this activity data (e.g running [fast 10km/h]) 
     * @param  AbstractActivity $activity Activity to sprintf name
     * @return string
     */
    private function getActivityName(AbstractActivity $activity): string
    {
        if($activity instanceof MovementActivity) {
            return sprintf('%s [%s (%d - %d km/h)]',
                $activity->getName(), 
                $activity->getIntensity(), 
                $activity->getSpeedAverageMin(),
                $activity->getSpeedAverageMax()
            );
        } 
        if($activity instanceof BodyweightActivity) {
            return sprintf('%s [%s (%d - %d repeats)]',
                $activity->getName(), 
                $activity->getIntensity(), 
                $activity->getRepetitionsAvgMin(),
                $activity->getRepetitionsAvgMax()
            );
        } 
        if($activity instanceof WeightActivity) {
            return sprintf('%s [%d-%d kg (%d - %d repeats)]',
                $activity->getName(),  
                $activity->getWeightAvgMin(),
                $activity->getWeightAvgMax(),
                $activity->getRepetitionsAvgMin(),
                $activity->getRepetitionsAvgMax()
            );
        }
        
        return sprintf('(%d) %s', $activity->getId(), $activity->getName());
    }
}
