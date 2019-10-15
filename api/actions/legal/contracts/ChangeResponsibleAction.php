<?php

namespace api\actions\legal\contracts;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use common\models\legal\ContractStatus;
use common\models\legal\ContractStatusHistory;
use common\models\legal\ContractResponsibleHistory;


/**
 * Смена ответственного договора
 * Class ChangeResponsibleAction
 * @package api\actions\legal\contracts
 */
class ChangeResponsibleAction extends Action
{
    /**
     *
     * @return array
     * @throws \Throwable
     */
    public function run()
    {
        $request = Yii::$app->getRequest()->getBodyParams();

        $contracts = $this->controller->modelClass::find()->where(['id' => $request['params']['checkedContractIds']])->all();
        $responsibleId = $request['params']['responsibleId'];

        $transaction = \Yii::$app->db->beginTransaction();

        try {
            foreach ($contracts as $key => $contract) {
                if ($responsibleId) {
                    $contract->responsible_id = $responsibleId;
                    if (isset($contract->getDirtyAttributes()['responsible_id'])) {
                        if ($contract->save()) {
                            $contractResponsibleHistory = new ContractResponsibleHistory();
                            $contractResponsibleHistory->contract_id = $contract->id;
                            $contractResponsibleHistory->employee_id = $contract->responsible_id;
                            $contractResponsibleHistory->date = time();
                            $contractResponsibleHistory->save();

                            $contractStatusHistory = new ContractStatusHistory();
                            $contractStatusHistory->contract_id = $contract->id;
                            $contractStatusHistory->status_id = ContractStatus::RESPONSIBLE_CHANGED;
                            $contractStatusHistory->employee_id = Yii::$app->getUser()->identity->employee->id;
                            $contractStatusHistory->date = time();
                            $contractStatusHistory->save();
                        } else {
                            throw new \Exception('Невозможно изменить ответственного, не все поля договора заполнены', 400);
                        }
                    }
                }
            }

            $transaction->commit();
            return ['message' => 'Ответственный успешно изменен!'];

        } catch (\Throwable $e) {
            $transaction->rollback();
            switch ($e->getCode()) {
                default:
                    Yii::$app->response->statusCode = 400;
                    return ['message' => 'Непредвиденная ошибка: ' . $e->getMessage()];
            }
        }
    }
}
