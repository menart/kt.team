<?php

declare(strict_types=1);

namespace UnitTests\Twig;

use App\Twig\WeightExtension;
use Twig\Test\IntegrationTestCase;

class WeightExtensionTest extends IntegrationTestCase
{
    public function getExtensions(): iterable
    {
        return [
            new WeightExtension(),
        ];
    }

    protected function getFixturesDir(): string
    {
        return '/www/tests/unit/fixture/twig/';
    }
}
