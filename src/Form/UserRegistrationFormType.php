<?php

namespace App\Form;

use App\Form\Model\UserRegistrationFormModel;

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

use Symfony\Component\Validator\Constraints\Image;




class UserRegistrationFormType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $user = $options['data'] ?? null;
        $isEdit = $user && $user->getId();
        //dd($isEdit);

        $imageConstraints = [
            new Image([
                'maxSize' => '5M'
            ])
        ];

        $builder
            ->add('email', EmailType::class)
            ->add('firstName', TextType::class)
            ->add('secondName', TextType::class)
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
             $builder->add('id', HiddenType::class);
        }

       
            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationFormModel::class
        ]);
    }
}
