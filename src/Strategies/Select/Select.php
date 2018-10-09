<?php

namespace FcPhp\Datasource\MySQL\Strategies\Select
{
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;
    use FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException;

    class Select implements ISelect
    {
        /**
         * @var string Select instruction
         */
        protected $selectInstruction = 'SELECT%s FROM%s';

        /**
         * @var string Select Rule
         */
        protected $selectRule;

        /**
         * @var array List of Select Rule Enable
         */
        protected $selectRules = ['ALL', 'DISTINCT', 'DISTINCTROW'];

        /**
         * @var bool is High Priority
         */
        protected $highPriority;

        /**
         * @var bool is Straight Join
         */
        protected $straightJoin;

        /**
         * @var array Size Result
         */
        protected $sizeResult = [];

        /**
         * @var array list of enable Size Result
         */
        protected $sizeResults = ['SQL_SMALL_RESULT', 'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT'];

        /**
         * @var bool if use MySQL cache
         */
        protected $noCache;

        /**
         * @var bool count records
         */
        protected $sqlCalcFoundRows;

        /**
         * @var array list of fields to Select
         */
        protected $select = [];

        /**
         * @var string Table to select
         */
        protected $table;

        /**
         * @var string Table alias
         */
        protected $tableAlias;

        /**
         * @var array Joins on Tables
         */
        protected $join = [];

        /**
         * @var array Joins enabled
         */
        protected $joins = ['LEFT', 'RIGHT', 'INNER', 'OUTER', 'NATURAL', 'STRAIGHT'];

        /**
         * @var array conditions on Where clouse
         */
        protected $where = [];

        /**
         * @var array Group By fields
         */
        protected $groupBy = [];

        /**
         * @var bool Group By with Rollup
         */
        protected $groupByWithRollup;

        /**
         * @var array fields to Having
         */
        protected $having = [];

        /**
         * @var array Order By fields
         */
        protected $orderBy = [];

        /**
         * @var bool Order By with Rollup
         */
        protected $orderByWithRollup;

        /**
         * @var int Limit of records
         */
        protected $limit;

        /**
         * @var int Offset of records
         */
        protected $offset;

        /**
         * @var FcPhp\Datasource\Interfaces\IStrategy
         */
        protected $strategy;

        /**
         * @var array
         */
        protected $tablesInQuery = [];

        /**
         * Method to construct instance
         *
         * @param FcPhp\Datasource\Interfaces\IStrategy
         * @return void
         */
        public function __construct(IStrategy $strategy)
        {
            $this->strategy = $strategy;
        }

