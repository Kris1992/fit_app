<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Form\Model\Curiosity\CuriosityFormModel;

class CuriosityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $curiosity = $options['data'] ?? null;
        $isEdit = $curiosity && $curiosity->getId();

        $builder
            ->add('title', TextType::class, [
                'attr' =>[
                    'placeholder' => 'Enter title of curiosity here.',
                ]
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'placeholder' => 'Enter short description of curiosity here.',
                ],
                'help' => 'This will be shown on the main page. Think of something catchy!',
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Enter content of curiosity here.',
                ],
                'required' => false
            ])
            ->add('isPublished', CheckboxType::class, [
                'required' => false,
            ])
        ;

        if ($isEdit) {
            $builder
                ->add('author', UserSelectTextType::class, [
                    'disabled' => true,
                ])
                ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CuriosityFormModel::class,
        ]);
    }
}
