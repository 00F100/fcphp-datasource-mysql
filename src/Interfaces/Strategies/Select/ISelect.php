<?php

namespace FcPhp\Datasource\MySQL\Interfaces\Strategies\Select
{
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;
    
    interface ISelect
    {
        /**
         * Method to construct instance
         *
         * @param FcPhp\Datasource\Interfaces\IStrategy
         * @return void
         */
        public function __construct(IStrategy $strategy);

        /**
         * Method to return instance of Criteria
         *
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function getCriteria();

        /**
         * Method to configure Select Rule
         *
         * @param string $rule Rule to use
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function selectRule(string $rule) :ISelect;

        /**
         * Method to define High Priority
         *
         * @param bool $highPriority define true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function highPriority(bool $highPriority) :ISelect;

        /**
         * Method to define Straight Join
         *
         * @param bool $straightJoin define true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function straightJoin(bool $straightJoin) :ISelect;

        /**
         * Method to define size result
         *
         * @param string $size Configure Size Result
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function sizeResult(string $size) :ISelect;

        /**
         * Method to configure to (non) use cache
         *
         * @param bool $noCache define true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function noCache(bool $noCache) :ISelect;

        /**
         * Method to calc rows of records
         *
         * @param bool $sqlCalcFoundRows configure to return calc of records
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function sqlCalcFoundRows(bool $sqlCalcFoundRows) :ISelect;

        /**
         * Method to confire fields to select
         *
         * @param string|array $fields Field(s) to select
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function select($fields) :ISelect;

        /**
         * Method to define table of select
         *
         * @param string $table name of Table
         * @param string $alias alias of Table
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function from(string $table, string $alias) :ISelect;

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
        public function join(string $joinType, array $tables, object $condition, array $using = [], bool $crossJoin = false) :ISelect;

        /**
         * Method to define Where conditions
         * 
         * @param object $callback callback to configure conditions
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function where(object $callback) :ISelect;

        /**
         * Method to configure group by 
         * 
         * @param array|string $fields fields to group
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function groupBy($fields) :ISelect;

        /**
         * Method to configure Group By with Rollup
         * 
         * @param bool $groupByWithRollup
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function groupByWithRollup(bool $groupByWithRollup) :ISelect;

        /**
         * Method to configure conditions into Havind
         * 
         * @param object $callback
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function having(object $callback) :ISelect;

        /**
         * Method to order records
         * 
         * @param array|string $field Field(s) to order
         * @param string $order Order of records
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function orderBy($field, string $order = 'ASC') :ISelect;

        /**
         * Method to configure order use with rollup
         * 
         * @param bool $orderByWithRollup
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function orderByWithRollup(bool $orderByWithRollup) :ISelect;

        /**
         *  Method to set limit of records
         * 
         * @param int $limit
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function limit(int $limit) :ISelect;

        /**
         * Method to define offset of records
         * 
         * @param int $offset
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect
         */
        public function offset(int $offset) :ISelect;

        /**
         * Method to return SQL
         * 
         * @return string
         */
        public function getSQL() :string;
    }
}
