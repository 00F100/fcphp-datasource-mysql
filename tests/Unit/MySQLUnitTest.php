<?php

use FcPhp\Datasource\MySQL\MySQL;
use FcPhp\Datasource\MySQL\Interfaces\IMySQL;
use PHPUnit\Framework\TestCase;

class MySQLUnitTest extends TestCase
{
    public function setUp()
    {
        $this->instance = new MySQL();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IMySQL::class, $this->instance);
    }
}
