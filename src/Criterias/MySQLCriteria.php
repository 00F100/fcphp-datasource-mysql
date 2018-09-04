<?php

namespace FcPhp\Datasource\MySQL\Criterias
{
    use FcPhp\Datasource\Criteria;
    use FcPhp\Datasource\Interfaces\ICriteria;
    use FcPhp\Datasource\Interfaces\IFactory;
    use FcPhp\Datasource\MySQL\Interfaces\IMySQLCriteria;

    class MySQLCriteria extends Criteria implements ICriteria, IMySQLCriteria
    {
        public function or(object $callback)
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->content[] = 'OR';
            $this->content[] = $criteria->getWhere();
            unset($criteria);
            unset($callback);
        }

        public function and(object $callback)
        {
            $criteria = $this->getCriteria();
            $callback($criteria);
            $this->content[] = 'AND';
            $this->content[] = $criteria->getWhere();
            unset($criteria);
            unset($callback);
        }

        public function condition(string $field, string $condition, $value)
        {
            if(is_string($value)) {
                $value = '"' . $value . '"';
            }
            // $this->content[] = $this->operator;
            $this->content[] = $field . ' ' . $condition . ' ' . $value;
        }

        public function getWhere()
        {
            return $this->content;
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
