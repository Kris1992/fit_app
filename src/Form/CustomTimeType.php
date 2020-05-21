<?php
declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use App\Form\DataTransformer\DurationToSecondsTransformer;
use Symfony\Component\Routing\RouterInterface;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\ReversedTransformer;


class CustomTimeType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                
        $hourOptions = $minuteOptions = $secondOptions = [
            'error_bubbling' => true,
            'empty_data' => '',
        ];

        // Append generic carry-along options
        foreach (['required', 'translation_domain'] as $passOpt) {
            $hourOptions[$passOpt] = $options[$passOpt];
            $minuteOptions[$passOpt] = $options[$passOpt];
            $secondOptions[$passOpt] = $options[$passOpt];
        }

        // when the form is compound the entries of the array are ignored in favor of children data
        // so we need to handle the cascade setting here
        $emptyData = $builder->getEmptyData() ?: [];

        if (isset($emptyData['hour'])) {
            $hourOptions['empty_data'] = $emptyData['hour'];
        }
        if (isset($emptyData['minute'])) {
            $minuteOptions['empty_data'] = $emptyData['minute'];
        }
        if (isset($emptyData['second'])) {
            $secondOptions['empty_data'] = $emptyData['second'];
        }


        if (isset($options['invalid_message'])) {
            $hourOptions['invalid_message'] = $options['invalid_message'];
            $minuteOptions['invalid_message'] = $options['invalid_message'];
            $secondOptions['invalid_message'] = $options['invalid_message'];
        }

        if (isset($options['invalid_message_parameters'])) {
            $hourOptions['invalid_message_parameters'] = $options['invalid_message_parameters'];
            $minuteOptions['invalid_message_parameters'] = $options['invalid_message_parameters'];
            $secondOptions['invalid_message_parameters'] = $options['invalid_message_parameters'];
        } 

        $hours = $minutes = $seconds = [];        
        foreach ($options['hours'] as $hour) {
            $hours[str_pad(strval($hour), 2, '0', STR_PAD_LEFT)] = $hour;
        }

        $hourOptions['choices'] = $hours;
        $hourOptions['placeholder'] = $options['placeholder']['hour'];
        $hourOptions['choice_translation_domain'] = $options['choice_translation_domain']['hour'];

        foreach ($options['minutes'] as $minute) {
            $minutes[str_pad(strval($minute), 2, '0', STR_PAD_LEFT)] = $minute;
        }
        
        $minuteOptions['choices'] = $minutes;
        $minuteOptions['placeholder'] = $options['placeholder']['minute'];
        $minuteOptions['choice_translation_domain'] = $options['choice_translation_domain']['minute'];

        foreach ($options['seconds'] as $second) {
            $seconds[str_pad(strval($second), 2, '0', STR_PAD_LEFT)] = $second;
        }
        
        $secondOptions['choices'] = $seconds;
        $secondOptions['placeholder'] = $options['placeholder']['second'];
        $secondOptions['choice_translation_domain'] = $options['choice_translation_domain']['second']; 

        $builder
            ->add('hour', ChoiceType::class, $hourOptions)
            ->add('minute', ChoiceType::class, $minuteOptions)
            ->add('second', ChoiceType::class, $secondOptions);
        /*$builder
            ->add('hour', ChoiceType::class, [
                'choices' => $hours,
                'placeholder' => 'Hour', 
                'label' => false,
                'required' => true
            ])
            ->add('minute', ChoiceType::class, [
                'choices' => $minutes,
                'placeholder' => 'Minute',
                'label' => false,
                'required' => true
            ])
            ->add('second', ChoiceType::class, [
                'choices' => $seconds,
                'placeholder' => 'Second',
                'label' => false,
                'required' => true
            ]);*/
        $builder->addModelTransformer(new DurationToSecondsTransformer());
    }

    public function getParent()
    {
        return FormType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $compound = function (Options $options) {
            return true;
        };

        $placeholderDefault = function (Options $options) {
            return $options['required'] ? null : '';
        };

        $placeholderNormalizer = function (Options $options, $placeholder) use ($placeholderDefault) {
            if (\is_array($placeholder)) {
                $default = $placeholderDefault($options);

                return array_merge(
                    ['hour' => $default, 'minute' => $default, 'second' => $default],
                    $placeholder
                );
            }

            return [
                'hour' => $placeholder,
                'minute' => $placeholder,
                'second' => $placeholder,
            ];
        };

        $choiceTranslationDomainNormalizer = function (Options $options, $choiceTranslationDomain) {
            if (\is_array($choiceTranslationDomain)) {
                $default = false;

                return array_replace(
                    ['hour' => $default, 'minute' => $default, 'second' => $default],
                    $choiceTranslationDomain
                );
            }

            return [
                'hour' => $choiceTranslationDomain,
                'minute' => $choiceTranslationDomain,
                'second' => $choiceTranslationDomain,
            ];
        };

        $resolver->setDefaults([
            'hours' => range(0, 23),
            'minutes' => range(0, 59),
            'seconds' => range(0, 59),
            'placeholder' => $placeholderDefault,
            'by_reference' => false,
            'error_bubbling' => false,
            'data_class' => null,
            'empty_data' => function (Options $options) {
                return $options['compound'] ? [] : '';
            },
            'compound' => $compound,
            'choice_translation_domain' => false,
        ]);

        $resolver->setNormalizer('placeholder', $placeholderNormalizer);
        $resolver->setNormalizer('choice_translation_domain', $choiceTranslationDomainNormalizer);

        $resolver->setAllowedTypes('hours', 'array');
        $resolver->setAllowedTypes('minutes', 'array');
        $resolver->setAllowedTypes('seconds', 'array');
    }
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace($view->vars, [
            'widget' => 'choice',
            'with_minutes' => true,
            'with_seconds' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()// To render time_widget
    {
        return 'time';
    }

}

