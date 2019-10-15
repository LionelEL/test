<?php

namespace common\components\contracts;

use common\components\contracts\builders\Builder;
use common\components\contracts\search\ContractSearch;


/**
 * Класс отвечает за все операции с договорами
 *
 * @author Alex
 * @since 0.1
 */
class ContractComponent
{
    /**
     * @var Класс отвечающий за поиск
     */
    protected $searchClass;

    /**
     * @var Класс отвечающий за построение массива нужного вида
     */
    protected $builder;

    /**
     * Создаем экземпляры классов, необходимые для работы компонента
     */
    public function __construct()
    {
        $this->searchClass = new ContractSearch;
        $this->builder = new Builder;
    }

    /**
     * Метод формирует массив с данными, с учетом фильтров
     *
     * @param array $params Параметры поиска
     * @return array Массив с результатами поиска
     */
    public function build(array $params = []) : array
    {
        $searchResults = $this->searchClass->setParams($params)->search();
        $searchResults['records'] = $this->builder->setRecords($searchResults['records'])->build();

        return $searchResults;
    }

    /**
     * Получить договор по идентификатору
     *
     * @param integer $id
     * @return array
     */
    public function buildOne($id)
    {
        return $this->builder->buildOne($this->searchClass->findOne($id));
    }

}
