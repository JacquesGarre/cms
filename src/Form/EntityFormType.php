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
use Symfony\Bridge\Doctrine\Form\Type\EntityType as EntityFieldType;
use App\Entity\Option;

class EntityFormType extends AbstractType
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
                    $class = EntityFieldType::class;
                    $options['class'] = Option::class;
                    $options['choice_label'] = 'text';
                    $options['choice_value'] = function (?Option $entity) {
                        return $entity ? $entity->getId() : '';
                    };
                    $options['choices'] = $field->getOptions();
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
