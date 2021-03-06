<?php

namespace FcPhp\Datasource\MySQL\Factories
{
    use FcPhp\Di\Interfaces\IDi;
    use FcPhp\Datasource\Interfaces\IStrategy;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLFactory;

    class MySQLFactory implements IMySQLFactory
    {
        private $di;
        private $strategy;
        private $listMethods;

        public function __construct(IDi $di, array $listMethods)
        {
            $this->di = $di;
            $this->listMethods = $listMethods;
        }

        public function get(string $method)
        {
            if(isset($this->listMethods[$method])) {
                $methodAlias = $this->listMethods[$method];
                if(!$this->di->has($methodAlias)) {
                    $this->di->setNonSingleton($methodAlias, str_replace('/', '\\', $methodAlias), ['strategy' => $this->strategy]);
                }
                return $this->di->make($methodAlias);
            }
        }

        public function setStrategy(IStrategy $strategy)
        {
            $this->strategy = $strategy;
        }
    }
}
