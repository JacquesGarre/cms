<?php

namespace App\Form;

use App\Entity\IndexColumn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Attribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class IndexColumnType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $builder
            ->add('position')
            ->add('field', EntityType::class, [
                'class' => Attribute::class,
                'choice_label' => 'label',
                'choices' => $options['data']->getView()->getModel()->getAttributes()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IndexColumn::class,
        ]);
    }
}
