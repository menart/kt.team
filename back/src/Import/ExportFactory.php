<?php

namespace App\Import;

use App\Exception\NotSupportedExportFileException;
use App\Import\JSON\JSONExport;

class ExportFactory
{
    /**
     * @throws NotSupportedExportFileException
     */
    public function getInstance($fileName): AbstractExport
    {
        switch ($this->getExtension($fileName)){
            case 'json':
                return new JSONExport($fileName);
            default:
                throw new NotSupportedExportFileException();
        }
    }

    private function getExtension(string $filename): string
    {
        $array = explode(".", $filename);
        return strtolower(array_pop($array));
    }
}