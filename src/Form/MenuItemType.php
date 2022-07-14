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
            ->add('materialIcon', null, [
                'label' => 'Icon',
                'attr' => ['class' => 'col-md-4 col-sm-12'],
            ])
            ->add('name', null, [
                'attr' => ['class' => 'col-md-4 col-sm-12'],
            ])
            ->add('position', null, [
                'attr' => ['class' => 'col-md-4 col-sm-12'],
            ])
            ->add('model', null, [
                'attr' => ['class' => 'col-md-4 col-sm-12'],
            ])
            ->add('route', ChoiceType::class, [
                'choices' => [
                    "index" => "index",
                    "new" => "new"
                ],
                'attr' => ['class' => 'col-md-4 col-sm-12']
            ])
            ->add('view', null, [
                'attr' => ['class' => 'col-md-4 col-sm-12'],
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
