<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Model\Workout\WorkoutSpecificFormModel;
use App\Entity\AbstractActivity;
use App\Entity\MovementActivity;
use App\Repository\UserRepository;
use App\Repository\AbstractActivityRepository;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\Image;


class WorkoutSpecificDataFormType extends AbstractType
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
        //datatransformer zamiast zapisywać do workout name?
        //zrobić kolejną tabele workoutdata i tam wrzucić do abstract duration, startAt, energy?
        //chyba nie ma to sensu trzeba tylko weight jeszcze dodać i raczej więcej pól nie będzie 
        //potrzebnych
            ->add('activityName', ChoiceType::class, [ 
                'choices' => $this->getActivityUniqueName(),
                'placeholder' => 'Choose an activity',
                'invalid_message' => 'Invalid activity!',
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstraints
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

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var WorkoutSpecificFormModel|null $data */
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
        
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function(FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                $this->setupSpecificActivityField(
                    $form,
                    $data['type']
                );
            }
        );

        /* If we wanna do calculations here we can 
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function(FormEvent $event) {
                //$form = $event->getForm();
                $data = $event->getData();
                //$data->calculateSaveBurnoutEnergy();
                if($data->getDistance() != null) {
                    $data->calculateSaveAverageSpeed();
                }
                /*$this->setupSpecificActivityField(
                    $form,
                    $data['type']
                );*
            }
        );*/
        
        if ($isEdit) {
            $builder
                ->add('id', HiddenType::class)
                ;
        }

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
            'data_class' => WorkoutSpecificFormModel::class,
            'is_admin' => false,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                switch ($data->getType()) {
                    case 'Movement':
                        return ['Default','movement'];
                    case 'MovementSet':
                        return ['specific_sets'];
                    case 'Weight':
                        return ['Default', 'weight'];         
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

    private function setupSpecificActivityField(FormInterface $form, ?string $type)
    {
        $hinted = true;
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

        switch ($type) {
            case 'Movement':
                $form
                    ->add('distanceTotal', NumberType::class)
                ;
                break;
            case 'MovementSet':
                $form
                    ->add('movementSets', CollectionType::class, [
                        'entry_type' => MovementSetSpecificFormType::class,
                        'entry_options' => [
                            'label' => false
                        ],
                        'by_reference' => false,
                        'allow_add' => true,
                        'allow_delete' => true
                    ])
                ;
                if($form->has('distanceTotal')) {
                    $form
                        ->remove('distanceTotal')
                    ;
                }
                if($form->has('durationSecondsTotal')) {
                    $form
                        ->remove('durationSecondsTotal')
                    ;
                }
                break;
            case 'Bodyweight':
                $form
                    ->add('repetitionsTotal', IntegerType::class)
                ;
                break;    
            case 'Weight':
                $form
                    ->add('repetitionsTotal', IntegerType::class)
                    ->add('dumbbellWeight', NumberType::class)   
                ;
                break;
            default:
                $hinted = false;
                break;
        }

        if($hinted){
            $form
                ->add('type', HiddenType::class)
                ;
        }

    }

    private function getActivityUniqueName()
    {
        $uniqueNamesArray = $this->activityRepository->findUniqueNamesAlphabetical();
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
