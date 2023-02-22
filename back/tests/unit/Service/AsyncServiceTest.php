<?php

declare(strict_types=1);

namespace UnitTests\Service;

use App\Service\AsyncService;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;

class AsyncServiceTest extends TestCase
{
    public function testPublished()
    {
        $producerInterface = $this->getMockBuilder(ProducerInterface::class)
            ->setMockClassName('SomeClassName')
            ->disableOriginalConstructor()
            ->getMock();

        $producerInterface->expects($this->once())->method('publish');

        $asyncService = new AsyncService();
        $asyncService->registerProducer(AsyncService::PARSE_DATA_FILE, $producerInterface);

        $asyncService->publishToExchange(AsyncService::PARSE_DATA_FILE, 'same_body');

        $producerInterface->expects($this->never())->method('publish');

        $asyncService->publishToExchange('other', 'same_body');
    }
}
