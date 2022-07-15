<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FrontOfficePageRepository;
use App\Repository\EntityRepository;
use App\Repository\FormRepository;
use App\Controller\FrontOfficeTemplateController; 

class FrontOfficeController extends AbstractController
{
    public function __construct(\Twig\Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/{uri<.+>}', defaults: ["uri" => ''], name: 'app_front_office')]
    public function index(
        Request $request, 
        string $uri,
        FrontOfficePageRepository $frontOfficePageRepository,
        EntityRepository $entityRepository,
        FormRepository $formRepository
    ): Response 
    {   
        $tags = [];
        if(empty($uri)){
            $uri = '/';
        }
        $page = $frontOfficePageRepository->findOneBy(['uri' => $uri]);
        $htmlTemplate = $this->twig->createTemplate($page->getTemplate()->getHtml());

        if(!empty($request->get('id')) && !empty($request->get('model'))){
            $id = $request->get('id');
            $modelName = $request->get('model');

            $model = $formRepository->findOneBy(['name' => $modelName]);
            $entity = $entityRepository->findOneBy([
                'id' => $id,
                'model' => $model
            ]);
            $tags['entity'] = $entity;
        }


        return $this->render('front_office_page/show.html.twig', [
            'page' => $page,
            'front_office_template' => $page->getTemplate(),
            'html_template' => $htmlTemplate->render($tags)
        ]);  
    }
}
