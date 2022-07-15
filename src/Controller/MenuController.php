<?php

namespace App\Controller;

use App\Repository\MenuItemRepository;
use App\Repository\FrontOfficePageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    public function menu(
        MenuItemRepository $menuItemRepository,
        FrontOfficePageRepository $pagesRepository
    ): Response
    {
        $items = $menuItemRepository->findBy([], ['position' => 'asc']);
        $pages = $pagesRepository->findBy([], ['id' => 'asc']);
        return $this->render('menu/index.html.twig', [
            'controller_name' => 'MenuController',
            'items' => $items,
            'pages' => $pages
        ]);
    }
}
