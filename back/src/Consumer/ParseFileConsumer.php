<?php

namespace App\Consumer;

use App\Exception\NotSupportedImportFileException;
use App\Import\ImportFactory;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Cache\CacheItemPoolInterface;

class ParseFileConsumer implements ConsumerInterface
{
    private ImportFactory $importFactory;

    /**
     * @param ImportFactory $importFactory
     */
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
        } catch (\JsonException $e) {
            return $this->reject($e->getMessage());
        }

        $pathFile = $message->getPathFile();
        $import = $this->importFactory->getInstance($pathFile);
        $import->parse($pathFile);

        return self::MSG_ACK;
    }

    private function reject(string $error): int
    {
        echo "Incorrect message: $error";

        return self::MSG_REJECT;
    }
}