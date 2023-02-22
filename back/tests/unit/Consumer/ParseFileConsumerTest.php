<?php

declare(strict_types=1);

namespace UnitTests\Consumer;

use App\Consumer\ParseFileConsumer;
use App\Import\ImportFactory;
use App\Import\XML\XMLImport;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use PHPUnit\Framework\TestCase;

class ParseFileConsumerTest extends TestCase
{
    private ImportFactory $importFactory;
    private XMLImport $XMLImport;

    public function setUp(): void
    {
        $this->importFactory = $this->getMockBuilder(ImportFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->XMLImport = $this->getMockBuilder(XMLImport::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->importFactory->method('getInstance')->willReturn($this->XMLImport);
    }

    public function testExecute()
    {
        $testPath = ['pathFile' => 'import.xml'];
        $message = new AMQPMessage();
        $message->setBody(json_encode($testPath));
        $this->XMLImport->expects($this->once())->method('parse');
        $parseFileConsumer = new ParseFileConsumer($this->importFactory);
        $result = $parseFileConsumer->execute($message);
        $this->assertEquals(ConsumerInterface::MSG_ACK, $result);
    }

    public function testErrorMessage()
    {
        $message = new AMQPMessage();
        $message->setBody('[breack json');
        $parseFileConsumer = new ParseFileConsumer($this->importFactory);
        $result = $parseFileConsumer->execute($message);
        $this->assertEquals(ConsumerInterface::MSG_REJECT, $result);
    }
}
