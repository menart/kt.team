<?php

namespace App\Import;

use Doctrine\Common\Collections\ArrayCollection;

abstract class AbstractExport
{
    protected string $fileName;

    /**
     * @param string $fileName
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    abstract function save(ArrayCollection $exportData): int;

}