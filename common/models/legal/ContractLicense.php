<?php

namespace common\models\legal;

use Yii;
use common\models\legal\NewContract;
use common\models\hr\Employees;
use common\models\legal\ContractLicenseWagons;

/**
 * This is the model class for table "legal.contract_licenses".
 *
 * @property int $id
 * @property int $contract_id Id договора
 * @property array $license Файлы доп. соглашения
 * @property string $name Наименование
 * @property int $date_start Дата подписания
 * @property int $creator_id Создатель документа
 */
class ContractLicense extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_licenses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_start'], 'filter', 'filter' => function ($value)
            {
                if (empty($this->errors) && empty($value)) {
                    return null;
                }

                if (empty($this->errors) && !is_int($value)) {
                    return (new \DateTime($value))->getTimestamp();
                }

                return $value;
            }],
            [['contract_id', 'date_start', 'creator_id', 'name'], 'required'],
            [['contract_id'], 'default', 'value' => null],
            [['contract_id', 'date_start', 'creator_id'], 'integer'],
            [['name'], 'string'],
            [['license'], 'safe'],
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
            'license' => 'License',
            'name' => 'Наименование',
            'date_start' => 'Дата подписания',
            'creator_id' => 'Создатель документа',
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
    public function getCreator()
    {
        return $this->hasOne(Employees::className(), ['id' => 'creator_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWagons()
    {
        return $this->hasMany(ContractLicenseWagon::className(), ['license_id' => 'id']);
    }
}
