<?php

namespace App\Form;

use App\Entity\Attribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\LabelType;

class AttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', LabelType::class)
            ->add('placeholder')
            ->add('name')
            ->add('defaultValue')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    "text" => "text",
                    "textarea" => "textarea",
                    "select" => "select",
                    "button" => "button",
                    "checkbox" => "checkbox",
                    "color" => "color",
                    "date" => "date",
                    "datetime-local" => "datetime-local",
                    "email" => "email",
                    "file" => "file",
                    "hidden" => "hidden",
                    "image" => "image",
                    "month" => "month",
                    "number" => "number",
                    "password" => "password",
                    "radio" => "radio",
                    "range" => "range",
                    "reset" => "reset",
                    "search" => "search",
                    "submit" => "submit",
                    "tel" => "tel",
                    "time" => "time",
                    "url" => "url",
                    "week" => "week",
                ]
            ])
            ->add('disabled')
            ->add('required')
            ->add('checked')
            ->add('readonly')
            ->add('multiple')
            ->add('col')
            ->add('position')
            ->add('cols')
            ->add('height')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attribute::class,
        ]);
    }
}
