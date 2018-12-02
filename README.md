# FcPhp Datasource MySQL

Class to manipulate Datasource MySQL

[![Build Status](https://travis-ci.org/00F100/fcphp-datasource-mysql.svg?branch=master)](https://travis-ci.org/00F100/fcphp-datasource-mysql) [![codecov](https://codecov.io/gh/00F100/fcphp-datasource-mysql/branch/master/graph/badge.svg)](https://codecov.io/gh/00F100/fcphp-datasource-mysql)

[![PHP Version](https://img.shields.io/packagist/php-v/00f100/fcphp-datasource-mysql.svg)](https://packagist.org/packages/00F100/fcphp-datasource-mysql) [![Packagist Version](https://img.shields.io/packagist/v/00f100/fcphp-datasource-mysql.svg)](https://packagist.org/packages/00F100/fcphp-datasource-mysql) [![Total Downloads](https://poser.pugx.org/00F100/fcphp-datasource-mysql/downloads)](https://packagist.org/packages/00F100/fcphp-datasource-mysql)

## How to install

Composer:
```sh
$ composer require 00f100/fcphp-datasource-mysql
```

or add in composer.json
```json
{
    "require": {
        "00f100/fcphp-datasource-mysql": "*"
    }
}
```

## How to use

```php

use FcPhp\Di\Facades\DiFacade;
use FcPhp\Datasource\Factory;
use FcPhp\Datasource\Strategy;
use FcPhp\Datasource\Interfaces\ICriteria;
use FcPhp\Datasource\MySQL\MySQL;

// MySQL Instance

$dataConnection = [
    'host' => '',
    'port' => '',
    'username' => '',
    'password' => '',
    'database' => '',
];

$instance = new MySQL('mysql', $dataConnection);

// to Query Instance ...

$strategies = [
    'mysql' => 'FcPhp/Datasource/MySQL/Strategies/MySQLStrategy',
];
$criterias = [
    'mysql' => 'FcPhp/Datasource/MySQL/Criterias/MySQL',
];
$di = DiFacade::getInstance();
$factory = new Factory($strategies, $criterias, $di);
$strategy = new Strategy('mysql', $factory);

$query = new Query($strategy);

```

### SELECT

```php
// Configure query

// SELECT t.field
//  FROM table AS t
//  LEFT JOIN table AS t2 ON t2.field = t.field AND t2.field = "string"
//  WHERE (
//      (
//          campo = 500 AND
//          campo2 = 500 AND (
//              field = "value" OR
//              field2 < "value2"
//          ) AND
//          campo3 = "abc" AND
//          campo3 = "abc" AND (
//              field = "value" OR
//              field2 < "value123122"
//          ) AND
//          campo3 IN (10,20,30)
//      )
//  )

$query->select('t.field')
    ->from('table', 't')
    ->join('LEFT', ['t2' => 'table'], function(ICriteria $criteria) {
        $criteria->condition('t2.field', '=', 't.field', true);
        $criteria->condition('t2.field', '=', 'string');
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
        $criteria->condition('campo3', 'IN', [10, 20, 30]);
    });
});

```

### INSERT

```php
// Configure query

// INSERT INTO
//  (column1,column2,column3) VALUES
//  (100,"content2",0)
//      ON DUPLICATE KEY UPDATE
//          `column1` = 50,
//          `column2` = "content to update",
//          `column3` = 1

$query->insert()
    ->columns(['column1', 'column2', 'column3'])
    ->values('column2', 'content2')
    ->values('column3', false)
    ->values('column1', 100)
    ->duplicateKey([
        'column1' => 50,
        'column2' => 'content to update',
        'column3' => true
    ]);
```

### EXECUTE

```php

// Execute Query

$data = $instance->execute($query);

```

See:

- [MySQL Strategy](tests/Integration/MySQLStrategyIntegrationTest.php)
- [MySQL Criteria](tests/Integration/MySQLCriteriaIntegrationTest.php)
