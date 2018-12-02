<?php

namespace FcPhp\Datasource\MySQL\Strategies\Update
{
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Update\IUpdate;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;

    class Update implements IUpdate
    {
        /**
         * @var string Update instruction
         */
        protected $updateInstruction = 'UPDATE%s';

        /**
         * @var FcPhp\Datasource\Interfaces\IStrategy
         */
        protected $strategy;

        /**
         * @var bool Priority of Update
         */
        protected $priority;

        /**
         * @var string Table name
         */
        protected $table;

        /**
         * @var string Alias table name
         */
        protected $alias;

        /**
         * @var array List of enable priorities
         */
        protected $priorityList = ['LOW_PRIORITY'];

        /**
         * @var array list of fields and values to apply
         */
        protected $values = [];

        /**
         * @var array conditions on Where clouse
         */
        protected $where = [];

        /**
         * @var array Order By fields
         */
        protected $orderBy = [];

        /**
         * @var int Limit of records
         */
        protected $limit;

        /**
         * @var array List of Tables in query
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
         * Method to define priority of Insert
         *
         * @param string $priority LOW_PRIORITY, DELAYED or HIGH_PRIORITY
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Update\IUpdate
         */
        public function priority(string $priority) :IUpdate
        {
            if(in_array($priority, $this->priorityList)) {
                $this->priority = $priority;
            }
            return $this;
        }

        /**
         * Method to define table of Update
         *
         * @param string $table Table name
         * @param string $alias Alias table name
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Update\IUpdate
         */
        public function from(string $table, string $alias) :IUpdate
        {
            $this->table = $table;
            $this->alias = $alias;
            $this->tablesInQuery[] = $table;
            return $this;
        }

        /**
         * Method to define values of Update
         *
         * @param string|array $key Single key or list of Values
         * @param $value string|null String to bind with $key
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Update\IUpdate
         */
        public function values($key, $value = null) :IUpdate
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
         * Method to define Where conditions
         * 
         * @param object $callback callback to configure conditions
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Update\IUpdate
         */
        public function where(object $callback) :IUpdate
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
         * Method to return SQL Update
         *
         * @return string
         */
        public function getSQL() :string
        {
            return sprintf($this->updateInstruction, (
                (!empty($this->priority) ? ' ' . $this->priority : '') .
                ' `' . $this->table . '` AS ' . $this->alias
            ));
        }

        /**
         * Method to return instance of Criteria
         *
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function getCriteria() :ICriteria
        {
            return $this->strategy->getCriteria();
        }

        /**
         * Method to return list of Tables in query
         * 
         * @return array
         */
        public function getTablesInQuery() :array
        {
            return $this->tablesInQuery;
        }

        private function mountValues(array $values)
        {
            d($values, true);
            $count = 0;
            $total = (count($values)-1);
            $return = [];
            foreach($values as $column => $value) {
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
    }
}
