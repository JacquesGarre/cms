<?php

namespace App\Form;

use App\Entity\MenuItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class MenuItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $builder
            ->add('name')
            ->add('link')
            ->add('position')
            ->add('model')
            ->add('route', ChoiceType::class, [
                'choices' => [
                    "" => "",
                    "index" => "index",
                    "new" => "new"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MenuItem::class,
        ]);
    }
}
