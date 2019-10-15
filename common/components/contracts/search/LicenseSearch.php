<?php

namespace common\components\contracts\search;

use common\models\legal\ContractLicense;
use common\components\base_components\SearchComponent;


/**
 *
 */
class LicenseSearch extends SearchComponent
{
    /**
     * @var string Поле по которому сортируем
     */
    protected $sorting = 'number';

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function search()
    {
        $models = ContractLicense::find()->with([
            'creator',
            'wagons.wagon',
        ]);

        return $this->getRecords($models);
    }

    public function findOne($id)
    {
        return ContractLicense::find()->where(['id' => $id])->with([
            'creator',
            'wagons.wagon',
        ])->asArray()->one();
    }

    /**
     * Возвращаем поля по которым сортировать выборку из бд исходя и запроса
     *
     * @return string
     */
    protected function getSortingField($sortField)
    {
        switch ($sortField) {
            default:
                return $sortField;
        }
    }
}
