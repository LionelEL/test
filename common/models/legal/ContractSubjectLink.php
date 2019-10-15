<?php

namespace common\models\legal;

use Yii;
use common\models\legal\NewContract;
use common\models\legal\ContractStatus;

/**
 * This is the model class for table "legal.contract_subject_links".
 *
 * @property int $id
 * @property int $contract_id Id договора
 * @property int $subject_id Id предмета договора
 */
class ContractSubjectLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_subject_links';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contract_id', 'subject_id'], 'default', 'value' => null],
            [['contract_id', 'subject_id'], 'integer'],
            [['contract_id', 'subject_id'], 'required'],
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
            'subject_id' => 'Subject ID',
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
    public function getSubject()
    {
        return $this->hasOne(ContractSubject::className(), ['id' => 'subject_id']);
    }
}
