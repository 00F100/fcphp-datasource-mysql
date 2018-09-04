<?php

namespace FcPhp\Datasource\MySQL\Criterias
{
    use FcPhp\Datasource\Criteria;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\Interfaces\IFactory;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLCriteria;

    class MySQLCriteria extends Criteria implements ICriteria, IMySQLCriteria
    {
        protected $conditionString = '"%s"';
        protected $conditionInt = '%s';
        protected $conditionAnd = 'AND';
        protected $conditionOr = 'OR';
        protected $conditionSpace = ' ';
    }
}
