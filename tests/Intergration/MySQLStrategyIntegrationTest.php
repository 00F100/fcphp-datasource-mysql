<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;
use FcPhp\Datasource\MySQL\Interfaces\IMySQLStrategy;
use FcPhp\Datasource\Interfaces\ICriteria;

use FcPhp\Di\Facades\DiFacade;
use FcPhp\Datasource\Factories\Factory;

class MySQLStrategyIntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->di = DiFacade::getInstance();
        $this->strategies = [];
        $this->criterias = [
            'mysql' => 'FcPhp/Datasource/MySQL/Criterias/MySQLCriteria'
        ];
        $this->criteria = 'mysql';

        $this->factory = new Factory($this->strategies, $this->criterias, $this->di);
        $this->instance = new MySQLStrategy($this->criteria, $this->factory);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IMySQLStrategy::class, $this->instance);
    }

    public function testSql()
    {
        $this->instance
            ->select('t.field')
            ->from('table', 't')
            ->join('LEFT', ['tb' => 'table2'], function(ICriteria $criteria) {
                $criteria->condition('tb.field', '=', 't.field2', true);
            })
            ->where(function(ICriteria $criteria) {
                $criteria->and(function(ICriteria $criteria) {
                    $criteria->condition('campo', '=', 500);
                    $criteria->condition('campo2', '=', 500);
                    $criteria->or(function(ICriteria $criteria) {
                        $criteria->condition('field', '=', 'value');
                        $criteria->condition('field2', '<', 'value2');
                    });
                    $criteria->condition('campo3', '=', 'abc');
                    $criteria->condition('campo3', '=', 'abc');
                    $criteria->or(function(ICriteria $criteria) {
                        $criteria->condition('field', '=', 'value');
                        $criteria->condition('field2', '<', 'value123122');
                    });
                    $criteria->condition('campo3', '=', 'abc');
                });
            });

        d($this->instance->getSQL(), true);
    }
}
