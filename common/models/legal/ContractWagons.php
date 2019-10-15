<?php

namespace common\models\legal;

use common\models\guides\Railways;
use common\models\guides\Stations;
use common\models\wagons_array\Wagons;
use Yii;

/**
 * This is the model class for table "legal.wagon_contracts".
 *
 * @property integer $id
 * @property integer $contracts_id
 * @property integer $wagons_id
 * @property string $date_input_wagon
 * @property string $date_output_wagon
 * @property boolean $abdpv
 * @property string $date_abdpv_start
 * @property string $date_abdpv_finish
 * @property boolean $datasheet
 * @property integer $datasheet_type
 * @property boolean $contracts_chain
 * @property boolean $release_telegram
 * @property string $date_release
 * @property integer $release_railway_id
 * @property string $date_transfer
 * @property integer $railway_transfer_wagon_id
 * @property integer $station_transfer_wagon_id
 * @property integer $date_start
 * @property integer $date_finish
 * @property double $price
 *
 * @property Railways $railwayTransferWagonId
 * @property Stations $stationTransferWagonId
 * @property NewContracts $contracts
 * @property Wagons $wagons
 */
class ContractWagons extends \yii\db\ActiveRecord
{
    public $wagon_numbers_string;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'legal.contract_wagons';
    }

    /**
     * @inheritdoc
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
            [['contracts_id', 'wagons_id', 'datasheet_type', 'release_railway_id', 'railway_transfer_wagon_id', 'station_transfer_wagon_id', 'date_start', 'date_finish'], 'integer'],
            [['date_input_wagon', 'date_output_wagon', 'date_abdpv_start', 'date_abdpv_finish',
            'date_release', 'date_transfer'], 'string'],
            [['price'], 'double'],
            // [['railway_transfer_wagon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Railways::className(), 'targetAttribute' => ['railway_transfer_wagon_id' => 'id']],
            // [['station_transfer_wagon_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stations::className(), 'targetAttribute' => ['station_transfer_wagon_id' => 'id']],
            [['abdpv', 'datasheet', 'contracts_chain', 'release_telegram'], 'boolean'],
            [['contracts_id'], 'exist', 'skipOnError' => true, 'targetClass' => NewContract::className(), 'targetAttribute' => ['contracts_id' => 'id']],
            [['wagons_id'], 'exist', 'skipOnError' => true, 'targetClass' => Wagons::className(), 'targetAttribute' => ['wagons_id' => 'id']],
            // ['wagon_numbers_string', 'match', 'pattern' => '/^([0-9]{8}\D?\s?)+$/', 'message' => 'Номера вагонов должны состоять из 8 цифр и должны быть разделены любым символом.'],
            // ['wagon_numbers_string', function ($attribute, $params) {
            //     $wagon_numbers_array = preg_split('/[\D\s]+/', $this->wagon_numbers_string);
            //     $contractWagons = ContractWagons::find()->where(['contracts_id' => $this->contracts_id])->all();
            //     $existingWagons = [];
            //     foreach ($contractWagons as $contractWagon) {
            //         if ((in_array($contractWagon->wagon->number, $wagon_numbers_array)) && $contractWagon->id == !$this->wagons_id) {
            //             $existingWagons[] = $contractWagon->wagon->number;
            //         }
            //     }
            //     if (count($existingWagons) > 0) {
            //         $this->addError($attribute, 'Вагон '.implode(', ',$existingWagons).' уже добавлен в договор');
            //     }
            //     return true;
            // }],
            // ['wagon_numbers_string', function ($attribute, $params) {
            //     $wagon_numbers_array = preg_split('/[\D\s]+/', $this->wagon_numbers_string);
            //     $missingWagons = [];
            //     foreach ($wagon_numbers_array as $number) {
            //         $existWagon = Wagons::find()->where(['number' => $number])->one();
            //         if (!$existWagon) $missingWagons[] = $number;
            //     }
            //     if (count($missingWagons) > 0) {
            //         $this->addError($attribute, 'Вагон '.implode(', ',$missingWagons).' отсутствует в системе');
            //     }
            //     return true;
            // }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contracts_id' => 'Договор',
            'wagons_id' => 'Номер вагона',
            'date_input_wagon' => 'Дата ввода',
            'date_output_wagon' => 'Дата вывода',
            'abdpv' => 'Регистрация АБД ПВ',
            'date_abdpv_start' => 'Дата начала регистрации АБД ПВ',
            'date_abdpv_finish' => 'Дата окончания регистрации АБД ПВ',
            'datasheet' => 'Техпаспорт ВУ-4М',
            'datasheet_type' => 'Тип техпаспорта',
            'contracts_chain' => 'Цепочка договоров',
            'release_telegram' => 'Открепительная телеграмма',
            'date_release' => 'Дата открепления',
            'release_railway_id' => 'ЖД открепления',
            'wagon_numbers_string' => 'Номер или номера вагонов',
            'date_transfer' => 'Дата передачи вагона',
            'railway_transfer_wagon_id' => 'ЖД передачи вагона',
            'station_transfer_wagon_id' => 'Станция передачи вагона',
            'date_start' => 'Дата начала',
            'date_finish' => 'Дата окончания',
            'price' => 'Стоимость'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContract()
    {
        return $this->hasOne(NewContract::className(), ['id' => 'contracts_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWagon()
    {
        return $this->hasOne(Wagons::className(), ['id' => 'wagons_id']);
    }

    public function getReleaseRailway()
    {
        return $this->hasOne(Railways::className(), ['id' => 'release_railway_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRailwayTransferWagon()
    {
        return $this->hasOne(Railways::className(), ['id' => 'railway_transfer_wagon_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStationTransferWagon()
    {
        return $this->hasOne(Stations::className(), ['id' => 'station_transfer_wagon_id']);
    }

    // public function beforeSave($insert)
    // {
    //     parent::beforeSave($insert);
    //     if ($this->wagon_numbers_string) {
    //         $wagon_numbers_array = preg_split('/[\D\s]+/', $this->wagon_numbers_string);
    //         foreach ($wagon_numbers_array as $index => $number) {
    //             $existWagon = Wagons::find()->where(['number' => $number])->one();
    //             if ($index == 0) {
    //                 $this->wagons_id = $existWagon->id;
    //             } else {
    //                 $newContractWagon = new ContractWagons();
    //                 $newContractWagon->attributes = $this->attributes;
    //                 $newContractWagon->wagon_numbers_string = null;
    //                 $newContractWagon->wagons_id = $existWagon->id;
    //                 $newContractWagon->save();
    //             }
    //         }
    //     }
    //     $this->date_input_wagon = $this->date_input_wagon ? (new \DateTime($this->date_input_wagon))->format('Y-m-d') : '';
    //     $this->date_output_wagon = $this->date_output_wagon ? (new \DateTime($this->date_output_wagon))->format('Y-m-d') : '';
    //     $this->date_abdpv_start = $this->date_abdpv_start ? (new \DateTime($this->date_abdpv_start))->format('Y-m-d') : '';
    //     $this->date_abdpv_finish = $this->date_abdpv_finish ? (new \DateTime($this->date_abdpv_finish))->format('Y-m-d') : '';
    //     $this->date_release = $this->date_release ? (new \DateTime($this->date_release))->format('Y-m-d') : '';
    //     $this->date_transfer = $this->date_transfer ? (new \DateTime($this->date_transfer))->format('Y-m-d') : '';
    //     return true;
    // }
}
