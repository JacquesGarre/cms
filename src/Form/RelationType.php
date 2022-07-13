<?php

namespace App\Form;

use App\Entity\Relation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Attribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RelationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('position')
            ->add('view')
            ->add('mappedBy', EntityType::class, [
                'class' => Attribute::class,
                'choice_label' => 'label',
                'group_by' => function(Attribute $attribute, $key, $value) {
                    return $attribute->getForm()->getName();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Relation::class,
        ]);
    }
}
