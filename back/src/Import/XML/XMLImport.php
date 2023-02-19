<?php

declare(strict_types=1);

namespace App\Import\XML;

use App\Import\AbstractImport;
use SimpleXMLElement;
use XMLReader;

/**
 * Для разбора импортируемого файла в XML
 */
class XMLImport extends AbstractImport
{
    private const NODE_PRODUCT_NAME = 'product';

    private XMLReader $XMLReader;

    public function parse(): int
    {
        if (false === file_exists($this->fileName)) {
            return 0;
        }

        $countParse = 0;
        $this->XMLReader = new XMLReader();
        $this->XMLReader->open($this->fileName);
        $this->skipRowToFisrtImplemntation(self::NODE_PRODUCT_NAME);
        while (self::NODE_PRODUCT_NAME === $this->XMLReader->name) {
            $node = new SimpleXMLElement($this->XMLReader->readOuterXml());
            $this->parseProduct($node);
            unset($node);
            $this->incCountUpload();
            $this->XMLReader->next(self::NODE_PRODUCT_NAME);
        }
        $this->XMLReader->close();
        unlink($this->fileName);
        $this->finishImport();

        return $countParse;
    }

    private function parseProduct(SimpleXMLElement $node): void
    {
        $name = strval($node->name);
        $description = strval($node->description);
        $weight = $this->parseWeight(strval($node->weight));
        $categoryName = strval($node->category);
        unset($node);
        $this->saveProduct($name, $description, $weight, $categoryName);
    }

    private function skipRowToFisrtImplemntation(string $nameFisrtImplemntation)
    {
        while ($this->XMLReader->read() && $this->XMLReader->name !== $nameFisrtImplemntation);
    }

    private function parseWeight(string $weightString): int
    {
        $split = explode(' ', $weightString);
        $digits = intval('kg' === trim($split[1] ?? 'kg') ? 1000 : 1);

        return intval($split[0]) * $digits;
    }
}
