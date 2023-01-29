<?php

namespace App\Controller;

use App\Dto\FilterDto;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/product')]
class ProductController extends AbstractController
{
    private ProductManager $productManager;
    private CategoryManager $categoryManager;

    public function __construct(ProductManager $productManager, CategoryManager $categoryManager)
    {
        $this->productManager = $productManager;
        $this->categoryManager = $categoryManager;
    }

    #[Route(path: '', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->request->get('page') ?? 0;
        $perPage = $request->request->get('per-page') ?? 20;
        $filter = new FilterDto();
        return $this->render('product.twig',
            [
                'title' => 'product',
                'rows' => $this->productManager->getProducts($page, $perPage),
                'categories' => $this->categoryManager->getAll(),
                'page' => $page,
                'perPage' => $perPage,
                'pageCount' => $this->productManager->getCountPage($perPage),
                'filter' => $filter,
            ]);
    }
}