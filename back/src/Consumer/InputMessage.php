<?php

namespace App\Consumer;

use Psr\Cache\CacheItemPoolInterface;

class InputMessage
{
    private string $pathFile;

    /**
     * @return string
     */
    public function getPathFile(): string
    {
        return $this->pathFile;
    }

    public static function createFromQueue(string $messageBody): self
    {
        $message = json_decode($messageBody, true, 512, JSON_THROW_ON_ERROR);
        $result = new self();
        $result->pathFile = $message['pathFile'];
        return $result;
    }

}