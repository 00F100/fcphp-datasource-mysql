<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Strategies\Select\Select;
use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;
use FcPhp\Di\Facades\DiFacade;
use FcPhp\Datasource\Factories\Factory;
use FcPhp\Datasource\MySQL\Factories\MySQLFactory;
use FcPhp\Datasource\MySQL\Strategies\MySQLStrategy;
use FcPhp\Datasource\Interfaces\ICriteria;

class SelectUnitTest extends TestCase
{
    public function setUp()
    {
        $this->criteria = $this->createMock('FcPhp\Datasource\MySQL\Interfaces\IMySQLCriteria');
        $this->criteria
            ->expects($this->any())
            ->method('getTablesInQuery')
            ->will($this->returnValue(['table']));
        $this->criteria
            ->expects($this->any())
            ->method('getWhere')
            ->will($this->returnValue([
                'AND',
                [
                    'WHERE-MOCK',
                    'WHERE-MOCK2',
                    'OR',
                    [
                        'WHERE-MOCK3',
                        'WHERE-MOCK4',
                        'WHERE-MOCK5',
                    ]
                ]
            ]));

        $this->strategy = $this->createMock('FcPhp\Datasource\MySQL\Strategies\MySQLStrategy');
        $this->strategy
            ->expects($this->any())
            ->method('getCriteria')
            ->will($this->returnValue($this->criteria));

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

    public function testSelectMultiWithAlias()
    {
        $select = $this->instance->select(['alias1' => 't.field', 'alias2' => 't.field2']);
        $this->assertInstanceOf(ISelect::class, $select);
        $this->assertEquals('SELECT t.field AS alias1,t.field2 AS alias2 FROM  AS ', $select->getSQL());
    }

    public function testFrom()
    {
        $from = $this->instance->select('t.field')->from('table', 't');
        $this->assertInstanceOf(ISelect::class, $from);
        $this->assertEquals('SELECT t.field FROM table AS t', $from->getSQL());
    }

    public function testJoinLeft()
    {
        $join = $this->instance->select('t.field')->join('LEFT', ['t' => 'table'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  LEFT JOIN (table AS t) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    public function testJoinRight()
    {
        $join = $this->instance->select('t.field')->join('RIGHT', ['t' => 'table'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  RIGHT JOIN (table AS t) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    public function testJoinInner()
    {
        $join = $this->instance->select('t.field')->join('INNER', ['t' => 'table'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  INNER JOIN (table AS t) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    public function testJoinInnerWhere()
    {
        $join = $this->instance->select('t.field')->join('INNER', ['t' => 'table'], function($criteria) {
            $criteria->or(function($criteria) {
                $criteria->condition('t.field', '=', 't2.field', true);
                $criteria->condition('t.field2', '=', 't2.field2', true);
            });
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  INNER JOIN (table AS t) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    public function testJoinInnerMultiTable()
    {
        $join = $this->instance->select('t.field')->join('INNER', ['t' => 'table', 't2' => 'table2'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
            $criteria->condition('t.field2', '=', 't2.field2', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  INNER JOIN (table AS t,table2 AS t2) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    public function testJoinOuter()
    {
        $join = $this->instance->select('t.field')->join('OUTER', ['t' => 'table'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  OUTER JOIN (table AS t) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    public function testJoinNatural()
    {
        $join = $this->instance->select('t.field')->join('NATURAL', ['t' => 'table'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  NATURAL JOIN (table AS t) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    public function testJoinStraight()
    {
        $join = $this->instance->select('t.field')->join('STRAIGHT', ['t' => 'table'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
        $this->assertInstanceOf(ISelect::class, $join);
        $this->assertEquals('SELECT t.field FROM  AS  STRAIGHT_JOIN (table AS t) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) ))', $join->getSQL());
    }

    /**
     * @expectedException FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException
     */
    public function testJoinInvalid()
    {
        $join = $this->instance->select('t.field')->join('TEST', ['t' => 'table'], function($criteria) {
            $criteria->condition('t.field', '=', 't2.field', true);
        });
    }

    public function testWhereAndOr()
    {
        $where = $this->instance->select('t.field')->where(function($criteria) {
            $criteria->and(function($criteria) {
                $criteria->condition('campo', '=', 500);
                $criteria->condition('campo2', '=', 500);
                $criteria->or(function($criteria) {
                    $criteria->condition('field', '=', 'value');
                    $criteria->condition('field2', '<', 'value2');
                });
                $criteria->condition('campo3', '=', 'abc');
                $criteria->condition('campo3', '=', 'abc');
                $criteria->or(function($criteria) {
                    $criteria->condition('field', '=', 'value');
                    $criteria->condition('field2', '<', 'value123122');
                });
                $criteria->condition('campo3', '=', 'abc');
            });
        });
        $this->assertInstanceOf(ISelect::class, $where);
        $this->assertEquals('SELECT t.field FROM  AS  WHERE ( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) )', $where->getSQL());
    }

    public function testGroupBy()
    {
        $groupBy = $this->instance->select('t.field')->groupBy('t.field2')->groupBy('t.field3');
        $this->assertInstanceOf(ISelect::class, $groupBy);
        $this->assertEquals('SELECT t.field FROM  AS GROUP BY t.field2,t.field3', $groupBy->getSQL());
    }

    public function testGroupByWithRollup()
    {
        $groupBy = $this->instance->select('t.field')->groupBy('t.field2')->groupBy('t.field3')->groupByWithRollup(true);
        $this->assertInstanceOf(ISelect::class, $groupBy);
        $this->assertEquals('SELECT t.field FROM  AS GROUP BY t.field2,t.field3 WITH ROLLUP', $groupBy->getSQL());
    }

    public function testHaving()
    {
        $having = $this->instance->select('t.field')->having(function($criteria) {
            $criteria->and(function($criteria) {
                $criteria->condition('SUM(t.field)', '>', 0);
            });
        });
        $this->assertInstanceOf(ISelect::class, $having);
        $this->assertEquals('SELECT t.field FROM  AS  HAVING ( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) )', $having->getSQL());
    }

    public function testOrderNonArray()
    {
        $order = $this->instance->select('t.field')->orderBy('t.field4', 'DESC');
        $this->assertInstanceOf(ISelect::class, $order);
        $this->assertEquals('SELECT t.field FROM  AS  ORDER BY t.field4 DESC', $order->getSQL());
    }

    public function testOrderByArray()
    {
        $order = $this->instance->select('t.field')->orderBy(['t.field' => 'DESC', 't.field2' => 'ASC'])->orderBy('t.field3')->orderBy('t.field4', 'DESC');
        $this->assertInstanceOf(ISelect::class, $order);
        $this->assertEquals('SELECT t.field FROM  AS  ORDER BY t.field DESC,t.field2 ASC,t.field3 ASC,t.field4 DESC', $order->getSQL());
    }

    public function testOrderByWithRollup()
    {
        $order = $this->instance->select('t.field')->orderBy('t.field4', 'DESC')->orderByWithRollup(true);
        $this->assertInstanceOf(ISelect::class, $order);
        $this->assertEquals('SELECT t.field FROM  AS  ORDER BY t.field4 DESC WITH ROLLUP', $order->getSQL());
    }

    public function testLimit()
    {
        $limit = $this->instance->select('t.field')->limit(10);
        $this->assertInstanceOf(ISelect::class, $limit);
        $this->assertEquals('SELECT t.field FROM  AS  LIMIT 10', $limit->getSQL());
    }

    public function testOffset()
    {
        $offset = $this->instance->select('t.field')->offset(20);
        $this->assertInstanceOf(ISelect::class, $offset);
        $this->assertEquals('SELECT t.field FROM  AS  OFFSET 20', $offset->getSQL());
    }

    public function testSubQuery()
    {
        $subquery = (new Select($this->strategy))->select('t.field')->from('table', 't');
        $principal = $this->instance
            ->select('t2.field')
            ->from('table2', 't2')
            ->where(function($criteria) use ($subquery) {
                $criteria->condition('t2.field', '=', $subquery);
            });
        $this->assertInstanceOf(ISelect::class, $subquery);
        $this->assertInstanceOf(ISelect::class, $principal);
        $this->assertEquals('SELECT t2.field FROM table2 AS t2 WHERE ( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) )', $principal->getSQL());
    }

    public function testTablesInQuery()
    {
        $subquery = (new Select($this->strategy))->select('t.field')->from('table', 't');
        $subquery2 = (new Select($this->strategy))->select('t4.field')->from('table4', 't4');
        $principal = $this->instance
            ->select($subquery2, 'item')
            ->from('table2', 't2')
            ->join('LEFT', ['t3' => 'table3'], function($criteria) {
                $criteria->condition('t3.field', '=', 't2.field', true);
            })
            ->where(function($criteria) use ($subquery) {
                $criteria->condition('t2.field', '=', $subquery);
            });
        $this->assertInstanceOf(ISelect::class, $subquery);
        $this->assertInstanceOf(ISelect::class, $principal);
        $this->assertEquals(['table4', 'table2', 'table3', 'table'], $principal->getTablesInQuery());
        $this->assertEquals('SELECT (SELECT t4.field FROM table4 AS t4) AS item FROM table2 AS t2 LEFT JOIN (table3 AS t3) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) )) WHERE ( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) )', $principal->getSQL());
    }

    public function testTablesInQueryManySelectQuery()
    {
        $subquery = (new Select($this->strategy))->select('t.field')->from('table', 't');
        $subquery2 = (new Select($this->strategy))->select('t4.field')->from('table4', 't4');
        $subquery3 = (new Select($this->strategy))->select('t5.field')->from('table5', 't5');
        $principal = $this->instance
            ->select([
                'value1' => $subquery2,
                'value2' => $subquery3
            ])
            ->from('table2', 't2')
            ->join('LEFT', ['t3' => 'table3'], function($criteria) {
                $criteria->condition('t3.field', '=', 't2.field', true);
            })
            ->where(function($criteria) use ($subquery) {
                $criteria->condition('t2.field', '=', $subquery);
            });
        $this->assertInstanceOf(ISelect::class, $subquery);
        $this->assertInstanceOf(ISelect::class, $principal);
        $this->assertEquals(['table4', 'table5', 'table2', 'table3', 'table'], $principal->getTablesInQuery());
        $this->assertEquals('SELECT (SELECT t4.field FROM table4 AS t4) AS value1,(SELECT t5.field FROM table5 AS t5) AS value2 FROM table2 AS t2 LEFT JOIN (table3 AS t3) ON (( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) )) WHERE ( ( WHERE-MOCK AND WHERE-MOCK2 AND ( WHERE-MOCK3 OR WHERE-MOCK4 OR WHERE-MOCK5 ) ) )', $principal->getSQL());
    }
}
