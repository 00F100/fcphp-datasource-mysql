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
    'postgresql' => 'FcPhp/PostgreSQL/Strategies/PostgreSQL',
    'sqlserver' => 'FcPhp/SQLServer/Strategies/SQLServer',
    'sqlite' => 'FcPhp/SQLite/Strategies/SQLite',
    'mongodb' => 'FcPhp/MongoDB/Strategies/MongoDB',
    'soap' => 'FcPhp/SOAP/Strategies/SOAP',
    'ldap' => 'FcPhp/Ldap/Strategies/Ldap',
    'rest' => 'FcPhp/Rest/Strategies/Rest',
    'file' => 'FcPhp/File/Strategies/File',
    'amazon-bucket' => 'FcPhp/Amazon/Strategies/Bucket',
    'amazon-log' => 'FcPhp/Amazon/Strategies/Log',
    'amazon-sqs' => 'FcPhp/Amazon/Strategies/Sqs',
    'amazon-redshift' => 'FcPhp/Amazon/Strategies/Redshift',
];
$criterias = [
    'mysql' => 'FcPhp/Datasource/MySQL/Criterias/MySQL',
    'postgresql' => 'FcPhp/PostgreSQL/Criterias/PostgreSQL',
    'sqlserver' => 'FcPhp/SQLServer/Criterias/SQLServer',
    'sqlite' => 'FcPhp/SQLite/Criterias/SQLite',
    'mongodb' => 'FcPhp/MongoDB/Criterias/MongoDB',
    'soap' => 'FcPhp/SOAP/Criterias/SOAP',
    'ldap' => 'FcPhp/Ldap/Criterias/Ldap',
    'rest' => 'FcPhp/Rest/Criterias/Rest',
    'file' => 'FcPhp/File/Criterias/File',
    'amazon-bucket' => 'FcPhp/Amazon/Criterias/Bucket',
    'amazon-log' => 'FcPhp/Amazon/Criterias/Log',
    'amazon-sqs' => 'FcPhp/Amazon/Criterias/Sqs',
    'amazon-redshift' => 'FcPhp/Amazon/Criterias/Redshift',
];
$di = DiFacade::getInstance();
$factory = new Factory($strategies, $criterias, $di);
$strategy = new Strategy('mysql', $factory);

$query = new Query($strategy);

// Configure query

$query->select('t.field')->from('table', 't')->where(function(ICriteria $criteria) {
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

// Execute Query

$data = $instance->execute($query);

```

See:

- [MySQL Strategy](tests/Integration/MySQLStrategyIntegrationTest.php)
- [MySQL Criteria](tests/Integration/MySQLCriteriaIntegrationTest.php)
