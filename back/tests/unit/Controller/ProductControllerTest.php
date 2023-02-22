<?php

declare(strict_types=1);

namespace UnitTests\Controller;

use App\Controller\ProductController;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ProductControllerTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testProductControllerIndex()
    {
        $productManager = $this->getMockBuilder(ProductManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoryManager = $this->getMockBuilder(CategoryManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $productController = $this
            ->getMockBuilder(ProductController::class)
            ->setConstructorArgs([$productManager, $categoryManager])
            ->onlyMethods(['render'])
            ->getMock();

        $request = new Request();
        $request->request->set('page', 0);
        $request->request->set('per-page',20);
        $request->request->set('filter', '{"category":[],"weightMin":10,"weightMax":1000}');
        $request->request->set('query', 'test');

        $productManager->expects($this->once())->method('getProducts');
        $productManager->expects($this->once())->method('getCountPage');
        $categoryManager->expects($this->once())->method('getAll');

        $productController->index($request);
    }
}
