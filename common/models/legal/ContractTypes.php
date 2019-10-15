<?php

namespace common\models\legal;

use Yii;
use common\models\legal\ContractSubject;

/**
 * This is the model class for table "legal.contract_types".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property integer $seq
 * @property string $folder
 *
 * @property ContractColumns[] $legalContractColumns
 * @property Contracts[] $legalContracts
 */
class ContractTypes extends \yii\db\ActiveRecord
{

    public $contracts_amount;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'legal.contract_types';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['seq'], 'integer'],
            [['name', 'type', 'folder'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'type' => 'Type',
            'seq' => 'Seq',
            'folder' => 'Folder'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractColumns()
    {
        return $this->hasMany(ContractColumns::className(), ['contract_types_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubjects()
    {
        return $this->hasMany(ContractSubject::className(), ['contract_type_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(NewContract::className(), ['type_id' => 'id']);
    }

    public function getAll()
    {
        return static::find()->all();
    }
}
