<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;
use FcPhp\Datasource\MySQL\Interfaces\IMySQLStrategy;
use FcPhp\Datasource\Interfaces\ICriteria;
use FcPhp\Datasource\Interfaces\IStrategy;

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

    public function testSelect()
    {
        $select = $this->instance->select('t.field');
        $this->assertInstanceOf(IStrategy::class, $select);
        $this->assertEquals('SELECT      t.field FROM  AS           ', $select->getSQL());
    }

    public function testSelectRuleDistinct()
    {
        $selectRule = $this->instance->selectRule('DISTINCT');
        $this->assertInstanceOf(IStrategy::class, $selectRule);
        $this->assertEquals('SELECT DISTINCT      FROM  AS           ', $selectRule->getSQL());
    }

    public function testSelectRuleAll()
    {
        $selectRule = $this->instance->selectRule('ALL');
        $this->assertInstanceOf(IStrategy::class, $selectRule);
        $this->assertEquals('SELECT ALL      FROM  AS           ', $selectRule->getSQL());
    }

    public function testSelectRuleDistinctRow()
    {
        $selectRule = $this->instance->selectRule('DISTINCTROW');
        $this->assertInstanceOf(IStrategy::class, $selectRule);
        $this->assertEquals('SELECT DISTINCTROW      FROM  AS           ', $selectRule->getSQL());
    }

    public function testHighPriority()
    {
        $highPriority = $this->instance->highPriority(true);
        $this->assertInstanceOf(IStrategy::class, $highPriority);
        $this->assertEquals('SELECT  HIGH_PRIORITY     FROM  AS           ', $highPriority->getSQL());
    }

    public function testStraightJoin()
    {
        $straightJoin = $this->instance->straightJoin(true);
        $this->assertInstanceOf(IStrategy::class, $straightJoin);
        $this->assertEquals('SELECT   STRAIGHT_JOIN    FROM  AS           ', $straightJoin->getSQL());
    }

    public function testSizeResultSqlSmallResult()
    {
        $sizeResult = $this->instance->sizeResult('SQL_SMALL_RESULT');
        $this->assertInstanceOf(IStrategy::class, $sizeResult);
        $this->assertEquals('SELECT    SQL_SMALL_RESULT   FROM  AS           ', $sizeResult->getSQL());
    }

    public function testSizeResultSqlBigResult()
    {
        $sizeResult = $this->instance->sizeResult('SQL_BIG_RESULT');
        $this->assertInstanceOf(IStrategy::class, $sizeResult);
        $this->assertEquals('SELECT    SQL_BIG_RESULT   FROM  AS           ', $sizeResult->getSQL());
    }

    public function testSizeResultSqlBufferResult()
    {
        $sizeResult = $this->instance->sizeResult('SQL_BUFFER_RESULT');
        $this->assertInstanceOf(IStrategy::class, $sizeResult);
        $this->assertEquals('SELECT    SQL_BUFFER_RESULT   FROM  AS           ', $sizeResult->getSQL());
    }

    public function testNoCache()
    {
        $noCache = $this->instance->noCache(true);
        $this->assertInstanceOf(IStrategy::class, $noCache);
        $this->assertEquals('SELECT     SQL_NO_CACHE  FROM  AS           ', $noCache->getSQL());
    }

    public function testSelectOne()
    {
        $select = $this->instance->select('t.field');
        $this->assertInstanceOf(IStrategy::class, $select);
        $this->assertEquals('SELECT      t.field FROM  AS           ', $select->getSQL());
    }

    public function testSelectMulti()
    {
        $select = $this->instance->select(['t.field', 't.field2']);
        $this->assertInstanceOf(IStrategy::class, $select);
        $this->assertEquals('SELECT      t.field,t.field2 FROM  AS           ', $select->getSQL());
    }

    public function testFrom()
    {
        $from = $this->instance->from('table', 't');
        $this->assertInstanceOf(IStrategy::class, $from);
        $this->assertEquals('SELECT       FROM table AS t          ', $from->getSQL());
    }

    public function testJoinLeft()
    {
        $join = $this->instance->join('LEFT', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  LEFT JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    }

    public function testJoinRight()
    {
        $join = $this->instance->join('RIGHT', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  RIGHT JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    }

    public function testJoinInner()
    {
        $join = $this->instance->join('INNER', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  INNER JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    }

    public function testJoinInnerWhere()
    {
        $join = $this->instance->join('INNER', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
            $criteria->condition('t.field2', '=', 't2.field2', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  INNER JOIN (table AS t) ON (( t.field = t2.field AND t.field2 = t2.field2 ))         ', $join->getSQL());
    }

    public function testJoinInnerMultiTable()
    {
        $join = $this->instance->join('INNER', ['t' => 'table', 't2' => 'table2'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
            $criteria->condition('t.field2', '=', 't2.field2', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  INNER JOIN (table AS t,table2 AS t2) ON (( t.field = t2.field AND t.field2 = t2.field2 ))         ', $join->getSQL());
    }

    public function testJoinOuter()
    {
        $join = $this->instance->join('OUTER', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  OUTER JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    }

    public function testJoinNatural()
    {
        $join = $this->instance->join('NATURAL', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  NATURAL JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    }

    public function testJoinStraight()
    {
        $join = $this->instance->join('STRAIGHT', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(IStrategy::class, $join);
        $this->assertEquals('SELECT       FROM  AS  STRAIGHT_JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    }

    /**
     * @expectedException FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException
     */
    public function testJoinInvalid()
    {
        $join = $this->instance->join('TEST', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
    }

    public function testWhereAndOr()
    {
        $where = $this->instance->where(function(ICriteria $criteria) {
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
        $this->assertEquals('SELECT       FROM  AS   WHERE ( ( campo = 500 AND campo2 = 500 AND ( field = "value" OR field2 < "value2" ) AND campo3 = "abc" AND campo3 = "abc" AND ( field = "value" OR field2 < "value123122" ) AND campo3 = "abc" ) )        ', $where->getSQL());
    }


        // d($this->instance->from('table', 't')->getSQL() . '"', true);
}

        // public function getSQL()

        // public function where(object $callback) :IStrategy

        // public function groupBy(string $field)

        // public function groupByWithRollup(bool $groupByWithRollup)

        // public function having(string $field, string $condition, string $value)

        // public function orderBy(string $field, string $order)

        // public function orderByWithRollup(bool $orderByWithRollup)

        // public function limit(int $limit)

        // public function offset(int $offset)



            // ->from('table', 't')
            // ->join('LEFT', ['tb' => 'table2'], function(ICriteria $criteria) {
            //     $criteria->condition('tb.field', '=', 't.field2', true);
            // })