        /**
         * Method to return instance of Criteria
         *
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function getCriteria()
        {
            return $this->strategy->getCriteria();
        }

        /**
         * Method to configure Select Rule
         *
         * @param string $rule Rule to use
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function selectRule(string $rule) :ISelect
        {
            if(in_array($rule, $this->selectRules)) {
                $this->selectRule = $rule;
            }
            return $this;
        }

        /**
         * Method to define High Priority
         *
         * @param bool $highPriority define true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function highPriority(bool $highPriority) :ISelect
        {
            $this->highPriority = $highPriority;
            return $this;
        }

        /**
         * Method to define Straight Join
         *
         * @param bool $straightJoin define true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function straightJoin(bool $straightJoin) :ISelect
        {
            $this->straightJoin = $straightJoin;
            return $this;
        }

        /**
         * Method to define size result
         *
         * @param string $size Configure Size Result
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function sizeResult(string $size) :ISelect
        {
            if(in_array($size, $this->sizeResults)) {
                $this->sizeResult[] = $size;
            }
            return $this;
        }

        /**
         * Method to configure to (non) use cache
         *
         * @param bool $noCache define true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function noCache(bool $noCache) :ISelect
        {
            $this->noCache = $noCache;
            return $this;
        }

        /**
         * Method to calc rows of records
         *
         * @param bool $sqlCalcFoundRows configure to return calc of records
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function sqlCalcFoundRows(bool $sqlCalcFoundRows) :ISelect
        {
            $this->sqlCalcFoundRows = $sqlCalcFoundRows;
            return $this;
        }

        /**
         * Method to confire fields to select
         *
         * @param string|array|ISelect $fields Field(s) to select
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function select($fields, string $alias = null) :ISelect
        {
            if(is_array($fields)) {
                $newFields = [];
                foreach($fields as $alias => $field) {
                    if($field instanceof ISelect) {
                        $tablesInQuery = $field->getTablesInQuery();
                        if(count($tablesInQuery) > 0) {
                            foreach($tablesInQuery as $table) {
                                if(!in_array($table, $this->tablesInQuery)) {
                                    $this->tablesInQuery[] = $table;
                                }
                            }
                        }
                        $fieldSQL = '(' . $field->getSQL() . ')';
                        if(!is_int($alias)) {
                            $fieldSQL .= ' AS ' . $alias;
                        }
                        $newFields[] = $fieldSQL;
                    }else{
                        $fieldSQL = $field;
                        if(!is_int($alias)) {
                            $fieldSQL .= ' AS ' . $alias;
                        }
                        $newFields[] = $fieldSQL;
                    }
                }
                if(count($newFields) > 0) {
                    $fields = $newFields;
                }
            }
            if(!is_array($fields)) {
                if($fields instanceof ISelect) {
                    $tablesInQuery = $fields->getTablesInQuery();
                    if(count($tablesInQuery) > 0) {
                        foreach($tablesInQuery as $table) {
                            if(!in_array($table, $this->tablesInQuery)) {
                                $this->tablesInQuery[] = $table;
                            }
                        }
                    }
                    $fields = '(' . $fields->getSQL() . ')';
                }
                if(!empty($alias)) {
                    $fields .= ' AS ' . $alias;
                }
                $fields = [$fields];
            }
            $this->select = $fields;
            return $this;
        }

        /**
         * Method to define table of select
         *
         * @param string $table name of Table
         * @param string $alias alias of Table
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function from(string $table, string $alias) :ISelect
        {
            $this->table = $table;
            $this->tableAlias = $alias;
            if(!in_array($table, $this->tablesInQuery)) {
                $this->tablesInQuery[] = $table;
            }
            return $this;
        }

        /**
         * Method to define many joins
         *
         * @param string $joinType type of Join
         * @param array $tables Tables to use
         * @param object $condition callback to use Criteria on condition
         * @param array $using
         * @param bool $crossJoin
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function join(string $joinType, array $tables, object $condition, array $using = [], bool $crossJoin = false) :ISelect
        {
            if(in_array($joinType, $this->joins)) {
                $this->join[] = compact('joinType', 'tables', 'condition', 'using', 'crossJoin');
                foreach($tables as $table) {
                    if(!in_array($table, $this->tablesInQuery)) {
                        $this->tablesInQuery[] = $table;
                    }
                }
                
                return $this;
            }
            throw new InvalidJoinTypeException();
        }

        /**
         * Method to define Where conditions
         * 
         * @param object $callback callback to configure conditions
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function where(object $callback) :ISelect
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->where = array_merge($this->where, $criteria->getWhere());
            $tablesInQuery = $criteria->getTablesInQuery();
            if(count($tablesInQuery) > 0) {
                foreach($tablesInQuery as $table) {
                    if(!in_array($table, $this->tablesInQuery)) {
                        $this->tablesInQuery[] = $table;
                    }
                }
            }
            return $this;
        }

        /**
         * Method to configure group by 
         * 
         * @param array|string $fields fields to group
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function groupBy($fields) :ISelect
        {
            $this->groupBy[] = $fields;
            return $this;
        }

        /**
         * Method to configure Group By with Rollup
         * 
         * @param bool $groupByWithRollup
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function groupByWithRollup(bool $groupByWithRollup) :ISelect
        {
            $this->groupByWithRollup = $groupByWithRollup;
            return $this;
        }

        /**
         * Method to configure conditions into Havind
         * 
         * @param object $callback
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function having(object $callback) :ISelect
        {
            $this->having = $callback;
            return $this;
        }

        /**
         * Method to order records
         * 
         * @param array|string $field Field(s) to order
         * @param string $order Order of records
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function orderBy($field, string $order = 'ASC') :ISelect
        {
            if(is_string($field)) {
                $field = [$field => $order];
            }
            $this->orderBy[] = $field;
            return $this;
        }

        /**
         * Method to configure order use with rollup
         * 
         * @param bool $orderByWithRollup
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function orderByWithRollup(bool $orderByWithRollup) :ISelect
        {
            $this->orderByWithRollup = $orderByWithRollup;
            return $this;
        }

        /**
         *  Method to set limit of records
         * 
         * @param int $limit
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function limit(int $limit) :ISelect
        {
            $this->limit = $limit;
            return $this;
        }

        /**
         * Method to define offset of records
         * 
         * @param int $offset
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function offset(int $offset) :ISelect
        {
            $this->offset = $offset;
            return $this;
        }

        /**
         * Method to return SQL
         * 
         * @return string
         */
        public function getSQL() :string
        {
            return sprintf($this->selectInstruction, implode('', [
                (!empty($this->selectRule) ? ' ' . $this->selectRule : '') .
                ($this->highPriority == true ? ' HIGH_PRIORITY' : '') .
                ($this->straightJoin == true ? ' STRAIGHT_JOIN' : '') .
                (count($this->sizeResult) > 0 ? ' ' . implode(' ', $this->sizeResult) : '') .
                ($this->noCache == true ? ' SQL_NO_CACHE' : '') .
                ($this->sqlCalcFoundRows == true ? ' SQL_CALC_FOUND_ROWS' : '') .
                ' ' . implode(',', $this->select)
            ]), implode('', [
                ' ' . $this->table . ' AS ' . $this->tableAlias,
                (count($this->join) > 0 ? ' ' . $this->mountJoin($this->join) : ''),
                (count($this->where) > 0 ? ' WHERE ' . $this->mountWhere($this->where) : ''),
                count($this->groupBy) > 0 ? $this->mountGroupBy($this->groupBy) : '',
                ($this->groupByWithRollup == true ? ' WITH ROLLUP' : ''),
                (is_object($this->having) > 0 ? ' ' . $this->mountHaving($this->having) : ''),
                (count($this->orderBy) > 0 ? ' ' . $this->mountOrderBy($this->orderBy) : ''),
                ($this->orderByWithRollup == true ? ' WITH ROLLUP' : ''),
                (!empty($this->limit) ? ' LIMIT ' . $this->limit : ''),
                (!empty($this->offset) ? ' OFFSET ' . $this->offset : '')
            ]));
        }

