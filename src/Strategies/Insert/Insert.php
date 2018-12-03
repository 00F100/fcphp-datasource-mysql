<?php

namespace FcPhp\Datasource\MySQL\Strategies\Insert
{
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;

    class Insert implements IInsert
    {
        /**
         * @var string Insert instruction
         */
        protected $insertInstruction = 'INSERT%s';

        /**
         * @var bool Priority of Insert
         */
        protected $priority;

        /**
         * @var array List of enable priorities
         */
        protected $priorityList = ['LOW_PRIORITY', 'DELAYED', 'HIGH_PRIORITY'];

        /**
         * @var bool Ignore Insert
         */
        protected $ignore = false;

        /**
         * @var bool Into Insert
         */
        protected $into = true;

        /**
         * @var string Table name
         */
        protected $table;

        /**
         * @var array List of Columns
         */
        protected $columns = [];

        /**
         * @var array List of Values
         */
        protected $values = [];

        /**
         * @var array List of commands on duplicate key
         */
        protected $duplicateKey = [];

        /**
         * @var array List of Tables in query
         */
        protected $tablesInQuery = [];

        /**
         * Method to define priority of Insert
         *
         * @param string $priority LOW_PRIORITY, DELAYED or HIGH_PRIORITY
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function priority(string $priority) :IInsert
        {
            if(in_array($priority, $this->priorityList)) {
                $this->priority = $priority;
            }
            return $this;
        }

        /**
         * Method to define ignore of Insert
         *
         * @param bool $ignore true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function ignore(bool $ignore) :IInsert
        {
            $this->ignore = $ignore;
            return $this;
        }

        /**
         * Method to define into of Insert
         *
         * @param bool $into true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function into(bool $into) :IInsert
        {
            $this->into = $into;
            return $this;
        }

        /**
         * Method to define table of Insert
         *
         * @param string $table Table name
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function from(string $table) :IInsert
        {
            $this->table = $table;
            $this->tablesInQuery[] = $table;
            return $this;
        }

        /**
         * Method to define columns of Insert
         *
         * @param string $table List of Columns
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function columns($columns) :IInsert
        {
            if(!is_array($columns)) {
                $columns = [$columns];
            }
            $this->columns = $columns;
            return $this;
        }

        /**
         * Method to define values of Insert
         *
         * @param string|array $key Single key or list of Values
         * @param $value string|null String to bind with $key
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function values($key, $value = null) :IInsert
        {
            if(is_array($key)) {
                foreach($key as $index => $value) {
                    $this->values[$index] = $value;
                }
            }else{
                $this->values[$key] = $value;
            }
            return $this;
        }

        /**
         * Method to define values of Insert on duplicate key has found
         *
         * @param string|array $key Single key or list of Duplicate Keys
         * @param $value string|null String to bind with $key
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function duplicateKey($key, $value = null) :IInsert
        {
            if(is_array($key)) {
                foreach($key as $index => $value) {
                    $this->duplicateKey[$index] = $value;
                }
            }else{
                $this->duplicateKey[$key] = $value;
            }
            return $this;
        }

        /**
         * Method to return SQL Insert
         *
         * @return string
         */
        public function getSQL()
        {
            return sprintf($this->insertInstruction, (
                (!empty($this->priority) ? ' ' . $this->priority : '') .
                ($this->ignore ? ' IGNORE' : '') .
                ($this->into ? ' INTO' : '') .
                (!empty($this->table) ? ' `' . $this->table . '`' : '') .
                (count($this->columns) > 0 ? ' (' . $this->mountColumns($this->columns) . ')' : '') .
                (count($this->values) > 0 ? ' VALUES (' . $this->mountValues($this->columns, $this->values) . ')' : '') .
                (count($this->duplicateKey) > 0 ? ' ON DUPLICATE KEY UPDATE ' . $this->mountDuplicateKey($this->duplicateKey) : '')
            ));
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
         * Method to return columns for SQL Insert
         *
         * @param array $columns List of Columns
         * @return string
         */
        private function mountColumns(array $columns)
        {
            return '`' . implode('`,`', $columns) . '`';
        }

        /**
         * Method to return duplicate key actions
         *
         * @param array $duplicateKey List of duplicate keys
         * @return string
         */
        private function mountDuplicateKey(array $duplicateKey) :string
        {
            $count = 0;
            $total = (count($duplicateKey)-1);
            $return = [];
            foreach($duplicateKey as $column => $value) {
                $return[] = '`' . $column . '` ';
                if(is_int($value)) {
                    $return[] = '= ' . $value;
                }else{
                    if(is_bool($value)) {
                        $return[] = '= ' . ($value === true ? 1 : 0);
                    }else{
                        $return[] = '= "' . $value . '"';
                    }
                }
                if($count < $total) {
                    $return[] = ',';
                }
                $count++;
            }
            return implode('', $return);
        }

        /**
         * Method to return values for SQL Insert
         *
         * @param array $columns List of Columns
         * @param array $values List of Values
         * @return string
         */
        private function mountValues(array $columns, array $values)
        {
            $return = [];
            foreach($values as $field => $value) {
                if(is_int($value)) {
                    $return[$field] = $value;
                }else{
                    if(is_bool($value)) {
                        $return[$field] = $value ? 1 : 0;
                    }else{
                        if($value instanceof ISelect) {
                            $this->tablesInQuery = array_merge($this->tablesInQuery, $value->getTablesInQuery());
                            return $value->getSQL();
                        }else{
                            $return[$field] = '"' . $value . '"';
                        }
                    }
                }
            }
            uksort($return, function($key1, $key2) use ($columns) {
                return (array_search($key1, $columns) > array_search($key2, $columns));
            });
            return implode(',', $return);
        }
    }
}
