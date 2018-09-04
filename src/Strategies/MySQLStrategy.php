<?php

namespace FcPhp\Datasource\MySQL\Strategies
{
    use FcPhp\Datasource\Strategy;
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLStrategy;

    class MySQLStrategy extends Strategy implements IStrategy, IMySQLStrategy
    {
        protected $selectInstruction = 'SELECT [selectRule] [highPriority] [straightJoin] [sizeResult] [noCache] [select] FROM [table] AS [tableAlias] [join] [where] [groupBy] [groupByWithRollup] [having] [orderBy] [orderByWithRollup] [limit] [offset] ';

        protected $selectRule;
        protected $selectRules = ['ALL', 'DISTINCT', 'DISTINCTROW'];
        protected $highPriority;
        protected $straightJoin;
        protected $sizeResult = [];
        protected $sizeResults = ['SQL_SMALL_RESULT', 'SQL_BIG_RESULT', 'SQL_BUFFER_RESULT'];
        protected $noCache;
        protected $select = [];
        protected $table;
        protected $tableAlias;
        protected $join = [];
        protected $where = [];
        protected $groupBy = [];
        protected $groupByWithRollup;
        protected $having = [];
        protected $orderBy = [];
        protected $orderByWithRollup;
        protected $limit;
        protected $offset;

        public function getSQL()
        {
            $select = $this->selectInstruction;
            $select = str_replace('[selectRule]', (!empty($this->selectRule) ? $this->selectRule : ''), $select);
            $select = str_replace('[highPriority]', ($this->highPriority == true ? 'HIGH_PRIORITY' : ''), $select);
            $select = str_replace('[straightJoin]', ($this->straightJoin == true ? 'STRAIGHT_JOIN' : ''), $select);
            $select = str_replace('[sizeResult]', (count($this->sizeResult) > 0 ? implode(' ', $this->sizeResult) : ''), $select);
            $select = str_replace('[noCache]', ($this->noCache == true ? 'SQL_NO_CACHE' : ''), $select);
            $select = str_replace('[select]', implode(' ', $this->select), $select);
            $select = str_replace('[table]', $this->table, $select);
            $select = str_replace('[tableAlias]', $this->tableAlias, $select);
            $select = str_replace('[join]', (count($this->join) > 0 ? implode(' ', $this->mountJoin($this->join)) : ''), $select);
            $select = str_replace('[where]', (count($this->where) > 0 ? implode(' ', $this->mountWhere($this->where)) : ''), $select);
            $select = str_replace('[groupBy]', (count($this->groupBy) > 0 ? implode(' ', $this->mountGroupBy($this->groupBy)) : ''), $select);
            $select = str_replace('[groupByWithRollup]', ($this->groupByWithRollup == true ? 'WITH ROLLUP' : ''), $select);
            $select = str_replace('[having]', (count($this->having) > 0 ? implode(' ', $this->mountHaving($this->having)) : ''), $select);
            $select = str_replace('[orderBy]', (count($this->orderBy) > 0 ? implode(' ', $this->mountOrderBy($this->orderBy)) : ''), $select);
            $select = str_replace('[orderByWithRollup]', ($this->orderByWithRollup == true ? 'WITH ROLLUP' : ''), $select);
            $select = str_replace('[limit]', (!empty($this->limit) ? 'LIMIT ' . $this->limit : ''), $select);
            $select = str_replace('[offset]', (!empty($this->offset) ? 'OFFSET ' . $this->offset : ''), $select);
        }

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

        public function select($fields)
        {
            if(!is_array($fields)) {
                $fields = [$fields];
            }
            $this->select = $fields;
            return $this;
        }

        public function from(string $table, string $alias)
        {
            $this->table = $table;
            $this->tableAlias = $alias;
            return $this;
        }

        public function join(string $joinType, string $field, string $condition, string $value)
        {
            $this->join[] = compact('joinType', 'field', 'condition', 'value');
            return $this;
        }

        public function where(object $callback)
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
                    $joins[$index] = implode(' ', $join);
                }
            }
            return $joins;
        }

        private function mountWhere(array $where)
        {
            return $where;
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
