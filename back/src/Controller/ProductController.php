<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\FilterDto;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Контроллер для отображения продуктов
 */
#[Route(path: '/products')]
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
        $page = intval($request->get('page') ?? 0);
        $perPage = intval($request->get('per-page') ?? 20);
        $filterDto = new FilterDto();
        $filter = json_decode($request->get('filter') ?? '[]');
        $filterDto->weightMin = intval($filter->weightMin ?? 0);
        $filterDto->weightMax = intval($filter->weightMax ?? 0);
        $filterDto->category = $filter->category ?? [];
        $filterDto->query = $request->get('query') ?? '';

        return $this->render(
            'product.twig',
            [
                'title'      => 'product',
                'rows'       => $this->productManager->getProducts($page, $perPage, $filterDto),
                'categories' => $this->categoryManager->getAll(),
                'page'       => $page,
                'perPage'    => $perPage,
                'pageCount'  => $this->productManager->getCountPage($perPage),
                'filter'     => $filterDto,
            ]
        );
    }
}
