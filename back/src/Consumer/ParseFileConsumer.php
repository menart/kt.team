<?php

declare(strict_types=1);

namespace App\Consumer;

use App\Import\ImportFactory;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Класс для подписки на событие в RabbitMQ
 */
class ParseFileConsumer implements ConsumerInterface
{
    private ImportFactory $importFactory;

    public function __construct(ImportFactory $importFactory)
    {
        $this->importFactory = $importFactory;
    }

    /**
     * @throws NotSupportedImportFileException
     */
    public function execute(AMQPMessage $msg): int
    {
        try {
            $message = InputMessage::createFromQueue($msg->getBody());
        } catch (JsonException $e) {
            return $this->reject($e->getMessage());
        }

        $pathFile = $message->getPathFile();
        $import = $this->importFactory->getInstance($pathFile);
        $import->parse();

        return self::MSG_ACK;
    }

    private function reject(string $error): int
    {
        echo sprintf('Incorrect message: %s', $error);
        return self::MSG_REJECT;
    }
}
