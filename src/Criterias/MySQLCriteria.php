<?php

namespace FcPhp\Datasource\MySQL\Criterias
{
    use FcPhp\Datasource\Criteria;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\Interfaces\IFactory;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLCriteria;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;

    class MySQLCriteria extends Criteria implements ICriteria, IMySQLCriteria
    {
        protected $tablesInQuery = [];
        protected $where = [];
        protected $conditionString = '"%s"';
        protected $conditionInt = '%s';
        protected $conditionAnd = 'AND';
        protected $conditionOr = 'OR';
        protected $conditionSpace = ' ';

        /**
         * Method to add "or" condition
         *
         * @param object $callback Callback to add conditions "or"
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function or(object $callback) :ICriteria
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->where[] = $this->conditionOr;
            $this->where[] = $criteria->getWhere();
            unset($criteria);
            unset($callback);
            return $this;
        }

        /**
         * Method to add "and" condition
         *
         * @param object $callback Callback to add conditions "and"
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function and(object $callback) :ICriteria
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->where[] = $this->conditionAnd;
            $this->where[] = $criteria->getWhere();
            unset($criteria);
            unset($callback);
            return $this;
        }

        /**
         * Method to add condition
         *
         * @param string $field Field to add condition
         * @param string $condition Condition to compare
         * @param string|int|bool $value Value to compare
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function condition(string $field, string $condition, $value, bool $isColumn = false, bool $parentheses = false) :ICriteria
        {
            if(is_string($value) && !$isColumn) {
                $value = sprintf($this->conditionString, $value);
            }
            if(is_int($value) || $isColumn) {
                $value = sprintf($this->conditionInt, $value);
                if($parentheses) {
                    $value = '(' . $value . ')';
                }
            }
            if($value instanceof ISelect) {
                foreach($value->getTablesInQuery() as $table) {
                    if(!in_array($table, $this->tablesInQuery)) {
                        $this->tablesInQuery[] = $table;
                    }
                }
                $value = '(' . $value->getSQL() . ')';
            }
            $this->where[] = $field . $this->conditionSpace . $condition . $this->conditionSpace . $value;
            return $this;
        }

        public function getWhere() :array
        {
            return $this->where;
        }
        public function getTablesInQuery()
        {
            return $this->tablesInQuery;
        }
    }
}
