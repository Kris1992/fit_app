<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Curiosity;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;




class CuriosityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
        ;

        if ($options['is_admin']) {
            $builder
                ->add('publishedAt', null, [
                    'widget' => 'single_text',
                ])
                ->add('author', UserSelectTextType::class)
                ;
            }
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Curiosity::class,
            'is_admin' => false
        ]);
    }
}
