<?php

namespace App\Form;

use App\Entity\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EntityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $fields = $options['data']->getModel()->getAttributes();
        foreach($fields as $field){
            $options = [
                'mapped' => false,
                'label' => $field->getLabel()
            ];
            switch($field->getType()){
                case 'text':
                    $class = TextType::class;
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
