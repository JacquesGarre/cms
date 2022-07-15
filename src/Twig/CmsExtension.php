<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Repository\FrontOfficeTemplateRepository;
use App\Repository\IndexRepository;
use App\Repository\EntityRepository;

class CmsExtension extends AbstractExtension
{
    public function __construct(
        FrontOfficeTemplateRepository $templatesRepository,
        IndexRepository $indexRepository,
        EntityRepository $entityRepository,
        \Twig\Environment $twig
    )
    {   
        $this->twig = $twig;
        $this->templatesRepository = $templatesRepository;
        $this->indexRepository = $indexRepository;
        $this->entityRepository = $entityRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'getTemplate', 
                [$this, 'getTemplate'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'getView', 
                [$this, 'getView'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getTemplate($name, $tags = [])
    {
        $template = $this->templatesRepository->findOneBy(['name' => $name]);
        $htmlTemplate = $this->twig->createTemplate($template->getHtml());

        if(empty($template)){
            return '<h2>Template '.$name.' not found. Are you sure you spelled it right?</h2>';
        } else {
            return $htmlTemplate->render($tags);
        }
    }

    public function getView($name)
    {
        $view = $this->indexRepository->findOneBy(['name' => $name]);
        $entities = $this->entityRepository->findByView($view);
        if(empty($view)){
            return '<h2>View '.$name.' not found. Are you sure you spelled it right?</h2>';
        } else {
            return $entities;
        }
    }
}
