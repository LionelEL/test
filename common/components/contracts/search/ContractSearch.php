<?php

namespace common\components\contracts\search;

use common\models\legal\NewContract;
use common\components\base_components\SearchComponent;


/**
 *
 */
class ContractSearch extends SearchComponent
{
    /**
     * @var string Поле по которому сортируем
     */
    protected $sorting = 'id';

    /**
     * @var string Тип сортировки
     */
    protected $sorting_type = SORT_DESC;

    protected $sortArray = [];

    /**
     * @inheritdoc
     *
     * @return array
     */
    public function search()
    {
        $models = NewContract::find()->with([
            'type',
            'contractor',
            'prolongation',
            'responsible',
            'creator',
            'subjects',
            'wagons.wagon',
            'addLicenses.creator',
            'addLicenses.wagons.wagon' => function ($query) {
                $query->select('id, number');
            }
        ])->joinWith([
            'contractor as cmp',
            'prolongation as prl',
            'subjects as sbj',
            'responsible as rsp',
        ]);

        if (isset($this->params['type_id']) && !empty($this->params['type_id'])) {
            $models->andWhere(['type_id' => $this->params['type_id']]);
        }

        if (isset($this->params['archive'])) {
            if ($this->params['archive'] === 'true') {
                $models->andWhere(['archive' => true]);
            } else {
                $models->andWhere(['archive' => false]);
            }
        }

        if (isset($this->params['expired'])) {
            if ($this->params['expired'] === 'true') {
                $models->andWhere(['<', 'date_finish', time()]);
            }
        }

        if (isset($this->params['number']) && !empty($this->params['number'])) {
            $models->andWhere(['ilike', 'number', $this->params['number']]);
        }

        if (isset($this->params['contractor']) && !empty($this->params['contractor'])) {
            $models->joinWith(['contractor']);
            $models->andWhere(['ilike', 'cmp.name', $this->params['contractor']]);
        }

        if (isset($this->params['contractor_id']) && !empty($this->params['contractor_id'])) {
            $models->andWhere(['contractor_id' => $this->params['contractor_id']]);
        }

        if (isset($this->params['subject']) && !empty($this->params['subject'])) {
            $models->joinWith(['subjects']);
            $models->andWhere(['ilike', 'sbj.name', $this->params['subject']]);
        }

        if (isset($this->params['date_start']) && !empty($this->params['date_start'])) {
            $models->andWhere(['between', 'date_start', $this->params['date_start'][0], $this->params['date_start'][1]]);
        }

        if (isset($this->params['date_finish']) && !empty($this->params['date_finish'])) {
            $models->andWhere(['between', 'date_finish', $this->params['date_finish'][0], $this->params['date_finish'][1]]);
        }

        if (isset($this->params['prolongation']) && !empty($this->params['prolongation'])) {
            $models->joinWith(['prolongation']);
            $models->andWhere(['ilike', 'prl.name', $this->params['prolongation']]);
        }

        if (isset($this->params['responsible']) && !empty($this->params['responsible'])) {
            $models->joinWith(['responsible']);
            $models->andWhere(['ilike', 'rsp.fio', $this->params['responsible']]);
        }

        if (isset($this->params['sortFields']) && !empty($this->params['sortFields'])) {
            $this->sortArray = [];

            foreach ($this->params['sortFields'] as $key => $field) {
                $this->sortArray[$this->getSortingField($key)] = $field == 'true' ? SORT_DESC : SORT_ASC;
            }
        } else {
            $this->sortArray[$this->getSortingField($this->sorting)] = $this->sorting_type;
        }


        return $this->getRecords($models);
    }

    public function findOne($id)
    {
        return NewContract::find()->where(['id' => $id])->with([
            'type',
            'contractor',
            'prolongation',
            'responsible',
            'creator',
            'subjects',
            'wagons.wagon',
            'statusHistory.status',
            'statusHistory.employee',
            'responsibleHistory.employee',
            'addLicenses.creator',
            'addLicenses.wagons.wagon' => function ($query) {
                $query->select('id, number');
            }
        ])->asArray()->one();
    }

    /**
     * Формируем массив с данными с учетом параметров (limit, offset и т.д.)
     *
     * @param $models Объекты AR
     * @return array
     */
    protected function getRecords($models) : array
    {
        if (isset($this->params['limit']) && !empty($this->params['limit'])) {
            $this->limit = $this->params['limit'];
        }

        if (isset($this->params['page'])) {
            $this->offset = $this->getOffset((int) $this->params['page']);
        }

        $expiredAmount = (clone $models)->andWhere(['<', 'date_finish', time()])->distinct()->count();

        $records = $models->orderBy($this->sortArray)
                ->offset($this->offset)->limit($this->limit)->asArray()->all();

        return [
                'records' => $records,
                'totalRecords' => $models->count(),
                'expiredAmount' => $expiredAmount
            ];
    }

    /**
     * Возвращаем поля по которым сортировать выборку из бд исходя и запроса
     *
     * @return string
     */
    protected function getSortingField($sortField)
    {
        switch ($sortField) {
            case 'number':
                return 'number';
            case 'contractor':
                return 'cmp.name';
            case 'subject':
                return 'sbj.name';
            case 'prolongation':
                return 'prl.name';
            case 'responsible':
                return 'rsp.fio';

            default:
                return $sortField;
        }
    }
}
