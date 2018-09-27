<?php

namespace FcPhp\Datasource\MySQL\Strategies\Select
{
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;
    use FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException;

    class Select implements ISelect
    {
        protected $selectInstruction = 'SELECT%s FROM%s';

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
        protected $strategy;

        public function __construct(IStrategy $strategy)
        {
            $this->strategy = $strategy;
        }

        public function getCriteria()
        {
            return $this->strategy->getCriteria();
        }

        public function selectRule(string $rule) :ISelect
        {
            if(in_array($rule, $this->selectRules)) {
                $this->selectRule = $rule;
            }
            return $this;
        }

        public function highPriority(bool $highPriority) :ISelect
        {
            $this->highPriority = $highPriority;
            return $this;
        }

        public function straightJoin(bool $straightJoin) :ISelect
        {
            $this->straightJoin = $straightJoin;
            return $this;
        }

        public function sizeResult(string $size) :ISelect
        {
            if(in_array($size, $this->sizeResults)) {
                $this->sizeResult[] = $size;
            }
            return $this;
        }

        public function noCache(bool $noCache) :ISelect
        {
            $this->noCache = $noCache;
            return $this;
        }

        public function sqlCalcFoundRows(bool $sqlCalcFoundRows) :ISelect
        {
            $this->sqlCalcFoundRows = $sqlCalcFoundRows;
            return $this;
        }

        public function select($fields) :ISelect
        {
            if(!is_array($fields)) {
                $fields = [$fields];
            }
            $this->select = $fields;
            return $this;
        }

        public function from(string $table, string $alias) :ISelect
        {
            $this->table = $table;
            $this->tableAlias = $alias;
            return $this;
        }

        public function join(string $joinType, array $tables, object $condition, array $using = [], bool $crossJoin = false) :ISelect
        {
            if(in_array($joinType, $this->joins)) {
                $this->join[] = compact('joinType', 'tables', 'condition', 'using', 'crossJoin');
                return $this;
            }
            throw new InvalidJoinTypeException();
        }

        public function where(object $callback) :ISelect
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->where = array_merge($this->where, $criteria->getWhere());
            return $this;
        }

        public function groupBy($fields) :ISelect
        {
            $this->groupBy[] = $fields;
            return $this;
        }

        public function groupByWithRollup(bool $groupByWithRollup) :ISelect
        {
            $this->groupByWithRollup = $groupByWithRollup;
            return $this;
        }

        public function having(string $field, string $condition, string $value) :ISelect
        {
            $this->having[] = compact('field', 'condition', 'value');
            return $this;
        }

        public function orderBy(string $field, string $order) :ISelect
        {
            $this->orderBy[] = compact('field', 'order');
            return $this;
        }

        public function orderByWithRollup(bool $orderByWithRollup) :ISelect
        {
            $this->orderByWithRollup = $orderByWithRollup;
            return $this;
        }

        public function limit(int $limit) :ISelect
        {
            $this->limit = $limit;
            return $this;
        }

        public function offset(int $offset) :ISelect
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

        private function mountGroupBy($groupBy)
        {
            if(count($this->groupBy) > 0) {
                return 'GROUP BY ' . implode(',', $this->groupBy);
            }
        }

        private function mountHaving(array $having)
        {
            return $having;
        }

        private function mountOrderBy(array $orderBy)
        {
            return $orderBy;
        }

        public function getSQL()
        {

        // protected $selectInstruction = 'SELECT [selectRule] [highPriority] [straightJoin] [sizeResult] [noCache] [sqlCalcFoundRows] [select] FROM [table] AS [tableAlias] [join] [where] [groupBy] [groupByWithRollup] [having] [orderBy] [orderByWithRollup] [limit] [offset] ';

            // protected $selectInstruction = 'SELECT %s FROM %s';
// d($this->select, true);

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
                (count($this->join) > 0 ? ' ' . implode(' ', $this->mountJoin($this->join)) : ''),
                (count($this->where) > 0 ? ' WHERE ' . $this->mountWhere($this->where) : ''),
                $this->mountGroupBy($this->groupBy),
                ($this->groupByWithRollup == true ? ' WITH ROLLUP' : ''),
                (count($this->having) > 0 ? ' ' . implode(' ', $this->mountHaving($this->having)) : ''),
                (count($this->orderBy) > 0 ? ' ' . implode(' ', $this->mountOrderBy($this->orderBy)) : ''),
                ($this->orderByWithRollup == true ? ' WITH ROLLUP' : ''),
                (!empty($this->limit) ? ' LIMIT ' . $this->limit : ''),
                (!empty($this->offset) ? ' OFFSET ' . $this->offset : '')
            ]));

            // $select = $this->selectInstruction;
            // $select .= str_replace('[selectRule]', (!empty($this->selectRule) ? $this->selectRule : ''), $select);
            // $select = str_replace('[selectRule]', (!empty($this->selectRule) ? $this->selectRule : ''), $select);
            // $select = str_replace('[highPriority]', ($this->highPriority == true ? 'HIGH_PRIORITY' : ''), $select);
            // $select = str_replace('[straightJoin]', ($this->straightJoin == true ? 'STRAIGHT_JOIN' : ''), $select);
            // $select = str_replace('[sizeResult]', (count($this->sizeResult) > 0 ? implode(' ', $this->sizeResult) : ''), $select);
            // $select = str_replace('[noCache]', ($this->noCache == true ? 'SQL_NO_CACHE' : ''), $select);
            // $select = str_replace('[sqlCalcFoundRows]', ($this->sqlCalcFoundRows == true ? 'SQL_CALC_FOUND_ROWS' : ''), $select);
            // $select = str_replace('[select]', implode(',', $this->select), $select);
            // $select = str_replace('[table]', $this->table, $select);
            // $select = str_replace('[tableAlias]', $this->tableAlias, $select);
            // $select = str_replace('[join]', (count($this->join) > 0 ? implode(' ', $this->mountJoin($this->join)) : ''), $select);
            // $select = str_replace('[where]', (count($this->where) > 0 ? 'WHERE ' . $this->mountWhere($this->where) : ''), $select);
            // $select = str_replace('[groupBy]', (count($this->groupBy) > 0 ? implode(' ', $this->mountGroupBy($this->groupBy)) : ''), $select);
            // $select = str_replace('[groupByWithRollup]', ($this->groupByWithRollup == true ? 'WITH ROLLUP' : ''), $select);
            // $select = str_replace('[having]', (count($this->having) > 0 ? implode(' ', $this->mountHaving($this->having)) : ''), $select);
            // $select = str_replace('[orderBy]', (count($this->orderBy) > 0 ? implode(' ', $this->mountOrderBy($this->orderBy)) : ''), $select);
            // $select = str_replace('[orderByWithRollup]', ($this->orderByWithRollup == true ? 'WITH ROLLUP' : ''), $select);
            // $select = str_replace('[limit]', (!empty($this->limit) ? 'LIMIT ' . $this->limit : ''), $select);
            $select = str_replace('[offset]', (!empty($this->offset) ? 'OFFSET ' . $this->offset : ''), $select);

            return $select;
        }
    }
}
