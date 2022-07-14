<?php

namespace App\Form;

use App\Entity\Attribute;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\LabelType;
use App\Repository\FormRepository;
use App\Repository\AttributeRepository;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class AttributeType extends AbstractType
{
    public function __construct(FormRepository $formRepository, AttributeRepository $attributeRepository)
    {
        $this->formRepository = $formRepository;
        $this->attributeRepository = $attributeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $entities = $this->formRepository->findAll();
        $selectEntities = [
            '' => '',
            'Options' => 'option'
        ];
        foreach($entities as $entity){
            $selectEntities[$entity->getName()] = $entity->getId();
        }

        $builder
            ->add('label', LabelType::class)
            ->add('name', null, [
                'attr' => ['class' => 'col-md-6 col-sm-12'],
            ])
            ->add('placeholder', null, [
                'attr' => ['class' => 'col-md-6 col-sm-12'],
            ])
            ->add('type', ChoiceType::class, [
                'attr' => ['class' => 'col-md-6 col-sm-12'],
                'choices' => [
                    "text" => "text",
                    "textarea" => "textarea",
                    "select" => "select",
                    // "button" => "button",
                    // "checkbox" => "checkbox",
                    // "color" => "color",
                    // "date" => "date",
                    // "datetime-local" => "datetime-local",
                    // "email" => "email",
                    // "file" => "file",
                    // "hidden" => "hidden",
                    // "image" => "image",
                    // "month" => "month",
                    // "number" => "number",
                    // "password" => "password",
                    // "radio" => "radio",
                    // "range" => "range",
                    // "reset" => "reset",
                    // "search" => "search",
                    // "submit" => "submit",
                    // "tel" => "tel",
                    // "time" => "time",
                    // "url" => "url",
                    // "week" => "week",
                ]
            ])
            ->add('selectEntity', ChoiceType::class, [
                'attr' => ['class' => 'col-md-6 col-sm-12'],
                'choices' => $selectEntities,
                'required' => false
            ])
            ->add('disabled', null, [
                'attr' => ['class' => 'col-md-2 col-sm-12']
            ])
            ->add('required', null, [
                'attr' => ['class' => 'col-md-2 col-sm-12']
            ])
            ->add('checked', null, [
                'attr' => ['class' => 'col-md-2 col-sm-12']
            ])
            ->add('readonly', null, [
                'attr' => ['class' => 'col-md-2 col-sm-12']
            ])
            ->add('multiple', null, [
                'attr' => ['class' => 'col-md-2 col-sm-12']
            ])
            ->add('col', RangeType::class, [
                'label' => 'Width',
                'attr' => [
                    'min' => 1,
                    'max' => 12
                ],
                'data' => 12
            ])
            ->add('position')
            // ->add('cols')
            // ->add('height')
            ->add('defaultValue')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attribute::class,
        ]);
    }
}
