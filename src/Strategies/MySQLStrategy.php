<?php

namespace FcPhp\Datasource\MySQL\Strategies
{
    use FcPhp\Datasource\Strategy;
    use FcPhp\Datasource\Interfaces\IStrategy;

    class MySQL extends Strategy implements IStrategy
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

        public function join(string $joinType, string $field, string $condition, string $value)
        {
            $this->join[] = compact('joinType', 'field', 'condition', 'value');
            return $this;
        }

        public function where(object $callback)
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->where = array_merge($this->where, $criteria->getWhere());
            return $this;
        }
    }
}

// $this->where(function(ICriteria $criteria) {
//     $criteria->or(function(ICriteria $criteria) {
//         $criteria->condition('field', 'condition', 'value');
//         $criteria->condition('field2', 'condition2', 'value2');
//         $criteria->and(function(ICriteria $criteria) {
//             $criteria->condition('field3', 'condition3', 'value3');
//             $criteria->condition('field4', 'condition4', 'value4');
//         });
//     });
//     $criteria->and(function(ICriteria $criteria) {
//         $criteria->condition('field5', 'condition5', 'value5');
//         $criteria->condition('field6', 'condition6', 'value6');
//     });
// });

// [
//     'OR',
//     [
//         'AND',
//         [
//             'o.item like "test%"',
//             'c.active=1',
//         ],
//         'OR',
//         [
//             'o.item like "test%"',
//             'c.active=1',
//         ],
//         'o.item like "test%"',
//     ],
//     'AND',
//     [
//         'OR',
//         [
//             '',
//             '',
//         ],
//         'AND',
//         [
//             'OR',
//             [
//                 '',
//                 ''
//             ],
//         ]
//     ]
// ]
