<?php

namespace api\actions\legal\licenses;

use Yii;
use yii\base\Action;
use yii\web\Response;
use common\models\User;
use yii\helpers\Url;
use common\models\legal\ContractLicenseWagon;
use common\models\legal\ContractStatus;
use common\models\legal\ContractStatusHistory;

/**
 * Экшн для создания доп.соглашения
 * @package api\actions\legal\licenses
 */
class CreateAction extends Action
{
    /**
     * @param array $filters
     * @return array
     */
     public function run()
     {
         /* @var $model \yii\db\ActiveRecord */
         $model = new $this->controller->modelClass();
         $request = Yii::$app->getRequest()->getBodyParams();

         $transaction = $model::getDb()->beginTransaction();
         try {
             $model->load($request, 'license');
             $model->creator_id = Yii::$app->getUser()->identity->employee->id;

             if (!$model->save()) {
                 throw new \Exception("Ошибка добавления доп. соглашения", 1);
             }

             foreach ($request['license']['wagons'] as $key => $wagon) {
                 $licenseWagon = new ContractLicenseWagon();
                 $licenseWagon->license_id = $model->id;
                 $licenseWagon->wagon_id = $wagon['id'];
                 $licenseWagon->date_start = $wagon['date_start'];
                 $licenseWagon->price = $wagon['price'];
                 $licenseWagon->save();
             }

             $contractStatusHistory = new ContractStatusHistory();
             $contractStatusHistory->contract_id = $model->contract_id;
             $contractStatusHistory->status_id = ContractStatus::UPDATED;
             $contractStatusHistory->employee_id = Yii::$app->getUser()->identity->employee->id;
             $contractStatusHistory->date = time();
             $contractStatusHistory->save();

             $transaction->commit();
             Yii::$app->response->statusCode = 201;
             return ['message' => 'Приложение ' . $model->name . ' успешно создано!'];
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
