<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\Interfaces\ICriteria;
use FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert;
use FcPhp\Datasource\MySQL\Strategies\Insert\Insert;
use FcPhp\Datasource\MySQL\Strategies\Select\Select;
use FcPhp\Di\Facades\DiFacade;
use FcPhp\Datasource\Factories\Factory;
use FcPhp\Datasource\MySQL\Factories\MySQLFactory;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;

class InsertIntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->instance = new Insert();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IInsert::class, $this->instance);
    }

    public function testPriotiryLow()
    {
        $priority = $this->instance->priority('LOW_PRIORITY');
        $this->assertInstanceOf(IInsert::class, $priority);
        $this->assertEquals('INSERT LOW_PRIORITY INTO', $priority->getSQL());
    }

    public function testPriotiryHigh()
    {
        $priority = $this->instance->priority('HIGH_PRIORITY');
        $this->assertInstanceOf(IInsert::class, $priority);
        $this->assertEquals('INSERT HIGH_PRIORITY INTO', $priority->getSQL());
    }

    public function testPriotiryDelay()
    {
        $priority = $this->instance->priority('DELAYED');
        $this->assertInstanceOf(IInsert::class, $priority);
        $this->assertEquals('INSERT DELAYED INTO', $priority->getSQL());
    }

    public function testIgnore()
    {
        $ignore = $this->instance->ignore(true);
        $this->assertInstanceOf(IInsert::class, $ignore);
        $this->assertEquals('INSERT IGNORE INTO', $ignore->getSQL());
    }

    public function testInto()
    {
        $into = $this->instance->into(false);
        $this->assertInstanceOf(IInsert::class, $into);
        $this->assertEquals('INSERT', $into->getSQL());
    }

    public function testFrom()
    {
        $from = $this->instance->from('table');
        $this->assertInstanceOf(IInsert::class, $from);
        $this->assertEquals('INSERT INTO table', $from->getSQL());
    }

    public function testColumnSingle()
    {
        $columns = $this->instance->columns('column1');
        $this->assertInstanceOf(IInsert::class, $columns);
        $this->assertEquals('INSERT INTO (column1)', $columns->getSQL());
    }

    public function testColumns()
    {
        $columns = $this->instance->columns([
            'column1',
            'column2',
            'column3',
            'column4',
            'column5',
        ]);
        $this->assertInstanceOf(IInsert::class, $columns);
        $this->assertEquals('INSERT INTO (column1,column2,column3,column4,column5)', $columns->getSQL());
    }

    public function testValueSingle()
    {
        $values = $this->instance->columns('column1')->values('column1', 'content1');
        $this->assertInstanceOf(IInsert::class, $values);
        $this->assertEquals('INSERT INTO (column1) VALUES ("content1")', $values->getSQL());
    }

    public function testMultiColumnSingleValue()
    {
        $values = $this->instance
            ->columns(['column1', 'column2', 'column3'])
            ->values('column2', 'content2')
            ->values('column3', false)
            ->values('column1', 100);

        $this->assertInstanceOf(IInsert::class, $values);
        $this->assertEquals('INSERT INTO (column1,column2,column3) VALUES (100,"content2",0)', $values->getSQL());
    }

    public function testValues()
    {
        $values = $this->instance->columns([
            'column1',
            'column2',
            'column3',
            'column4',
            'column5',
        ])->values([
            'content1',
            100,
            true,
            'content4',
            'content5'
        ]);
        $this->assertInstanceOf(IInsert::class, $values);
        $this->assertEquals('INSERT INTO (column1,column2,column3,column4,column5) VALUES ("content1",100,1,"content4","content5")', $values->getSQL());
    }

    public function testInsertValueWithSelectAndTablesInQuery()
    {
        $di = DiFacade::getInstance();
        $strategies = [];
        $criterias = [
            'mysql' => 'FcPhp/Datasource/MySQL/Criterias/MySQLCriteria'
        ];
        $methods = [
            'select' => 'FcPhp\Datasource\MySQL\Strategies\Select\Select',
        ];
        $criteria = 'mysql';
        $factory = new Factory($strategies, $criterias, $di);
        $mySQLFactory = new MySQLFactory($di, $methods);
        $strategy = new MySQLStrategy($criteria, $factory, $mySQLFactory);

        $select = (new Select($strategy))
            ->select([
                't.field1',
                't.field2',
                't.field3',
                't.field4',
                't.field5',
            ])
            ->from('table', 't');

        $values = $this->instance->from('table2')->columns([
            'column1',
            'column2',
            'column3',
            'column4',
            'column5',
        ])->values([
            'content1',
            100,
            $select,
            'content4',
            'content5'
        ]);
        $this->assertInstanceOf(IInsert::class, $values);
        $this->assertEquals('INSERT INTO table2 (column1,column2,column3,column4,column5) VALUES (SELECT t.field1,t.field2,t.field3,t.field4,t.field5 FROM table AS t)', $values->getSQL());
        $this->assertEquals(['table2', 'table'], $values->getTablesInQuery());
    }

    public function testOnDuplicateKeySingle()
    {
        $duplicateKey = $this->instance
            ->columns(['column1', 'column2', 'column3'])
            ->values('column2', 'content2')
            ->values('column3', false)
            ->values('column1', 100)
            ->duplicateKey('column1', 50);

        $this->assertInstanceOf(IInsert::class, $duplicateKey);
        $this->assertEquals('INSERT INTO (column1,column2,column3) VALUES (100,"content2",0) ON DUPLICATE KEY UPDATE `column1` = 50', $duplicateKey->getSQL());
    }

    public function testOnDuplicateKeyMulti()
    {
        $duplicateKey = $this->instance
            ->columns(['column1', 'column2', 'column3'])
            ->values('column2', 'content2')
            ->values('column3', false)
            ->values('column1', 100)
            ->duplicateKey('column1', 50)
            ->duplicateKey('column2', 'content to update');

        $this->assertInstanceOf(IInsert::class, $duplicateKey);
        $this->assertEquals('INSERT INTO (column1,column2,column3) VALUES (100,"content2",0) ON DUPLICATE KEY UPDATE `column1` = 50,`column2` = "content to update"', $duplicateKey->getSQL());
    }

    public function testOnDuplicateKeyList()
    {
        $duplicateKeyList = [
            'column1' => 50,
            'column2' => 'content to update',
            'column3' => true
        ];

        $duplicateKey = $this->instance
            ->columns(['column1', 'column2', 'column3'])
            ->values('column2', 'content2')
            ->values('column3', false)
            ->values('column1', 100)
            ->duplicateKey($duplicateKeyList);

        $this->assertInstanceOf(IInsert::class, $duplicateKey);
        $this->assertEquals('INSERT INTO (column1,column2,column3) VALUES (100,"content2",0) ON DUPLICATE KEY UPDATE `column1` = 50,`column2` = "content to update",`column3` = 1', $duplicateKey->getSQL());
    }
}
