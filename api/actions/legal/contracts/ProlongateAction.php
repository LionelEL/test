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
 * Class ProlongateAction
 * @package api\actions\legal\contracts
 */
class ProlongateAction extends Action
{
    /**
     * Пролонгирует договор
     * @param $id
     * @return array
     * @throws \Throwable
     */
    public function run($id)
    {
        $model = $this->controller->modelClass::find()->where(['id' => $id])->one();
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $model->date_finish = $model->date_finish + 31536000;
            $model->save();

            $contractStatusHistory = new ContractStatusHistory();
            $contractStatusHistory->contract_id = $model->id;
            $contractStatusHistory->status_id = ContractStatus::MANUAL_PROLONG;
            $contractStatusHistory->employee_id = Yii::$app->getUser()->identity->employee->id;
            $contractStatusHistory->date = time();
            $contractStatusHistory->save();

            $transaction->commit();
            return ['message' => 'Срок действия договора продлен.'];

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
