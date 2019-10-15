<?php

namespace common\models\legal;

use common\models\hr\Employees;
use Yii;

/**
 * This is the model class for table "legal.responsible_history".
 *
 * @property integer $id
 * @property integer $employees_id
 * @property integer $contracts_id
 * @property integer $created_at
 * @property boolean $status
 * @property integer $date_assign
 * @property integer $date_refuse
 *
 * @property Employees $employees
 * @property Contracts $contracts
 */
class ResponsibleHistory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'legal.responsible_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['employees_id', 'contracts_id', 'created_at'], 'integer'],
            [['employees_id'], 'required'],
            [['date_assign', 'date_refuse'], 'safe'],
            [['status'], 'boolean'],
            [['employees_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employees::className(), 'targetAttribute' => ['employees_id' => 'id']],
            [['contracts_id'], 'exist', 'skipOnError' => true, 'targetClass' => NewContract::className(), 'targetAttribute' => ['contracts_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employees_id' => 'Ответственный',
            'contracts_id' => 'Договор',
            'created_at' => 'Дата создания',
            'status' => 'Статус',
            'date_assign' => 'Дата назначения',
            'date_refuse' => 'Дата снятия'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employees::className(), ['id' => 'employees_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(NewContract::className(), ['id' => 'contracts_id']);
    }

    public function beforeSave($insert)
    {
        parent::beforeSave($insert);
        if ($insert) {
            $this->created_at = time();
        }
        $this->date_assign = $this->date_assign ? (new \DateTime($this->date_assign))->format('Y-m-d') : date('Y-m-d');
        $this->date_refuse = $this->date_refuse ? (new \DateTime($this->date_refuse))->format('Y-m-d') : null;
        $responsibleHistory = ResponsibleHistory::find()->where(['status' => true, 'contracts_id' => $this->contracts_id])->one();
        if (isset($responsibleHistory) && ($responsibleHistory->employees_id !== $this->employees_id)) {
            $responsibleHistory->status = false;
            $responsibleHistory->date_refuse = date('Y-m-d');
            $responsibleHistory->update();
        }
        return true;
    }
}
