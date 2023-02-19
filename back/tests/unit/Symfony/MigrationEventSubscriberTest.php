<?php

namespace UnitTests\Symfony;

use App\Symfony\MigrationEventSubscriber;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MigrationEventSubscriberTest extends TestCase
{
    private EventDispatcher $dispatcher;

    public function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testPostGenerateSchema()
    {
        $listener = new MigrationEventSubscriber();
        $this->assertEquals(['postGenerateSchema'], $listener->getSubscribedEvents());
        $this->dispatcher->addListener('postGenerateSchema', [$listener, 'postGenerateSchema']);

        // dispatch your event here
        $entitytManager = \Mockery::mock(EntityManager::class);
        $schema = new Schema();

        $args = new GenerateSchemaEventArgs($entitytManager, $schema);
        $this->dispatcher->dispatch($args, 'postGenerateSchema');
        $schema = $args->getSchema();
        $this->assertTrue($schema->hasNamespace('public'));
    }
}
