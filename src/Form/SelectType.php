<?php

namespace App\Form;

use App\Entity\Select;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('form')
            ->add('label')
            ->add('name')
            ->add('disabled')
            ->add('multiple')
            ->add('required')
            ->add('options')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Select::class,
        ]);
    }
}
