<?php

namespace App\Form;

use App\Entity\Index;
use App\Entity\IndexColumn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\IndexColumnRepository;
use App\Repository\IndexRepository;
use Doctrine\Common\Collections\ArrayCollection;

class IndexType extends AbstractType
{
    public function __construct(IndexColumnRepository $columnsRepository, IndexRepository $indexRepository)
    {   
        $this->indexRepository = $indexRepository;
        $this->columnsRepository = $columnsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        if(empty($options['data']->getId())){
            $columns = new ArrayCollection();
        } else {
            $view = $this->indexRepository->find($options['data']->getId());
            $columns = $this->columnsRepository->findBy(['view' => $view]);
        }

        $builder
            ->add('name')
            ->add('pagination')
            ->add('orderBy', EntityType::class, [
                'class' => IndexColumn::class,
                'choices' => $columns
            ])
            ->add('orderDirection', ChoiceType::class, [
                'choices' => [
                    'Ascending' => 'ASC',
                    'Descending' => 'DESC'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Index::class,
        ]);
    }
}
