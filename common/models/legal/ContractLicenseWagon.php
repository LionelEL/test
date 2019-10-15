<?php

namespace common\models\legal;

use Yii;
use common\models\legal\ContractLicense;
use common\models\wagons_array\Wagons;

/**
 * This is the model class for table "legal.contract_license_wagons".
 *
 * @property int $id
 * @property int $license_id Id договора
 * @property int $wagon_id Файлы доп. соглашения
 * @property int $date_start Дата начала
 * @property int $date_finish Дата окончания
 * @property string $price Стоимость
 */
class ContractLicenseWagon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.contract_license_wagons';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_start', 'date_finish'], 'filter', 'filter' => function ($value)
            {
                if (empty($this->errors) && empty($value)) {
                    return null;
                }

                if (empty($this->errors) && !is_int($value)) {
                    return (new \DateTime($value))->getTimestamp();
                }

                return $value;
            }],
            [['license_id', 'date_start', 'date_finish'], 'default', 'value' => null],
            [['license_id', 'date_start', 'date_finish', 'wagon_id'], 'integer'],
            [['price'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'license_id' => 'License ID',
            'wagon_id' => 'Wagon ID',
            'date_start' => 'Date Start',
            'date_finish' => 'Date Finish',
            'price' => 'Price',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLicense()
    {
        return $this->hasOne(ContractLicense::className(), ['id' => 'license_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWagon()
    {
        return $this->hasOne(Wagons::className(), ['id' => 'wagon_id']);
    }
}
