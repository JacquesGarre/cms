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
use App\Repository\EntityRepository;
use App\Repository\FormRepository;

class EntityFormType extends AbstractType
{
    public function __construct(EntityRepository $entityRepository, FormRepository $formRepository)
    {
        $this->entityRepository = $entityRepository;
        $this->formRepository = $formRepository;
    }

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
                'empty_data' => $field->getDefaultValue()
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

                    if($field->getSelectEntity() == 'option' || empty($field->getSelectEntity())){
                        $options['class'] = Option::class;
                        $options['choices'] = $field->getOptions();
                        $options['choice_label'] = 'text';
                        $options['choice_value'] = function (?Option $entity) {
                            return $entity ? $entity->getId() : '';
                        };
                    } else {

                        $model = $this->formRepository->find($field->getSelectEntity());
                        $entities = $this->entityRepository->findBy(['model' => $model]);
                        $class = ChoiceType::class;
                        $options['choices'] = ['' => ''];
                        foreach($entities as $index => $entity)
                        {
                            $pattern = $model->getDisplayPattern();
                            foreach($entity->getEntityMetas() as $meta)
                            {
                                $pattern = str_replace($meta->getName(), $meta->getValue(), $pattern);
                            }
                            $options['choices'][$pattern] = $entity->getId();
                        }
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
