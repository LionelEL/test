<?php

namespace api\actions\legal\contracts;

use Yii;
use yii\base\Action;
use yii\web\Response;
use common\models\User;
use yii\helpers\Url;
use common\models\legal\ContractWagons;
use common\models\legal\ContractSubjectLink;
use common\models\legal\ContractStatus;
use common\models\legal\ContractStatusHistory;
use common\models\legal\ContractResponsibleHistory;

/**
 * Экшн для обновления договора
 * @package api\actions\legal\contracts
 */
class UpdateAction extends Action
{
    /**
     * @param $id
     * @param array $filters
     * @return array
     */
    public function run($id)
    {
        /* @var $model \yii\db\ActiveRecord */
        $model = $this->controller->modelClass::findOne($id);
        $request = Yii::$app->getRequest()->getBodyParams();

        $transaction = $model::getDb()->beginTransaction();
        try {
            $model->load($request, 'contract');
            $model->creator_id = Yii::$app->getUser()->identity->employee->id;

            if (isset($model->getDirtyAttributes()['responsible_id'])) {
                $contractResponsibleHistory = new ContractResponsibleHistory();
                $contractResponsibleHistory->contract_id = $model->id;
                $contractResponsibleHistory->employee_id = $model->responsible_id;
                $contractResponsibleHistory->date = time();
                $contractResponsibleHistory->save();
            }

            if (!$model->save()) {
                throw new \Exception("Ошибка редактирования договора", 1);
            }

            ContractSubjectLink::deleteAll("contract_id = $model->id");
            foreach ($request['contract']['subjects'] as $key => $sub) {
                $link = new ContractSubjectLink();
                $link->contract_id = $model->id;
                $link->subject_id = $sub;
                $link->save();
            }

            ContractWagons::deleteAll("contracts_id = $model->id");
            foreach ($request['contract']['wagons'] as $key => $wagon) {
                $contractWagon = new ContractWagons();
                $contractWagon->contracts_id = $model->id;
                $contractWagon->wagons_id = $wagon['id'];
                $contractWagon->date_start = $wagon['date_start'];
                $contractWagon->date_finish = $wagon['date_finish'];
                $contractWagon->price = $wagon['price'];
                $contractWagon->save();
            }

            $contractStatusHistory = new ContractStatusHistory();
            $contractStatusHistory->contract_id = $model->id;
            $contractStatusHistory->status_id = ContractStatus::UPDATED;
            $contractStatusHistory->employee_id = Yii::$app->getUser()->identity->employee->id;
            $contractStatusHistory->date = time();
            $contractStatusHistory->save();


            $transaction->commit();
            Yii::$app->response->statusCode = 201;
            return ['message' => 'Договор №' . $model->number . ' успешно отредактирован!'];
        } catch (\Throwable $e) {
            $transaction->rollback();
            switch ($e->getCode()) {
                case 1:
                    Yii::$app->response->statusCode = 422;
                    return ['message' => $e->getMessage(), 'errors' => $model->getErrors()];

                default:
                    Yii::$app->response->statusCode = 400;
                    return ['message' => 'Непредвиденная ошибка: ' . $e->getMessage()];
            }
        }
    }
}
