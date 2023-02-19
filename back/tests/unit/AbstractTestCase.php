<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function getFixtureDir(): string
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.'fixture'.DIRECTORY_SEPARATOR;
    }
}
