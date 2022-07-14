<?php

namespace App\Form;

use App\Entity\IndexColumn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Attribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\FormRepository;

class IndexColumnType extends AbstractType
{
    public function __construct(FormRepository $formRepository)
    {
        $this->formRepository = $formRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   
        $model = $options['data']->getView()->getModel();
        $fields = $model->getAttributes();
        
        foreach($model->getAttributes() as $field){
            if($field->getSelectEntity() !== 'option' && !empty($field->getSelectEntity())){
                $fieldEntity = $this->formRepository->find($field->getSelectEntity());
                foreach($fieldEntity->getAttributes() as $externalField){
                    $fields->add($externalField);
                }
            }
        }

        $builder
            ->add('field', EntityType::class, [
                'class' => Attribute::class,
                'choice_label' => 'label',
                'choices' => $fields,
                'group_by' => function(Attribute $attribute, $key, $value) {
                    return $attribute->getForm()->getName();
                }
            ])
            ->add('name', null, [
                'label' => 'Label (Leave blank to use default field label)'
            ])
            ->add('position')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => IndexColumn::class,
        ]);
    }
}
