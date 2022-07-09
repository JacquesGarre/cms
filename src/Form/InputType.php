<?php

namespace App\Form;

use App\Entity\Input;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class InputType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('form')
            ->add('label')
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'text' => 'text',
                    'checkbox' => 'checkbox',
                    'color' => 'color',
                    'date' => 'date',
                    'datetime-local' => 'datetime-local',
                    'email' => 'email',
                    'file' => 'file',
                    'hidden' => 'hidden',
                    'image' => 'image',
                    'month' => 'month',
                    'number' => 'number',
                    'password' => 'password',
                    'radio' => 'radio',
                    'range' => 'range',
                    'tel' => 'tel',
                    'time' => 'time',
                    'url' => 'url',
                    'week' => 'week',
                ],
            ])
            ->add('checked')
            ->add('disabled')
            ->add('placeholder', TextType::class, [
                'required' => false
            ])
            ->add('readonly')
            ->add('required')
            ->add('class')
            ->add('value')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Input::class,
        ]);
    }
}
