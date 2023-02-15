<?php

namespace UnitTests\Twig;

use App\Twig\WeightExtension;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Twig\Test\IntegrationTestCase;

class WeightExtensionTest extends IntegrationTestCase
{

    public function getExtensions(): iterable
    {
        return [
            new WeightExtension()
        ];
    }

    protected function getFixturesDir(): string
    {
        return '/www/tests/unit/fixture/twig/';
    }
}
