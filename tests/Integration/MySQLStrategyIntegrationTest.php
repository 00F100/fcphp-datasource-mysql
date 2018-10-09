<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;
use FcPhp\Datasource\MySQL\Interfaces\IMySQLStrategy;
use FcPhp\Datasource\Interfaces\ICriteria;
use FcPhp\Datasource\Interfaces\IStrategy;
use FcPhp\Datasource\Interfaces\IFactory;
use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;
use FcPhp\Di\Facades\DiFacade;
use FcPhp\Datasource\Factories\Factory;
use FcPhp\Datasource\MySQL\Factories\MySQLFactory;

class MySQLStrategyIntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->di = DiFacade::getInstance();
        $this->strategies = [];
        $this->criterias = [
            'mysql' => 'FcPhp/Datasource/MySQL/Criterias/MySQLCriteria'
        ];
        $this->methods = [
            'select' => 'FcPhp\Datasource\MySQL\Strategies\Select\Select',
        ];
        $this->criteria = 'mysql';

        $this->factory = new Factory($this->strategies, $this->criterias, $this->di);
        $this->mySQLFactory = new MySQLFactory($this->di, $this->methods);

        $this->instance = new MySQLStrategy($this->criteria, $this->factory, $this->mySQLFactory);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IMySQLStrategy::class, $this->instance);
    }

    public function testCallMethod()
    {
        $select = $this->instance->select('t.field');
        $this->assertInstanceOf(ISelect::class, $select);
    }

    /**
     * @expectedException FcPhp\Datasource\MySQL\Exceptions\InvalidMethodException
     */
    public function testJoinInvalid()
    {
        $join = $this->instance->methodtest('t.field');
    }
}
