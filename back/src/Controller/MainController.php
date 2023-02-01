<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/')]
class MainController extends AbstractController
{
    #[Route(path: '', methods: ['GET'])]
    public function index(): Response
    {
        $list = [
            [
                'href' => '/products',
                'title' => 'Список товаров',
            ],
            [
                'href' => '/import',
                'title' => 'Импорт товаров',
            ],

        ];
        return $this->render('index.twig', [
           'title' => 'main',
            'list' => $list,
        ]);
    }
}