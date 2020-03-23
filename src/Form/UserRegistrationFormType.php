<?php

namespace App\Form;

use App\Form\Model\User\UserRegistrationFormModel;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Image;

class UserRegistrationFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $options['data'] ?? null;
        $isEdit = $user && $user->getId();

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
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class)
            ->add('secondName', TextType::class)
            ->add('gender', ChoiceType::class, [
                'choices'  => [
                    'Male' => 'Male',
                    'Female' => 'Female',
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => $imageConstraints
            ])
            ;


        if ($isEdit == false)
        {
            $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'first_options' => [ 'help' => 'Password should contain at least 2 numbers and 3 letters' ]
            ])
            ->add('agreeTerms', CheckboxType::class);
        }
        else
        {
            $builder
                ->add('id', HiddenType::class)
                ->add('birthdate', BirthdayType::class, [
                    'required' => false,
                    'placeholder' => [
                        'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                    ]
                ])
                ->add('weight', IntegerType::class, [
                    'required' => false,
                    'help' => 'Weight measured in kg.',
                ])
                ->add('height', IntegerType::class, [
                    'required' => false,
                    'help' => 'Height measured in cm.',
                ]);
        }

        if($options['is_admin'])
        {
            $builder
            ->add('role', ChoiceType::class, [
                'choices'  => [
                    'User' => 'ROLE_USER',
                    'Moderator' => 'ROLE_MODERATOR',
                    'Admin' => 'ROLE_ADMIN'
                ],
            ]);
        }   
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationFormModel::class,
            'is_admin' => false
        ]);
    }
}
