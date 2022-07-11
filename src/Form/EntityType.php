<?php

namespace App\Form;

use App\Entity\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Option;

class EntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $fields = $options['data']->getModel()->getAttributes();
        foreach($fields as $field){
            $options = [
                'mapped' => false,
                'label' => $field->getLabel(),
                'disabled' => $field->isDisabled(),
                'required' => $field->isRequired(),
                'attr' => [
                    'placeholder' => $field->getPlaceholder(),
                    'readonly' => $field->isReadonly(),
                ], 
                'data' => $field->getDefaultValue()
            ];
            switch($field->getType()){
                case 'text':
                    $class = TextType::class;
                break;
                case 'textarea':
                    $class = TextareaType::class;
                    $options['attr']['cols'] = $field->getCols();
                    $options['attr']['rows'] = $field->getHeight();
                break;
                case 'select':
                    $class = ChoiceType::class;
                    $options['choices'] = ['' => ''];
                    foreach($field->getOptions() as $option){
                        $options['choices'][$option->getText()] = $option->getValue();   
                    }
                break;
            }
            $builder->add($field->getName(), $class, $options);

        }

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entity::class,
        ]);
    }
}
