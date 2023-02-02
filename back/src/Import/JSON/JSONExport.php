<?php

namespace App\Import\JSON;

use App\Import\AbstractExport;
use Doctrine\Common\Collections\ArrayCollection;

class JSONExport extends AbstractExport
{
    public function save(ArrayCollection $exportData): int
    {
        $fileResource = fopen($this->fileName, 'w+');
        $count = 0;
        foreach ($exportData as $data) {
            $json = json_encode($data, JSON_FORCE_OBJECT, 512) . PHP_EOL;
            fwrite($fileResource, $json);
            $count++;
        }
        fclose($fileResource);
        return $count;
    }

}