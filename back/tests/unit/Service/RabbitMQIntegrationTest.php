<?php

declare(strict_types=1);

namespace UnitTests\Service;

use ErrorException;
use OldSound\RabbitMqBundle\RabbitMq\Consumer;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RabbitMQIntegrationTest extends KernelTestCase
{
    protected AMQPMessage $mqConsumedMessage;

    protected array $mqExchangeOptions;

    protected array $mqQueueOptions;

    protected string $mqRoutingKey;

    private Consumer $mqConsumer;


    protected function setUp(): void
    {
        parent::setUp();
        $this->bootKernel();
        $this->mqRoutingKey      = 'test.created';
        $this->mqExchangeOptions = ['name' => 'old_sound_rabbit_mq.parse_data_file', 'type' => 'direct'];
        $this->mqQueueOptions    = ['name' => 'old_sound_rabbit_mq.consumer.parse_data_file'];
    }

    public function testItProducesAndConsumes()
    {
        $message = uniqid('some_body', true);

        $this->getProducer()->publish($message);

        static::assertSame($message, $this->consumeMessage()->getBody());
    }

    protected function assertRabbitMQ()
    {
        $connection = $this->getConnection();

        try {
            $connection->reconnect();
        } catch (ErrorException $e) {
            $this->fail('Connection failed: '.$e->getMessage());
        }

        static::assertTrue($connection->isConnected());
    }

    public function handleMessage(AMQPMessage $message)
    {
        $this->mqConsumedMessage = $message;
    }

    /**
     * Fetch the next message.
     *
     * @return AMQPMessage
     */
    private function consumeMessage(): AMQPMessage
    {
        try {
            $this->getConsumer()->consume(1);
        } catch (\PhpAmqpLib\Exception\AMQPTimeoutException $e) {
            $this->fail('Timeout');
        }

        return $this->mqConsumedMessage;
    }

    /**
     * Create a new consumer for this test.
     *
     * @return Consumer
     */
    private function getConsumer()
    {
        if (isset($this->mqConsumer)) {
            // Already present, so we reuse it.
            return $this->mqConsumer;
        }

        $this->assertRabbitMQ();

        $this->mqConsumer = new Consumer($this->getConnection());

        $this->mqConsumer->setCallback([$this, 'handleMessage']);
        $this->mqConsumer->setExchangeOptions($this->mqExchangeOptions);
        $this->mqConsumer->setIdleTimeout(3);
        $this->mqConsumer->setQueueOptions($this->mqQueueOptions);

        if ($this->mqRoutingKey) {
            $this->mqConsumer->setRoutingKey($this->mqRoutingKey);
        }

        return $this->mqConsumer;
    }

    protected function getProducer()
    {
        return static::$kernel->getContainer()->get('old_sound_rabbit_mq.parse_data_file_producer');
    }

    /**
     * @return object|\PhpAmqpLib\Connection\AMQPConnection
     */
    private function getConnection()
    {
        return static::$kernel->getContainer()->get('old_sound_rabbit_mq.connection.default');
    }
}
