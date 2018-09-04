<?php

use PHPUnit\Framework\TestCase;
use FcPhp\Datasource\MySQL\Criterias\MySQLCriteria;
use FcPhp\Datasource\MySQL\Interfaces\IMySQLCriteria;
use FcPhp\Datasource\Factories\Factory;
use FcPhp\Datasource\Interfaces\ICriteria;
use FcPhp\Di\Facades\DiFacade;

class MySQLCriteriaIntegrationTest extends TestCase
{
    public function setUp()
    {
        $this->di = DiFacade::getInstance();
        $this->strategies = [
            'mysql' => 'FcPhp/Datasource/MySQL/Strategies/MySQLStrategy',
        ];
        $this->criterias = [
            'mysql' => 'FcPhp/Datasource/MySQL/Criterias/MySQLCriteria'
        ];

        $this->factory = new Factory('mysql', 'mysql', $this->strategies, $this->criterias, $this->di);
        $this->instance = new MySQLCriteria($this->factory);
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
                'field = "value"',
                'field2 < "value2"'
            ],
            'OR',
            [
                'AND',
                [
                    'field3 != "value3"',
                    'field4 > "value4"'
                ],
                'OR',
                [
                    'field5 != 15',
                    'field6 > "value6"',
                ],
            ],
        ];

        $this->assertEquals($value, $this->instance->getWhere());
    }
}
