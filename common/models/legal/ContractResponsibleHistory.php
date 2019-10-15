<?php

namespace common\models\legal;

use Yii;
use common\models\hr\Employees;

/**
 * This is the model class for table "legal.contracts_responsible_history".
 *
 * @property int $id
 * @property int $contract_id Договор
 * @property int $employee_id Ответственный
 * @property int $date
 */
class ContractResponsibleHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_responsible_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contract_id', 'employee_id', 'date'], 'default', 'value' => null],
            [['contract_id', 'employee_id', 'date'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contract_id' => 'Contract ID',
            'employee_id' => 'Employee ID',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(NewContract::className(), ['id' => 'contract_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employees::className(), ['id' => 'employee_id']);
    }
}
