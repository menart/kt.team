<?php

declare(strict_types=1);

namespace UnitTests\Consumer;

use App\Consumer\InputMessage;
use JsonException;
use PHPUnit\Framework\TestCase;

class InputMessageTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function testCreateFromQueue()
    {
        $testPath = 'test_path';
        $message = json_encode(['pathFile' => $testPath]);
        $inputMessage = InputMessage::createFromQueue($message);
        $this->assertEquals($testPath, $inputMessage->getPathFile());
    }
}
