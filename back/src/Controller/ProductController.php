<?php

namespace App\Controller;

use App\Manager\ProductManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/product')]
class ProductController extends AbstractController
{
    private ProductManager $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    #[Route(path: '', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->request->get('page') ?? 0;
        $perPage = $request->request->get('per-page') ?? 20;
        return $this->render('product.twig',
            [
                'title' => 'product',
                'rows' => $this->productManager->getProducts($page, $perPage),
                'page' => $page,
                'perPage' => $perPage,
                'pageCount' => $this->productManager->getCountPage($perPage)
            ]);
    }
}