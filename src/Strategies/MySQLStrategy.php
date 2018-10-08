<?php

namespace FcPhp\Datasource\MySQL\Strategies
{
    use FcPhp\Datasource\Strategy;
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\Interfaces\IFactory;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLStrategy;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLFactory;
    use FcPhp\Datasource\MySQL\Exceptions\InvalidJoinTypeException;
    use FcPhp\Datasource\MySQL\Exceptions\InvalidMethodException;
    use FcPhp\Datasource\MySQL\Interfaces\Strategies\Select\ISelect;

    class MySQLStrategy extends Strategy implements IStrategy, IMySQLStrategy
    {
        const DATASOURCE_MYSQL_STRATEGY_SELECT = 0;
        const DATASOURCE_MYSQL_STRATEGY_INSERT = 1;
        const DATASOURCE_MYSQL_STRATEGY_UPDATE = 2;
        const DATASOURCE_MYSQL_STRATEGY_DELETE = 2;
        const DATASOURCE_MYSQL_STRATEGY_CREATE = 3;
        const DATASOURCE_MYSQL_STRATEGY_ALTER = 4;
        const DATASOURCE_MYSQL_STRATEGY_DROP = 5;
        const DATASOURCE_MYSQL_STRATEGY_RENAME = 6;
        const DATASOURCE_MYSQL_STRATEGY_TRUNCATE = 7;
        const DATASOURCE_MYSQL_STRATEGY_CALL = 7;
        const DATASOURCE_MYSQL_STRATEGY_TRANSACTION = 8;
        const DATASOURCE_MYSQL_STRATEGY_LOCK = 9;
        const DATASOURCE_MYSQL_STRATEGY_PREPARE = 10;
        const DATASOURCE_MYSQL_STRATEGY_SET = 11;
        const DATASOURCE_MYSQL_STRATEGY_EXECUTE = 12;
        const DATASOURCE_MYSQL_STRATEGY_DEALLOCATE = 13;
        const DATASOURCE_MYSQL_STRATEGY_BEGIN = 14;
        const DATASOURCE_MYSQL_STRATEGY_REPEAT = 14;
        const DATASOURCE_MYSQL_STRATEGY_DELIMITER = 15;

        protected $availableMethods = [
            self::DATASOURCE_MYSQL_STRATEGY_SELECT => 'select',
            // self::DATASOURCE_MYSQL_STRATEGY_INSERT => 'insert',
            // self::DATASOURCE_MYSQL_STRATEGY_UPDATE => 'update',
            // self::DATASOURCE_MYSQL_STRATEGY_DELETE => 'delete',
            // self::DATASOURCE_MYSQL_STRATEGY_CREATE => 'create',
            // self::DATASOURCE_MYSQL_STRATEGY_ALTER => 'alter',
            // self::DATASOURCE_MYSQL_STRATEGY_DROP => 'drop',
            // self::DATASOURCE_MYSQL_STRATEGY_RENAME => 'rename',
            // self::DATASOURCE_MYSQL_STRATEGY_TRUNCATE => 'truncate',
            // self::DATASOURCE_MYSQL_STRATEGY_CALL => 'call',
            // self::DATASOURCE_MYSQL_STRATEGY_TRANSACTION => 'transaction',
            // self::DATASOURCE_MYSQL_STRATEGY_LOCK => 'lock',
            // self::DATASOURCE_MYSQL_STRATEGY_PREPARE => 'prepare', 
            // self::DATASOURCE_MYSQL_STRATEGY_SET => 'set', 
            // self::DATASOURCE_MYSQL_STRATEGY_EXECUTE => 'execute', 
            // self::DATASOURCE_MYSQL_STRATEGY_DEALLOCATE => 'deallocate', 
            // self::DATASOURCE_MYSQL_STRATEGY_BEGIN => 'begin', 
            // self::DATASOURCE_MYSQL_STRATEGY_REPEAT => 'repeat', 
            // self::DATASOURCE_MYSQL_STRATEGY_DELIMITER => 'delimiter', 

        ];
        protected $mode;
        protected $mySQLFactory;

        public function __construct(string $criteria, IFactory $factory, IMySQLFactory $mySQLFactory)
        {
            $this->mySQLFactory = $mySQLFactory;
            $this->mySQLFactory->setStrategy($this);
            return parent::__construct($criteria, $factory);
        }

        public function __call(string $method, array $args)
        {
            if(in_array($method, $this->availableMethods)) {
                $this->mode = $this->availableMethods[constant('FcPhp\Datasource\MySQL\Strategies\MySQLStrategy::DATASOURCE_MYSQL_STRATEGY_' . strtoupper($method))];
                $instance = $this->mySQLFactory->get($method);
                return call_user_func_array([$instance, $method], $args);
            }
            throw new InvalidMethodException();
        }

        public function execute(ISelect $query)
        {
            return [
                'tables' => $query->getTablesInQuery(),
                'sql' => $query->getSQL(),
            ];
        }
    }
}
