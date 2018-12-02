<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\Interfaces\ICriteria;
use FcPhp\Datasource\MySQL\Interfaces\Strategies\Update\IUpdate;
use FcPhp\Datasource\MySQL\Strategies\Update\Update;
use FcPhp\Datasource\MySQL\Strategies\Select\Select;
use FcPhp\Di\Facades\DiFacade;
use FcPhp\Datasource\Factories\Factory;
use FcPhp\Datasource\MySQL\Factories\MySQLFactory;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;

class UpdateIntegrationTest extends TestCase
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
        $this->strategy = new MySQLStrategy($this->criteria, $this->factory, $this->mySQLFactory);
        $this->instance = new Update($this->strategy);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IUpdate::class, $this->instance);
    }

    public function testUpdatePriority()
    {
        $updatePriority = $this->instance->priority('LOW_PRIORITY');
        $this->assertInstanceOf(IUpdate::class, $updatePriority);
        $this->assertEquals('UPDATE LOW_PRIORITY `` AS ', $updatePriority->getSQL());
    }

    public function testUpdateTable()
    {
        $updateTable = $this->instance->from('table', 't');
        $this->assertInstanceOf(IUpdate::class, $updateTable);
        $this->assertEquals('UPDATE `table` AS t', $updateTable->getSQL());
    }

    // public function testUpdateValues()
    // {
        
    // }
}
