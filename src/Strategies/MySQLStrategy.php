<?php

namespace FcPhp\Datasource\MySQL\Strategies
{
    use FcPhp\Datasource\Interfaces\IStrategy;

    class MySQL implements IStrategy
    {
        private $select = [];
        private $table;
        private $tableAlias;

        public function select($fields)
        {
            if(!is_array($fields)) {
                $fields = [$fields];
            }
            $this->select = $fields;
            return $this;
        }

        public function from(string $table, string $alias = null)
        {
            $this->table = $table;
            $this->tableAlias = $alias;
            return $this;
        }

        public function join(string $join, string $field, string $condition, string $value)
        {
            $this->join[] = compact('join', 'field', 'condition', 'value');
            return $this;
        }

        public function or(object $clousure)
        {
            // $this->where[] = ['or' => $clousure($this)];
            return $this;
        }

        public function and(object $clousure)
        {
            // $this->where[] = ['and' => $clousure($this)];
            return $this;
        }

        public function where(string $field, string $condition, string $value)
        {
            // $this->where[] = ['and' => compact('field', 'condition', 'value')];
            return $this;
        }
    }
}
