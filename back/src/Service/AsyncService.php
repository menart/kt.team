<?php

declare(strict_types=1);

namespace App\Service;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

/**
 * Класс для публикаций сообщений в RabbitMQ и асинхронной обработки файла
 */
class AsyncService
{
    public const PARSE_DATA_FILE = 'parse_data_file';

    /** @var ProducerInterface[] */
    private array $producers;

    public function __construct()
    {
        $this->producers = [];
    }

    public function registerProducer(string $producerName, ProducerInterface $producer): void
    {
        $this->producers[$producerName] = $producer;
    }

    public function publishToExchange(
        string $producerName,
        string $message,
        ?string $routingKey = null,
        ?array $additionalProperties = null
    ): bool {
        if (isset($this->producers[$producerName])) {
            $this->producers[$producerName]->publish(
                $message,
                $routingKey ?? '',
                $additionalProperties ?? []
            );

            return true;
        }

        return false;
    }
}
