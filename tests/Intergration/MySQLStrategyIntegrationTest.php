<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;
use FcPhp\Datasource\MySQL\Strategies\Interfaces\IMySQLStrategy;

class MySQLStrategyIntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->instance = new MySQLStrategy();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IMySQLStrategy::class, $this->instance);
    }
}
