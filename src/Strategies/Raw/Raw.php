<?php

namespace FcPhp\Datasource\MySQL\Strategies\Raw
{
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Raw\IRaw;

    class Raw implements IRaw
    {
        public function setSQL(string $sql) :IRaw
        {
            $this->sql = $sql;
            return $this;
        }

        public function getSQL() :string
        {
            return $this->sql;
        }
    }
}
