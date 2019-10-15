<?php

namespace api\actions\legal\contracts;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use common\models\legal\ContractStatus;
use common\models\legal\ContractStatusHistory;


/**
 * Архивирование
 * Class ArchiveAction
 * @package api\actions\legal\contracts
 */
class ArchiveAction extends Action
{
    /**
     * Архивирует документ
     * @param $id
     * @return array
     * @throws \Throwable
     */
    public function run($id)
    {

        $model = $this->controller->modelClass::find()->where(['id' => $id])->one();
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $model->archive = !$model->archive;
            $model->save();

            $contractStatusHistory = new ContractStatusHistory();
            $contractStatusHistory->contract_id = $model->id;
            $contractStatusHistory->status_id = $model->archive ? ContractStatus::ARCHIVED : ContractStatus::RETURN_ARCHIVED;
            $contractStatusHistory->employee_id = Yii::$app->getUser()->identity->employee->id;
            $contractStatusHistory->date = time();
            $contractStatusHistory->save();

            $transaction->commit();
            return ['message' => $model->archive ? 'Договор отправлен в архив' : 'Договор восстановлен из архива'];

        } catch (\Throwable $e) {
            $transaction->rollback();
            switch ($e->getCode()) {
                case 58664:
                    Yii::$app->response->statusCode = 422;
                    if ($wagonObject->getDetailsErrors()) {
                        return ['errors' => $wagonObject->getDetailsErrors()];
                    }
                    return [
                        'errors' => [
                            'message' => $e->getMessage()
                        ]
                    ];
                default:
                    Yii::$app->response->statusCode = 400;
                    return ['message' => 'Непредвиденная ошибка: ' . $e->getMessage()];
            }
        }
    }
}
