<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Стартовый контроллер
 */
#[Route(path: '/')]
class MainController extends AbstractController
{
    #[Route(path: '', methods: ['GET'])]
    public function index(): Response
    {
        $list = [
            [
                'href'  => '/products',
                'title' => 'Список товаров',
            ],
            [
                'href'  => '/import',
                'title' => 'Импорт товаров',
            ],
        ];

        return $this->render('index.twig', [
            'title' => 'main',
            'list'  => $list,
        ]);
    }
}
