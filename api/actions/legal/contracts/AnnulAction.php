<?php

namespace api\actions\legal\contracts;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use common\models\legal\ContractStatus;
use common\models\legal\ContractStatusHistory;


/**
 * Расторжение договора
 * Class AnnulAction
 * @package api\actions\legal\contracts
 */
class AnnulAction extends Action
{
    /**
     * @return array
     * @throws \Throwable
     */
    public function run()
    {
        $request = Yii::$app->getRequest()->getBodyParams();
        $model = $this->controller->modelClass::find()->where(['id' => $request['contract']['id']])->one();

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $model->annul_doc = $request['contract']['annul_doc'];
            $model->archive = !$model->archive;
            if ($model->save()) {
                $contractStatusHistory = new ContractStatusHistory();
                $contractStatusHistory->contract_id = $model->id;
                $contractStatusHistory->status_id = ContractStatus::ANNULED;
                $contractStatusHistory->employee_id = Yii::$app->getUser()->identity->employee->id;
                $contractStatusHistory->date = time();
                $contractStatusHistory->save();

                $contractStatusHistory = new ContractStatusHistory();
                $contractStatusHistory->contract_id = $model->id;
                $contractStatusHistory->status_id = ContractStatus::ARCHIVED;
                $contractStatusHistory->employee_id = Yii::$app->getUser()->identity->employee->id;
                $contractStatusHistory->date = time();
                $contractStatusHistory->save();
            } else {
                throw new \Exception('Невозможно расторгнуть договор, не все поля заполнены', 400);
            }

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
