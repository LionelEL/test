<?php

namespace common\models\legal;

use Yii;

/**
 * This is the model class for table "legal.contract_subjects".
 *
 * @property int $id
 * @property string $name Наименование
 * @property int $contract_type_id Тип договора
 * @property int $seq Последовательность
 */
class ContractSubject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_subjects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contract_type_id', 'seq'], 'default', 'value' => null],
            [['contract_type_id', 'seq'], 'integer'],
            [['name'], 'string', 'max' => 255],
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
            'contract_type_id' => 'Contract Type ID',
            'seq' => 'Seq',
        ];
    }
}
