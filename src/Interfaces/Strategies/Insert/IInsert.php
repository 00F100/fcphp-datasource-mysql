<?php

namespace FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert
{
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\Interfaces\ICriteria;
    
    interface IInsert
    {
        /**
         * Method to define priority of Insert
         *
         * @param string $priority LOW_PRIORITY, DELAYED or HIGH_PRIORITY
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function priority(string $priority) :IInsert;

        /**
         * Method to define ignore of Insert
         *
         * @param bool $ignore true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function ignore(bool $ignore) :IInsert;

        /**
         * Method to define into of Insert
         *
         * @param bool $into true or false
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function into(bool $into) :IInsert;

        /**
         * Method to define table of Insert
         *
         * @param string $table Table name
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function from(string $table) :IInsert;

        /**
         * Method to define columns of Insert
         *
         * @param string $table List of Columns
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function columns($columns) :IInsert;

        /**
         * Method to define values of Insert
         *
         * @param string|array $key Single key or list of Values
         * @param $value string|null String to bind with $key
         * @return FcPhp\Datasource\MySQL\Interfaces\Strategies\Insert\IInsert
         */
        public function values($key, $value = null) :IInsert;

        /**
         * Method to return SQL Insert
         *
         * @return string
         */
        public function getSQL();

        /**
         * Method to return list of Tables in query
         * 
         * @return array
         */
        public function getTablesInQuery();
    }
}
