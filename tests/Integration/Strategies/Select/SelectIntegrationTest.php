<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Strategies\Select\Select;
use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;
use FcPhp\Di\Facades\DiFacade;
use FcPhp\Datasource\Factories\Factory;
use FcPhp\Datasource\MySQL\Factories\MySQLFactory;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;
use FcPhp\Datasource\Interfaces\ICriteria;

class SelectIntegrationTest extends TestCase
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
        $this->instance = new Select($this->strategy);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(ISelect::class, $this->instance);
    }

    public function testSelect()
    {
        $select = $this->instance->select('t.field');
        $this->assertInstanceOf(ISelect::class, $select);
        $this->assertEquals('SELECT t.field FROM  AS ', $select->getSQL());
    }

    public function testSelectRuleDistinct()
    {
        $selectRule = $this->instance->select('t.field')->selectRule('DISTINCT');
        $this->assertInstanceOf(ISelect::class, $selectRule);
        $this->assertEquals('SELECT DISTINCT t.field FROM  AS ', $selectRule->getSQL());
    }

    public function testSelectRuleAll()
    {
        $selectRule = $this->instance->select('t.field')->selectRule('ALL');
        $this->assertInstanceOf(ISelect::class, $selectRule);
        $this->assertEquals('SELECT ALL t.field FROM  AS ', $selectRule->getSQL());
    }

    public function testSelectRuleDistinctRow()
    {
        $selectRule = $this->instance->select('t.field')->selectRule('DISTINCTROW');
        $this->assertInstanceOf(ISelect::class, $selectRule);
        $this->assertEquals('SELECT DISTINCTROW t.field FROM  AS ', $selectRule->getSQL());
    }

    public function testHighPriority()
    {
        $highPriority = $this->instance->select('t.field')->highPriority(true);
        $this->assertInstanceOf(ISelect::class, $highPriority);
        $this->assertEquals('SELECT HIGH_PRIORITY t.field FROM  AS ', $highPriority->getSQL());
    }

    public function testStraightJoin()
    {
        $straightJoin = $this->instance->select('t.field')->straightJoin(true);
        $this->assertInstanceOf(ISelect::class, $straightJoin);
        $this->assertEquals('SELECT STRAIGHT_JOIN t.field FROM  AS ', $straightJoin->getSQL());
    }

    public function testSizeResultSqlSmallResult()
    {
        $sizeResult = $this->instance->select('t.field')->sizeResult('SQL_SMALL_RESULT');
        $this->assertInstanceOf(ISelect::class, $sizeResult);
        $this->assertEquals('SELECT SQL_SMALL_RESULT t.field FROM  AS ', $sizeResult->getSQL());
    }

    public function testSizeResultSqlBigResult()
    {
        $sizeResult = $this->instance->select('t.field')->sizeResult('SQL_BIG_RESULT');
        $this->assertInstanceOf(ISelect::class, $sizeResult);
        $this->assertEquals('SELECT SQL_BIG_RESULT t.field FROM  AS ', $sizeResult->getSQL());
    }

    public function testSizeResultSqlBufferResult()
    {
        $sizeResult = $this->instance->select('t.field')->sizeResult('SQL_BUFFER_RESULT');
        $this->assertInstanceOf(ISelect::class, $sizeResult);
        $this->assertEquals('SELECT SQL_BUFFER_RESULT t.field FROM  AS ', $sizeResult->getSQL());
    }

    public function testNoCache()
    {
        $noCache = $this->instance->select('t.field')->noCache(true);
        $this->assertInstanceOf(ISelect::class, $noCache);
        $this->assertEquals('SELECT SQL_NO_CACHE t.field FROM  AS ', $noCache->getSQL());
    }

    public function testCalcFoundRows()
    {
        $calcFoundRows = $this->instance->select('t.field')->sqlCalcFoundRows(true);
        $this->assertInstanceOf(ISelect::class, $calcFoundRows);
        $this->assertEquals('SELECT SQL_CALC_FOUND_ROWS t.field FROM  AS ', $calcFoundRows->getSQL());
    }

    public function testSelectOne()
    {
        $select = $this->instance->select('t.field');
        $this->assertInstanceOf(ISelect::class, $select);
        $this->assertEquals('SELECT t.field FROM  AS ', $select->getSQL());
    }

    public function testSelectMulti()
    {
        $select = $this->instance->select(['t.field', 't.field2']);
        $this->assertInstanceOf(ISelect::class, $select);
        $this->assertEquals('SELECT t.field,t.field2 FROM  AS ', $select->getSQL());
    }

    public function testFrom()
    {
        $from = $this->instance->select('t.field')->from('table', 't');
        $this->assertInstanceOf(ISelect::class, $from);
        $this->assertEquals('SELECT t.field FROM table AS t', $from->getSQL());
    }

    public function testJoinLeft()
    {
        $join = $this->instance->select('t.field')->join('LEFT', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  LEFT JOIN (table AS t) ON (( t.field = t2.field ))', $join->getSQL());
    }

    public function testJoinRight()
    {
        $join = $this->instance->select('t.field')->join('RIGHT', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  RIGHT JOIN (table AS t) ON (( t.field = t2.field ))', $join->getSQL());
    }

    public function testJoinInner()
    {
        $join = $this->instance->select('t.field')->join('INNER', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  INNER JOIN (table AS t) ON (( t.field = t2.field ))', $join->getSQL());
    }

    public function testJoinInnerWhere()
    {
        $join = $this->instance->select('t.field')->join('INNER', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->or(function(ICriteria $criteria) {
                $criteria->condition('t.field', '=', 't2.field', true);
                $criteria->condition('t.field2', '=', 't2.field2', true);
            });
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  INNER JOIN (table AS t) ON (( ( t.field = t2.field OR t.field2 = t2.field2 ) ))', $join->getSQL());
    }

    public function testJoinInnerMultiTable()
    {
        $join = $this->instance->select('t.field')->join('INNER', ['t' => 'table', 't2' => 'table2'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
            $criteria->condition('t.field2', '=', 't2.field2', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  INNER JOIN (table AS t,table2 AS t2) ON (( t.field = t2.field AND t.field2 = t2.field2 ))', $join->getSQL());
    }

    public function testJoinOuter()
    {
        $join = $this->instance->select('t.field')->join('OUTER', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  OUTER JOIN (table AS t) ON (( t.field = t2.field ))', $join->getSQL());
    }

    public function testJoinNatural()
    {
        $join = $this->instance->select('t.field')->join('NATURAL', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  NATURAL JOIN (table AS t) ON (( t.field = t2.field ))', $join->getSQL());
    }

    public function testJoinStraight()
    {
        $join = $this->instance->select('t.field')->join('STRAIGHT', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  STRAIGHT_JOIN (table AS t) ON (( t.field = t2.field ))', $join->getSQL());
    }

    /**
     * @expectedException FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException
     */
    public function testJoinInvalid()
    {
        $join = $this->instance->select('t.field')->join('TEST', ['t' => 'table'], function(ICriteria $criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
    }

    public function testWhereAndOr()
    {
        $where = $this->instance->select('t.field')->where(function(ICriteria $criteria) {
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
        $this->assertInstanceOf(ISelect::class, $where);
        $this->assertEquals('SELECT t.field FROM  AS  WHERE ( ( campo = 500 AND campo2 = 500 AND ( field = "value" OR field2 < "value2" ) AND campo3 = "abc" AND campo3 = "abc" AND ( field = "value" OR field2 < "value123122" ) AND campo3 = "abc" ) )', $where->getSQL());
    }

    public function testGroupBy()
    {
        $groupBy = $this->instance->select('t.field')->groupBy('t.field2')->groupBy('t.field3');
        $this->assertInstanceOf(ISelect::class, $groupBy);
        $this->assertEquals('SELECT t.field FROM  AS GROUP BY t.field2,t.field3', $groupBy->getSQL());
    }
}