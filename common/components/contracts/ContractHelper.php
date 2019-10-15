<?php

namespace common\components\contracts;

/**
 *
 */
class ContractHelper
{
    /**
     * Типы предметов договоров
     *
     * @return array
     */
    public static function getRepairReasons($id = null)
    {
        $contractReasons = [
            [
                'id' => 1,
                'type' => 'Текущий ремонт'
            ],
            [
                'id' => 2,
                'type' => 'Деповской ремонт'
            ],
            [
                'id' => 3,
                'type' => 'Капитальный ремонт.'
            ],
            [
                'id' => 4,
                'type' => 'Капитальный ремонт с продлением срока службы.'
            ],
            [
                'id' => 5,
                'type' => 'Текущий, деповской, капитальный.'
            ],
            [
                'id' => 6,
                'type' => 'Текущий, деповской.'
            ],
            [
                'id' => 7,
                'type' => 'Текущий, ремонт деталей.'
            ],
            [
                'id' => 8,
                'type' => 'Текущий, деповской, капитальный, ремонт деталей.'
            ],
            [
                'id' => 9,
                'type' => 'Текущий деповской, ремонт деталей.'
            ],
            [
                'id' => 10,
                'type' => 'Ремонт деталей.'
            ],
            [
                'id' => 11,
                'type' => 'Коммерческий ремонт.'
            ]
        ];

        if ($id === null) {
            return $contractReasons;
        }

        foreach ($contractReasons as $reason) {
            if ($reason['id'] == $id) {
                return $reason;
            }
        }
    }
}
