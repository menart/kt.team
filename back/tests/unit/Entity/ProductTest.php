<?php

namespace UnitTests\Entity;

use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function productDataProvider(): array
    {
        return [
            'name' => 'in suscipit',
            'description' => 'By this time she found herself at last it unfolded its arms, took the regular course.\' '
                . '\'What was that?\' inquired Alice. \'Reeling and Writhing, of course, I meant,\' '
                . 'the King very decidedly, and he.',
            'weight' => '30 g',
            'category' => 'et',
        ];
    }

    /**
     * @dataProvider productDataProvider
     */
    public function testCreateProducts()
    {

    }
}