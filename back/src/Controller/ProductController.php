<?php

namespace App\Controller;

use App\Manager\ProductManager;
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
    public function index(): Response
    {
        return $this->render('product.twig',
            [
                'title' => 'product',
                'rows' => $this->productManager->getProducts()
            ]);
    }
}