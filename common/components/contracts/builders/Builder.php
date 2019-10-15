<?php

namespace common\components\contracts\builders;

use common\components\contracts\ContractHelper;
use common\helpers\StringHelper;

/**
 *
 */
class Builder
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
        // Выносим связи в переменные. Потом удалим их
        $type = $record['type'];
        $subjects = $record['subjects'];
        $contractor = $record['contractor'];
        $prolongation = $record['prolongation'];
        $responsible = $record['responsible'];
        $creator = $record['creator'];
        $wagons = $record['wagons'];
        $addLicenses = $record['addLicenses'];

        // Удаляем ненужные поля
        unset(
            $record['creator'],
            $record['addLicenses']
        );

        $record['wagons'] = [];
        foreach ($wagons as $key => $wagon) {
            $startDate = isset($wagon['date_start']) ? (new \DateTime)->setTimestamp($wagon['date_start']) : null;
            $finishDate = isset($wagon['date_finish']) ? (new \DateTime)->setTimestamp($wagon['date_finish']) : null;
            $wagon['date_start'] = isset($startDate) ? $startDate->format('Y-m-d\TH:i:s.000\Z') : null;
            $wagon['date_finish'] = isset($finishDate) ? $finishDate->format('Y-m-d\TH:i:s.000\Z') : null;

            $record['wagons'][] = [
                'id' => $wagon['wagons_id'],
                'number' => $wagon['wagon']['number'],
                'date_start' => $wagon['date_start'],
                'date_finish' => $wagon['date_finish'],
                'price' => $wagon['price']
            ];
        }

        $record['addLicenses'] = [];
        foreach ($addLicenses as $key => $license) {
            $startDate = isset($license['date_start']) ? (new \DateTime)->setTimestamp($license['date_start']) : null;
            $license['date_start'] = isset($startDate) ? $startDate->format('Y-m-d\TH:i:s.000\Z') : null;
            $wagon['date_finish'] = isset($finishDate) ? $finishDate->format('Y-m-d\TH:i:s.000\Z') : null;

            $record['addLicenses'][] = [
                'id' => $license['id'],
                'name' => $license['name'],
                'date_start' => $license['date_start'],
                'creator' => $license['creator']['fio'],
                'wagons' => $license['wagons'],
                'license' => json_decode($license['license'])
            ];
        }

        $subjectsIds = [];
        $subjectsLabels = [];
        foreach ($record['subjects'] as $key => $sub) {
            $subjectsIds[] = $sub['id'];
            $subjectsLabels[] = $sub['name'];
        }

        $record['subjects'] = $subjectsIds;
        $record['subjects_labels'] = $subjectsLabels ? implode(', ', $subjectsLabels) : '-';

        $record['type'] = $type['name'] ?? '-';
        $record['type_id_name'] = $type['type'] ?? '-';
        $record['contractor_name'] = $contractor['name'] ?? '-';
        $record['prolongation_name'] = $prolongation['name'] ?? '-';
        $record['responsible_name'] = $responsible['fio'] ?? '-';
        $record['creator_name'] = $creator['fio'] ?? '-';
        $record['statement_term_text'] = $record['statement_term'] && !$record['permanent'] ? StringHelper::num2word($record['statement_term'], ['день', 'дня', 'дней']) : '-';
        $record['prolongation_term_text'] = $record['prolongation_term'] && !$record['permanent'] ? StringHelper::num2word($record['prolongation_term'], ['день', 'дня', 'дней']) : '-';

        $startDate = isset($record['date_start']) ? (new \DateTime)->setTimestamp($record['date_start']) : null;
        $finishDate = isset($record['date_finish']) ? (new \DateTime)->setTimestamp($record['date_finish']) : null;
        $record['date_start'] = isset($startDate) ? $startDate->format('Y-m-d\TH:i:s.000\Z') : null;
        $record['date_finish'] = isset($finishDate) ? $finishDate->format('Y-m-d\TH:i:s.000\Z') : null;
        $record['date_finish_left'] = isset($finishDate) ? StringHelper::num2word((new \DateTime())->diff($finishDate)->format("%r%a"), ['день', 'дня', 'дней']) : null;
        $record['isExpired'] = $record['date_finish_left'] < $record['prolongation_term'];


        $record['contracts'] = json_decode($record['contracts']);
        $record['licenses'] = json_decode($record['licenses']);
        $record['applications'] = json_decode($record['applications']);
        $record['specifications'] = json_decode($record['specifications']);
        $record['delivery_acts'] = json_decode($record['delivery_acts']);
        $record['others'] = json_decode($record['others']);
        $record['annul_doc'] = json_decode($record['annul_doc']);

        return $record;
    }
}
