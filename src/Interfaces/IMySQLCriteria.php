<?php

namespace FcPhp\Datasource\MySQL\Interfaces
{
    use FcPhp\Datasource\Interfaces\ICriteria;
    
    interface IMySQLCriteria
    {
        /**
         * Method to add "or" condition
         *
         * @param object $callback Callback to add conditions "or"
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function or(object $callback) :ICriteria;

        /**
         * Method to add "and" condition
         *
         * @param object $callback Callback to add conditions "and"
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function and(object $callback) :ICriteria;

        /**
         * Method to add condition
         *
         * @param string $field Field to add condition
         * @param string $condition Condition to compare
         * @param string|int|bool $value Value to compare
         * @return FcPhp\Datasource\Interfaces\ICriteria
         */
        public function condition($field, string $condition, $value, bool $isColumn = false, bool $parentheses = false) :ICriteria;

        /**
         * Method to return where
         *
         * @return array
         */
        public function getWhere() :array;

        /**
         * Method to return list of table(s) used on query
         *
         * @return array
         */
        public function getTablesInQuery() :array;
    }
}
