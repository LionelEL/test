<?php

namespace common\models\legal;

use Yii;
use common\models\Companies;
use common\models\legal\ContractProlongation;
use common\models\legal\ContractSubject;
use common\models\legal\ContractWagons;
use common\models\legal\ContractStatusHistory;
use common\models\legal\ContractResponsibleHistory;
use common\models\hr\Employees;

/**
 * This is the model class for table "legal.new_contracts".
 *
 * @property int $id
 * @property int $type_id Тип договора
 * @property string $number Номер договора
 * @property int $contractor_id Контрагент
 * @property int $date_start Дата подписания
 * @property int $date_finish Дата окончания
 * @property int $prolongation_id Тип пролонгации
 * @property int $prolongation_term Срок оповещения о пролонгации в днях
 * @property int $responsible_id Ответственный
 * @property int $creator_id Внесение информации
 * @property array $contracts Файлы договора
 * @property array $licenses Файлы доп. соглашения
 * @property array $applications Приложения
 * @property array $specifications Спецификации
 * @property array $delivery_acts Акт приема-передачи
 * @property array $others Прочие файлы
 * @property bool $archive Архивный
 * @property int $date_notify Дата оповещения
 * @property int $statement_term Заявление о пролонгации
 * @property bool $permanent Бессрочный договор
 * @property array $annul_doc Файл расторжения
 * @property array $notificated_at Дата отправки уведомления
 */
class NewContract extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legal.new_contracts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_start', 'date_finish', 'notificated_at'], 'filter', 'filter' => function ($value)
            {
                if (empty($this->errors) && empty($value)) {
                    return null;
                }

                if (empty($this->errors) && !is_int($value)) {
                    return (new \DateTime($value))->getTimestamp();
                }

                return $value;
            }],
            [['date_notify'], 'filter', 'filter' => function ($value)
            {
                if ($this->prolongation_term) {
                    return $this->date_finish - $this->prolongation_term * 86400;
                } else {
                    return $this->date_finish;
                }

            }],
            [['date_finish', 'prolongation_id', 'prolongation_term'], 'required', 'when' => function($model) {
                return !$model->permanent;
            }],
            [['number', 'contractor_id', 'date_start'], 'unique', 'targetAttribute' => ['number', 'contractor_id', 'date_start'], 'skipOnEmpty' => false, 'message' => 'Договор с указанным номером, контрагентом и датой уже добавлен'],
            [['type_id', 'contractor_id', 'date_start', 'responsible_id', 'creator_id'], 'required'],
            [['type_id', 'contractor_id', 'date_start', 'date_finish', 'prolongation_id', 'prolongation_term', 'responsible_id', 'creator_id'], 'default', 'value' => null],
            [['type_id', 'contractor_id', 'date_start', 'date_finish', 'prolongation_id', 'prolongation_term', 'responsible_id', 'creator_id', 'statement_term'], 'integer'],
            [['contracts', 'licenses', 'applications', 'specifications', 'delivery_acts', 'others', 'annul_doc'], 'safe'],
            [['archive', 'permanent'], 'boolean'],
            ['archive', 'default', 'value' => false],
            [['number'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_id' => 'Тип договора',
            'number' => 'Номер договора',
            'contractor_id' => 'Контрагент',
            'date_start' => 'Дата начала',
            'date_finish' => 'Дата окончания',
            'prolongation_id' => 'Тип пролонгации',
            'prolongation_term' => 'Оповещение об окончании',
            'responsible_id' => 'Ответственный',
            'creator_id' => 'Внес информацию',
            'contracts' => 'Документы',
            'licenses' => 'Доп. соглашения',
            'applications' => 'Пиложения',
            'specifications' => 'Спецификации',
            'delivery_acts' => 'Акты приема-передачи',
            'others' => 'Прочие документы',
            'archive' => 'Статус',
            'date_notify' => 'Дата оповещения',
            'statement_term' => 'Заявление о пролонгации',
            'permanent' => 'Бессрочный договор',
            'annul_doc' => 'Документ соглашения о расторжении',
            'notificated_at' => 'Дата отправки оповещения',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(ContractTypes::className(), ['id' => 'type_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractor()
    {
        return $this->hasOne(Companies::className(), ['id' => 'contractor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProlongation()
    {
        return $this->hasOne(ContractProlongation::className(), ['id' => 'prolongation_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible()
    {
        return $this->hasOne(Employees::className(), ['id' => 'responsible_id']);
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
    public function getSubjects()
    {
        return $this->hasMany(ContractSubject::className(), ['id' => 'subject_id'])->viaTable('legal.contract_subject_links', ['contract_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddLicenses()
    {
        return $this->hasMany(ContractLicense::className(), ['contract_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWagons()
    {
        return $this->hasMany(ContractWagons::className(), ['contracts_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatusHistory()
    {
        return $this->hasMany(ContractStatusHistory::className(), ['contract_id' => 'id'])->orderBy(['date' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibleHistory()
    {
        return $this->hasMany(ContractResponsibleHistory::className(), ['contract_id' => 'id'])->orderBy(['date' => SORT_DESC]);
    }
}
