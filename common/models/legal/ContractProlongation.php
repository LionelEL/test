<?php

namespace common\models\legal;

use Yii;
use common\models\legal\NewContract;

/**
 * This is the model class for table "legal.contract_prolongations".
 *
 * @property int $id
 * @property string $name Тип пролонгации
 */
class ContractProlongation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_prolongations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'default', 'value' => null],
            [['name'], 'string'],
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
