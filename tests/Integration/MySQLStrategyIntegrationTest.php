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
            // 'insert' => 'FcPhp\Datasource\MySQL\Strategies\Insert\Insert',
            // 'update' => 'FcPhp\Datasource\MySQL\Strategies\Update\Update',
            // 'delete' => 'FcPhp\Datasource\MySQL\Strategies\Delete\Delete',
            // 'create' => 'FcPhp\Datasource\MySQL\Strategies\Create\Create',
            // 'alter' => 'FcPhp\Datasource\MySQL\Strategies\Alter\Alter',
            // 'drop' => 'FcPhp\Datasource\MySQL\Strategies\Drop\Drop',
            // 'rename' => 'FcPhp\Datasource\MySQL\Strategies\Rename\Rename',
            // 'truncate' => 'FcPhp\Datasource\MySQL\Strategies\Truncate\Truncate',
            // 'call' => 'FcPhp\Datasource\MySQL\Strategies\Call\Call',
            // 'transaction' => 'FcPhp\Datasource\MySQL\Strategies\Transaction\Transaction',
            // 'lock' => 'FcPhp\Datasource\MySQL\Strategies\Lock\Lock',
            // 'prepare' => 'FcPhp\Datasource\MySQL\Strategies\Prepare\Prepare',
            // 'set' => 'FcPhp\Datasource\MySQL\Strategies\Set\Set',
            // 'execute' => 'FcPhp\Datasource\MySQL\Strategies\Execute\Execute',
            // 'deallocate' => 'FcPhp\Datasource\MySQL\Strategies\Deallocate\Deallocate',
            // 'begin' => 'FcPhp\Datasource\MySQL\Strategies\Begin\Begin',
            // 'repeat' => 'FcPhp\Datasource\MySQL\Strategies\Repeat\Repeat',
            // 'delimiter' => 'FcPhp\Datasource\MySQL\Strategies\Delimiter\Delimiter',
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
        d($sizeResult->getSQL().'"', true);
        $this->assertEquals('SELECT SQL_BUFFER_RESULT t.field FROM  AS ', $sizeResult->getSQL());
    }

    // public function testNoCache()
    // {
    //     $noCache = $this->instance->noCache(true);
    //     $this->assertInstanceOf(IStrategy::class, $noCache);
    //     $this->assertEquals('SELECT     SQL_NO_CACHE  FROM  AS           ', $noCache->getSQL());
    // }

    // public function testSelectOne()
    // {
    //     $select = $this->instance->select('t.field');
    //     $this->assertInstanceOf(IStrategy::class, $select);
    //     $this->assertEquals('SELECT      t.field FROM  AS           ', $select->getSQL());
    // }

    // public function testSelectMulti()
    // {
    //     $select = $this->instance->select(['t.field', 't.field2']);
    //     $this->assertInstanceOf(IStrategy::class, $select);
    //     $this->assertEquals('SELECT      t.field,t.field2 FROM  AS           ', $select->getSQL());
    // }

    // public function testFrom()
    // {
    //     $from = $this->instance->from('table', 't');
    //     $this->assertInstanceOf(IStrategy::class, $from);
    //     $this->assertEquals('SELECT       FROM table AS t          ', $from->getSQL());
    // }

    // public function testJoinLeft()
    // {
    //     $join = $this->instance->join('LEFT', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  LEFT JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    // }

    // public function testJoinRight()
    // {
    //     $join = $this->instance->join('RIGHT', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  RIGHT JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    // }

    // public function testJoinInner()
    // {
    //     $join = $this->instance->join('INNER', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  INNER JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    // }

    // public function testJoinInnerWhere()
    // {
    //     $join = $this->instance->join('INNER', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //         $criteria->condition('t.field2', '=', 't2.field2', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  INNER JOIN (table AS t) ON (( t.field = t2.field AND t.field2 = t2.field2 ))         ', $join->getSQL());
    // }

    // public function testJoinInnerMultiTable()
    // {
    //     $join = $this->instance->join('INNER', ['t' => 'table', 't2' => 'table2'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //         $criteria->condition('t.field2', '=', 't2.field2', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  INNER JOIN (table AS t,table2 AS t2) ON (( t.field = t2.field AND t.field2 = t2.field2 ))         ', $join->getSQL());
    // }

    // public function testJoinOuter()
    // {
    //     $join = $this->instance->join('OUTER', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  OUTER JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    // }

    // public function testJoinNatural()
    // {
    //     $join = $this->instance->join('NATURAL', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  NATURAL JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    // }

    // public function testJoinStraight()
    // {
    //     $join = $this->instance->join('STRAIGHT', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //     });
    //     $this->assertInstanceOf(IStrategy::class, $join);
    //     $this->assertEquals('SELECT       FROM  AS  STRAIGHT_JOIN (table AS t) ON (( t.field = t2.field ))         ', $join->getSQL());
    // }

    // /**
    //  * @expectedException FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException
    //  */
    // public function testJoinInvalid()
    // {
    //     $join = $this->instance->join('TEST', ['t' => 'table'], function(ICriteria $criteria) {
    //         $criteria->condition('t.field', '=', 't2.field', true);
    //     });
    // }

    // public function testWhereAndOr()
    // {
    //     $where = $this->instance->where(function(ICriteria $criteria) {
    //         $criteria->and(function(ICriteria $criteria) {
    //             $criteria->condition('campo', '=', 500);
    //             $criteria->condition('campo2', '=', 500);
    //             $criteria->or(function(ICriteria $criteria) {
    //                 $criteria->condition('field', '=', 'value');
    //                 $criteria->condition('field2', '<', 'value2');
    //             });
    //             $criteria->condition('campo3', '=', 'abc');
    //             $criteria->condition('campo3', '=', 'abc');
    //             $criteria->or(function(ICriteria $criteria) {
    //                 $criteria->condition('field', '=', 'value');
    //                 $criteria->condition('field2', '<', 'value123122');
    //             });
    //             $criteria->condition('campo3', '=', 'abc');
    //         });
    //     });
    //     $this->assertEquals('SELECT       FROM  AS   WHERE ( ( campo = 500 AND campo2 = 500 AND ( field = "value" OR field2 < "value2" ) AND campo3 = "abc" AND campo3 = "abc" AND ( field = "value" OR field2 < "value123122" ) AND campo3 = "abc" ) )        ', $where->getSQL());
    // }


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
