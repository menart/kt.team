<?php

declare(strict_types=1);

namespace UnitTests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testMainPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('kt.tream: main');
        $this->assertSelectorTextContains('a[href="/products"]', 'Список товаров');
        $this->assertSelectorTextContains('a[href="/import"]', 'Импорт товаров');
    }
}
