<?php

declare(strict_types=1);

namespace UnitTests\Import\XML;

use App\Exception\NotSupportedImportFileException;
use App\Import\XML\XMLImport;
use App\Manager\CategoryManager;
use App\Manager\ProductManager;
use App\Mapper\ProductMapper;
use Exception;
use UnitTests\AbstractTestCase;

class XMLImportTest extends AbstractTestCase
{
    private ProductManager $productManager;
    private CategoryManager $categoryManager;
    private string $importFilePath;
    /** @var string[] */
    private array $xmlCategory;
    /** @var string[] */
    private array $xmlProduct;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->productManager = new ProductManager($this->makeFakeProductManager(), new ProductMapper());
        $this->categoryManager = new CategoryManager($this->makeFakeCategoryManager());
        $this->importFilePath = getenv('PATH_TEST') . getenv('IMPORT_FILE_TEST');
    }


    /**
     * @throws Exception
     */
    public function testParse()
    {
        $this->createFakeImportFile(5, 3);
        $import = new XMLImport($this->categoryManager, $this->productManager, $this->importFilePath);
        $import->parse();

        $this->assertEquals(count($this->xmlProduct), count($this->getProducts()));
    }

    /**
     * @throws NotSupportedImportFileException
     * @throws Exception
     */
    public function testEmptyParse()
    {
        $this->createFakeImportFile();
        $import = new XMLImport($this->categoryManager, $this->productManager, $this->importFilePath);
        $import->parse();

        $this->assertEquals(0, count($this->getProducts()));
        $this->assertEquals(0, count($this->getCategories()));
    }

    /**
     * @throws NotSupportedImportFileException
     * @throws Exception
     */
    public function testBigParse()
    {
        $this->createFakeImportFile(2500, 20);
        $import = new XMLImport($this->categoryManager, $this->productManager, $this->importFilePath);
        $import->parse();

        $this->assertEquals(2500, count($this->getProducts()));
    }

    private function createFakeImportFile(int $countProduct = 0, int $countCategories = 0)
    {
        $this->products->clear();
        $this->categories->clear();

        $this->xmlCategory = [];
        for ($i = 0; $i < $countCategories; $i++) {
            $this->xmlCategory[] = sprintf('Category %d', $i);
        }

        $this->xmlProduct = [];
        for ($i = 1; $i < $countProduct + 1; $i++) {
            $this->xmlProduct[] = [
                'name'        => sprintf('Product %d', $i),
                'description' => sprintf('description %d', $i),
                'weight'      => sprintf('%d %s', rand(10, 300), (rand(0, 1) === 1 ? 'g' : 'kg')),
                'category'    => $this->xmlCategory[rand(0, $countCategories-1)],
            ];
        }

        $str = '<?xml version="1.0" encoding="utf-8" ?><products>' . PHP_EOL;
        foreach ($this->xmlProduct as $product) {
            $str .= '<product>';
            foreach ($product as $atribute => $value) {
                $str .= sprintf('<%1$s>%2$s</%1$s>' . PHP_EOL, $atribute, $value);
            }
            $str .= '</product>' . PHP_EOL;
        }
        $str .= '</products>';

        file_put_contents($this->importFilePath, $str);
    }

    /**
     * @throws NotSupportedImportFileException
     * @throws Exception
     */
    public function testNotFoundParse()
    {
        $this->products->clear();
        $import = new XMLImport($this->categoryManager, $this->productManager, '');
        $import->parse();

        $this->assertEquals(0, count($this->getProducts()));
    }
}
