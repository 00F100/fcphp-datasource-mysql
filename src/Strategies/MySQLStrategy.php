<?php

namespace FcPhp\Datasource\MySQL\Strategies
{
    use FcPhp\Datasource\Strategy;
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLStrategy;
    use FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException;

    class MySQLStrategy extends Strategy implements IStrategy, IMySQLStrategy
    {
        const DATASOURCE_MYSQL_STRATEGY_SELECT = 0;
        const DATASOURCE_MYSQL_STRATEGY_INSERT = 1;
        const DATASOURCE_MYSQL_STRATEGY_UPDATE = 2;
        const DATASOURCE_MYSQL_STRATEGY_DELETE = 2;
        const DATASOURCE_MYSQL_STRATEGY_CREATE = 3;
        const DATASOURCE_MYSQL_STRATEGY_ALTER = 4;
        const DATASOURCE_MYSQL_STRATEGY_DROP = 5;
        const DATASOURCE_MYSQL_STRATEGY_RENAME = 6;
        const DATASOURCE_MYSQL_STRATEGY_TRUNCATE = 7;
        const DATASOURCE_MYSQL_STRATEGY_CALL = 7;
        const DATASOURCE_MYSQL_STRATEGY_TRANSACTION = 8;
        const DATASOURCE_MYSQL_STRATEGY_LOCK = 9;
        const DATASOURCE_MYSQL_STRATEGY_PREPARE = 10;
        const DATASOURCE_MYSQL_STRATEGY_SET = 11;
        const DATASOURCE_MYSQL_STRATEGY_EXECUTE = 12;
        const DATASOURCE_MYSQL_STRATEGY_DEALLOCATE = 13;
        const DATASOURCE_MYSQL_STRATEGY_BEGIN = 14;
        const DATASOURCE_MYSQL_STRATEGY_REPEAT = 14;
        const DATASOURCE_MYSQL_STRATEGY_DELIMITER = 15;

        protected $availableMethods = [
            self::DATASOURCE_MYSQL_STRATEGY_SELECT => 'select',
            self::DATASOURCE_MYSQL_STRATEGY_INSERT => 'insert',
            self::DATASOURCE_MYSQL_STRATEGY_UPDATE => 'update',
            self::DATASOURCE_MYSQL_STRATEGY_DELETE => 'delete',
            self::DATASOURCE_MYSQL_STRATEGY_CREATE => 'create',
            self::DATASOURCE_MYSQL_STRATEGY_ALTER => 'alter',
            self::DATASOURCE_MYSQL_STRATEGY_DROP => 'drop',
            self::DATASOURCE_MYSQL_STRATEGY_RENAME => 'rename',
            self::DATASOURCE_MYSQL_STRATEGY_TRUNCATE => 'truncate',
            self::DATASOURCE_MYSQL_STRATEGY_CALL => 'call',
            self::DATASOURCE_MYSQL_STRATEGY_TRANSACTION => 'transaction',
            self::DATASOURCE_MYSQL_STRATEGY_LOCK => 'lock',
            self::DATASOURCE_MYSQL_STRATEGY_PREPARE => 'prepare', 
            self::DATASOURCE_MYSQL_STRATEGY_SET => 'set', 
            self::DATASOURCE_MYSQL_STRATEGY_EXECUTE => 'execute', 
            self::DATASOURCE_MYSQL_STRATEGY_DEALLOCATE => 'deallocate', 
            self::DATASOURCE_MYSQL_STRATEGY_BEGIN => 'begin', 
            self::DATASOURCE_MYSQL_STRATEGY_REPEAT => 'repeat', 
            self::DATASOURCE_MYSQL_STRATEGY_DELIMITER => 'delimiter', 

        ];
        protected $mode;
        protected $mySQLFactory;

        public function __construct(string $criteria, IFactory $factory, IMySQLFactory $mySQLFactory)
        {
            $this->mySQLFactory = $mySQLFactory;
            parent::__construct($criteria, $factory);
        }

        public function __call(string $method, array $args)
        {
            if(in_array($method, $this->availableMethods)) {
                return $this->mySQLFactory->get($method);
            }
        }

        public function getSQL()
        {
            $sql = $this->selectInstruction;
            $sql .= str_replace('[selectRule]', (!empty($this->selectRule) ? $this->selectRule : ''), $select);
            // $sql = str_replace('[selectRule]', (!empty($this->selectRule) ? $this->selectRule : ''), $select);
            $sql = str_replace('[highPriority]', ($this->highPriority == true ? 'HIGH_PRIORITY' : ''), $select);
            $sql = str_replace('[straightJoin]', ($this->straightJoin == true ? 'STRAIGHT_JOIN' : ''), $select);
            $sql = str_replace('[sizeResult]', (count($this->sizeResult) > 0 ? implode(' ', $this->sizeResult) : ''), $select);
            $sql = str_replace('[noCache]', ($this->noCache == true ? 'SQL_NO_CACHE' : ''), $select);
            $sql = str_replace('[sqlCalcFoundRows]', ($this->sqlCalcFoundRows == true ? 'SQL_CALC_FOUND_ROWS' : ''), $select);
            $sql = str_replace('[select]', implode(',', $this->select), $select);
            $sql = str_replace('[table]', $this->table, $select);
            $sql = str_replace('[tableAlias]', $this->tableAlias, $select);
            $sql = str_replace('[join]', (count($this->join) > 0 ? implode(' ', $this->mountJoin($this->join)) : ''), $select);
            $sql = str_replace('[where]', (count($this->where) > 0 ? 'WHERE ' . $this->mountWhere($this->where) : ''), $select);
            $sql = str_replace('[groupBy]', (count($this->groupBy) > 0 ? implode(' ', $this->mountGroupBy($this->groupBy)) : ''), $select);
            $select = str_replace('[groupByWithRollup]', ($this->groupByWithRollup == true ? 'WITH ROLLUP' : ''), $select);
            $select = str_replace('[having]', (count($this->having) > 0 ? implode(' ', $this->mountHaving($this->having)) : ''), $select);
            $select = str_replace('[orderBy]', (count($this->orderBy) > 0 ? implode(' ', $this->mountOrderBy($this->orderBy)) : ''), $select);
            $select = str_replace('[orderByWithRollup]', ($this->orderByWithRollup == true ? 'WITH ROLLUP' : ''), $select);
            $select = str_replace('[limit]', (!empty($this->limit) ? 'LIMIT ' . $this->limit : ''), $select);
            $select = str_replace('[offset]', (!empty($this->offset) ? 'OFFSET ' . $this->offset : ''), $select);

            return $select;
        }

        
    }
}
