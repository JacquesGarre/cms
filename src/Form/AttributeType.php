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
            ->add('selectEntity', ChoiceType::class, [
                'choices' => $selectEntities,
                'required' => false
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
