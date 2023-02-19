<?php

declare(strict_types=1);

namespace App\Consumer;

/**
 * Класс для разбора сообщения полученного из RabbitMQ
 */
class InputMessage
{
    private string $pathFile;

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
