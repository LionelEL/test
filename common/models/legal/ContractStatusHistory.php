<?php

namespace common\models\legal;

use Yii;
use common\models\legal\NewContract;
use common\models\legal\ContractStatus;
use common\models\hr\Employees;

/**
 * This is the model class for table "legal.contract_status_history".
 *
 * @property int $id
 * @property int $contract_id Id договора
 * @property int $status_id Id предмета договора
 * @property int $employee_id Ответственный
 * @property int $date Дата
 */
class ContractStatusHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_status_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contract_id', 'status_id', 'employee_id', 'date'], 'default', 'value' => null],
            [['contract_id', 'status_id', 'employee_id', 'date'], 'integer'],
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
            'status_id' => 'Status ID',
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
    public function getStatus()
    {
        return $this->hasOne(ContractStatus::className(), ['id' => 'status_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employees::className(), ['id' => 'employee_id']);
    }
}
