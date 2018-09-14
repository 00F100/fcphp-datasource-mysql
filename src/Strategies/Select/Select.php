<?php

namespace FcPhp\Datasource\MySQL\Strategies\Select
{
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;

    class Select implements ISelect
    {
        protected $selectInstruction = 'SELECT %s FROM %s';
        // protected $selectInstruction = 'SELECT [selectRule] [highPriority] [straightJoin] [sizeResult] [noCache] [sqlCalcFoundRows] [select] FROM [table] AS [tableAlias] [join] [where] [groupBy] [groupByWithRollup] [having] [orderBy] [orderByWithRollup] [limit] [offset] ';

        protected $selectRule;
        protected $selectRules = ['ALL', 'DISTINCT', 'DISTINCTROW'];
        protected $highPriority;
        protected $straightJoin;
        protected $sizeResult = [];
        protected $sizeResults = ['SQL_SMALL_RESULT', 'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT'];
        protected $noCache;
        protected $sqlCalcFoundRows;
        protected $select = [];
        protected $table;
        protected $tableAlias;
        protected $join = [];
        protected $joins = ['LEFT', 'RIGHT', 'INNER', 'OUTER', 'NATURAL', 'STRAIGHT'];
        protected $where = [];
        protected $groupBy = [];
        protected $groupByWithRollup;
        protected $having = [];
        protected $orderBy = [];
        protected $orderByWithRollup;
        protected $limit;
        protected $offset;

        public function selectRule(string $rule)
        {
            if(in_array($rule, $this->selectRules)) {
                $this->selectRule = $rule;
            }
            return $this;
        }

        public function highPriority(bool $highPriority)
        {
            $this->highPriority = $highPriority;
            return $this;
        }

        public function straightJoin(bool $straightJoin)
        {
            $this->straightJoin = $straightJoin;
            return $this;
        }

        public function sizeResult(string $size)
        {
            if(in_array($size, $this->sizeResults)) {
                $this->sizeResult[] = $size;
            }
            return $this;
        }

        public function noCache(bool $noCache)
        {
            $this->noCache = $noCache;
            return $this;
        }

        public function sqlCalcFoundRows(bool $sqlCalcFoundRows)
        {
            $this->sqlCalcFoundRows = $sqlCalcFoundRows;
            return $this;
        }

        public function select($fields) :IStrategy
        {
            $this->mode = self::DATASOURCE_MYSQL_STRATEGY_SELECT;
            if(!is_array($fields)) {
                $fields = [$fields];
            }
            $this->select = $fields;
            return $this;
        }

        public function from(string $table, string $alias) :IStrategy
        {
            $this->table = $table;
            $this->tableAlias = $alias;
            return $this;
        }

        public function join(string $joinType, array $tables, object $condition, array $using = [], bool $crossJoin = false)
        {
            if(in_array($joinType, $this->joins)) {
                $this->join[] = compact('joinType', 'tables', 'condition', 'using', 'crossJoin');
                return $this;
            }
            throw new InvalidJoinTypeException();
        }

        public function where(object $callback) :IStrategy
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->where = array_merge($this->where, $criteria->getWhere());
            return $this;
        }

        public function groupBy(string $field)
        {
            $this->groupBy[] = $field;
            return $this;
        }

        public function groupByWithRollup(bool $groupByWithRollup)
        {
            $this->groupByWithRollup = $groupByWithRollup;
            return $this;
        }

        public function having(string $field, string $condition, string $value)
        {
            $this->having[] = compact('field', 'condition', 'value');
            return $this;
        }

        public function orderBy(string $field, string $order)
        {
            $this->orderBy[] = compact('field', 'order');
            return $this;
        }

        public function orderByWithRollup(bool $orderByWithRollup)
        {
            $this->orderByWithRollup = $orderByWithRollup;
            return $this;
        }

        public function limit(int $limit)
        {
            $this->limit = $limit;
            return $this;
        }

        public function offset(int $offset)
        {
            $this->offset = $offset;
            return $this;
        }



        private function mountJoin(array $joins)
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
            return $joins;
        }

        private function mountJoinType(string $joinType)
        {
            if($joinType == 'STRAIGHT') {
                return $joinType . '_JOIN';
            }
            return $joinType. ' JOIN';
        }

        private function mountJoinTable($tables)
        {
            if(is_array($tables)) {
                foreach($tables as $index => $table) {
                    if(!is_int($index)) {
                        $tables[$index] .= ' AS ' . $index;
                    }
                }
                return '(' . implode(',', $tables) . ')';
            }
            return $tables;
        }

        private function mountWhere(array $where, string $argCondition = 'AND')
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

        private function mountConditionWhere(array $where, string $condition)
        {

        }

        private function mountGroupBy(array $groupBy)
        {
            return $groupBy;
        }

        private function mountHaving(array $having)
        {
            return $having;
        }

        private function mountOrderBy(array $orderBy)
        {
            return $orderBy;
        }
    }
}
