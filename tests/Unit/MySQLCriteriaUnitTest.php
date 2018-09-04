<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Criterias\MySQLCriteria;
use FcPhp\Datasource\MySQL\Interfaces\IMySQLCriteria;
use FcPhp\Datasource\Interfaces\ICriteria;

class MySQLCriteriaUnitTest extends TestCase
{
    public function setUp()
    {
        $this->di = $this->createMock('FcPhp\Di\Interfaces\IDi');
        $this->strategies = [];
        $this->criterias = [
            'mysql' => 'MySQLCriteriaMock'
        ];

        $this->factory = $this->createMock('FcPhp\Datasource\Interfaces\IFactory');
        $this->instance = new MySQLCriteriaMock('mysql', $this->factory);
    }

    public function testInstance()
    {
        $this->assertInstanceOf(IMySQLCriteria::class, $this->instance);
    }

    public function testCriteria()
    {
        $this->instance->and(function(ICriteria $criteria) {
            $criteria->condition('field', '=', 'value');
            $criteria->condition('field2', '<', 'value2');
        });

        $this->instance->or(function(ICriteria $criteria) {
            $criteria->and(function(ICriteria $criteria) {
                $criteria->condition('field3', '!=', 'value3');
                $criteria->condition('field4', '>', 'value4');
            });
            $criteria->or(function(ICriteria $criteria) {
                $criteria->condition('field5', '!=', 15);
                $criteria->condition('field6', '>', 'value6');
            });
        });

        $value = [
            'AND',
            [
            ],
            'OR',
            [
            ],
        ];

        $this->assertEquals($value, $this->instance->getWhere());
    }
}

class MySQLCriteriaMock extends MySQLCriteria
{
}
