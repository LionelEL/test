<?php

namespace common\components\contracts\builders;


/**
 *
 */
class LicenseBuilder
{

    /**
     * @var array Записи из базы данных
     */
    protected $records;

    /**
     * Формирум нужный массив для вывода.
     *
     * @return array
     */
    public function build() : array
    {
        foreach ($this->records as &$record) {
            $record = $this->convertArray($record);
        }

        return $this->records;
    }

    /**
     * Преобразовываем формируем массив для вывода одной строки
     *
     * @param array $array Массив полученый из бд
     * @return array сформированый массив
     */
    public function buildOne($array)
    {
        return $this->convertArray($array);
    }

    /**
     * Задаем записи из бд для формирования
     *
     * @param array $records Записи
     * @return
     */
    public function setRecords(array $records) : Builder
    {
        $this->records = $records;
        return $this;
    }

    /**
     * Формируем массив
     *
     * @param array $record Одна запись из бд
     * @return array Сформированный массив
     */
    public function convertArray(array $record): array
    {
        $creator = $record['creator'];
        $wagons = $record['wagons'];

        $record['wagons'] = [];


        foreach ($wagons as $key => $wagon) {
            $startDate = isset($wagon['date_start']) ? (new \DateTime)->setTimestamp($wagon['date_start']) : null;
            $finishDate = isset($wagon['date_finish']) ? (new \DateTime)->setTimestamp($wagon['date_finish']) : null;
            $wagon['date_start'] = isset($startDate) ? $startDate->format('Y-m-d\TH:i:s.000\Z') : null;
            $wagon['date_finish'] = isset($finishDate) ? $finishDate->format('Y-m-d\TH:i:s.000\Z') : null;

            $record['wagons'][] = [
                'id' => $wagon['wagon_id'],
                'number' => $wagon['wagon']['number'],
                'date_start' => $wagon['date_start'],
                'date_finish' => $wagon['date_finish'],
                'price' => $wagon['price']
            ];
        }

        $record['creator'] = $creator['fio'] ?? '-';

        $startDate = isset($record['date_start']) ? (new \DateTime)->setTimestamp($record['date_start']) : null;
        $record['date_start'] = isset($startDate) ? $startDate->format('Y-m-d\TH:i:s.000\Z') : null;

        $record['license'] = json_decode($record['license']);

        return $record;
    }
}