        /**
         * Method to return list of Tables in query
         * 
         * @return array
         */
        public function getTablesInQuery()
        {
            return $this->tablesInQuery;
        }

        /**
         * Method to mount Join
         * 
         * @return string
         */
        private function mountJoin(array $joins) :string
        {
            if(count($joins)) {
                foreach($joins as $index => $join) {
                    $criteria = $this->getCriteria();
                    $callback = $join['condition'];
                    $callback($criteria);
                    $joins[$index] = $this->mountJoinType($join['joinType']) . ' ' . $this->mountJoinTable($join['tables']) . ' ON (' . $this->mountWhere($criteria
                        ->getWhere()) . ')';
                }
            }
            return implode(' ', $joins);
        }

        /**
         * Method to mount Join Type
         * 
         * @return string
         */
        private function mountJoinType(string $joinType) :string
        {
            if($joinType == 'STRAIGHT') {
                return $joinType . '_JOIN';
            }
            return $joinType. ' JOIN';
        }

        /**
         * Method to return Join Table
         * 
         * @return string
         */
        private function mountJoinTable(array $tables) :string
        {
            foreach($tables as $index => $table) {
                if(!is_int($index)) {
                    $tables[$index] .= ' AS ' . $index;
                }
            }
            return '(' . implode(',', $tables) . ')';
        }

        /**
         * Method to return where conditions
         * 
         * @return string
         */
        private function mountWhere(array $where, string $argCondition = 'AND') :string
        {
            $parentCondition = $argCondition;
            $condition = [];
            $condition[] = '(';
            $conditionCommands = ['AND', 'OR'];
            foreach($where as $key => $content) {
                if(is_string($content) && in_array($content, $conditionCommands)) {
                    $argCondition = $content;
                    continue;
                }
                if(is_string($content)) {
                    $condition[] = $content;
                    if(($key+1) < count($where)) {
                        $condition[] = $parentCondition;
                    }
                    continue;
                }
                if(is_array($content)) {
                    $condition[] = $this->mountWhere($content, $argCondition);
                    if(($key+1) < count($where)) {
                        $condition[] = $parentCondition;
                    }
                    continue;
                }
            }
            $condition[] = ')';
            return implode(' ', $condition);
        }

        /**
         * Method to mount Group By
         * 
         * @return string
         */
        private function mountGroupBy($groupBy) :string
        {
            return 'GROUP BY ' . implode(',', $this->groupBy);
        }

        /**
         * Method to mount Having
         * 
         * @return string
         */
        private function mountHaving(object $callback) :string
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            return 'HAVING ' . $this->mountWhere($criteria->getWhere());
        }

        /**
         * Method to mount Order By
         * 
         * @return string
         */
        private function mountOrderBy(array $orderBy) :string
        {
            $orders = [];
            $ordersToSql = [];
            foreach($orderBy as $order) {
                $orders = array_merge($orders, $order);
            }
            foreach($orders as $name => $order) {
                $ordersToSql[] = $name . ' ' . $order;
            }
            return 'ORDER BY ' . implode(',', $ordersToSql);
        }
    }
}
