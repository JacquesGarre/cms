<?php

namespace App\Form;

use App\Entity\Textarea;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('form')
            ->add('label', LabelType::class)
            ->add('name')
            ->add('placeholder')
            ->add('disabled')
            ->add('readonly')
            ->add('required')
            ->add('cols')
            ->add('rowscount')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Textarea::class,
        ]);
    }
}
