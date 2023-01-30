<?php

namespace App\Import\XML;

use App\Import\AbstractImport;
use \XMLReader;

class XMLImport extends AbstractImport
{
    private const NODE_PRODUCT_NAME = 'product';

    private XMLReader $XMLReader;


    public function parse($fileName): int
    {
        $countParse = 0;
        $this->XMLReader = new XMLReader();
        $this->XMLReader->open($fileName);
        $this->skipRowToFisrtImplemntation(self::NODE_PRODUCT_NAME);
        while ($this->XMLReader->name === self::NODE_PRODUCT_NAME) {
            $node = new \SimpleXMLElement($this->XMLReader->readOuterXml());
            $this->parseProduct($node);
            $node = null;
            $this->XMLReader->next(self::NODE_PRODUCT_NAME);
        }
        $this->XMLReader->close();
        return $countParse;
    }

    private function parseProduct(\SimpleXMLElement $node): void
    {
        $name = $node->name;
        $description = $node->description;
        $weight = intval($node->weight);
        $categoryName = $node->category;
        $this->saveProduct($name, $description, $weight, $categoryName);
    }

    private function skipRowToFisrtImplemntation(string $nameFisrtImplemntation)
    {
        while ($this->XMLReader->read() && $this->XMLReader->name !== $nameFisrtImplemntation) ;
    }
}