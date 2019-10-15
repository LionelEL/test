<?php

namespace common\models\legal;

use Yii;
use common\models\legal\NewContract;

/**
 * This is the model class for table "legal.contract_statuses".
 *
 * @property int $id
 * @property string $name Наименование
 * @property string $tag_type Тип
 * @property string $seq Последовательность
 */
class ContractStatus extends \yii\db\ActiveRecord
{
    /** Статусы договоров */
    const CREATED = 1;
    const UPDATED = 2;
    const MANUAL_PROLONG = 3;
    const AUTO_PROLONG = 4;
    const ARCHIVED = 5;
    const RETURN_ARCHIVED = 6;
    const ANNULED = 7;
    const RESPONSIBLE_CHANGED = 8;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'tag_type', 'seq'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'tag_type' => 'Tag Type',
            'seq' => 'Seq',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(NewContract::className(), ['prolongation_id' => 'id']);
    }
}
