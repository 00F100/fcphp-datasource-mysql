<?php

namespace FcPhp\Datasource\MySQL
{
    use FcPhp\Datasource\Datasource;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQL;
    use FcPhp\Datasource\Interfaces\IQuery;

    class MySQL extends Datasource implements IMySQL
    {
        /**
         * Method to execute query
         *
         * @return array
         */
        public function execute($query) :array
        {
            $queryFromStrategy = $this->strategy->execute($query);

            // $queryFromStrategy['sql']
            // $queryFromStrategy['tables']

            // verify tables has up-to-date
            // if(up-to-date) {
                // return cache
            // }else{
                // update cache
                // return data
            // }
        }
    }
}
