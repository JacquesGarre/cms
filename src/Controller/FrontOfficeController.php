<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FrontOfficeController extends AbstractController
{
    #[Route('/{uri<.+>}', defaults: ["uri" => ''], name: 'app_front_office')]
    public function index(Request $request, string $uri): Response
    {
        //$uri = $request->getBasePath();


        return $this->render('front_office/index.html.twig', [
            'uri' => $uri
        ]);
    }
}
