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
 * Экшн для обновления доп. соглашения
 * @package api\actions\legal\licenses
 */
class UpdateAction extends Action
{
    /**
     * Формирование массива перевозок с учетом фильтров
     *
     *
     * @param array $filters
     * @return array
     */
    public function run($id)
    {
        $model = $this->controller->modelClass::findOne($id);
        $request = Yii::$app->getRequest()->getBodyParams();

        $transaction = $model::getDb()->beginTransaction();
        try {
            $model->load($request, 'license');

            if (!$model->save()) {
                throw new \Exception("Ошибка редактирования доп. соглашения", 1);
            }

            ContractLicenseWagon::deleteAll("license_id = $model->id");
            foreach ($request['license']['wagons'] as $key => $wagon) {
                $licenseWagon = new ContractLicenseWagon();
                $licenseWagon->license_id = $model->id;
                $licenseWagon->wagon_id = $wagon['id'];
                $licenseWagon->date_start = $wagon['date_start'];
                $licenseWagon->date_finish = $wagon['date_finish'];
                $licenseWagon->price = $wagon['price'];
                $licenseWagon->save();
            }

            $transaction->commit();
            Yii::$app->response->statusCode = 201;
            return ['message' => 'Доп. соглашение ' . $model->name . ' успешно отредактировано!'];
        } catch (\Throwable $e) {
            $transaction->rollback();
            switch ($e->getCode()) {
                case 1:
                    Yii::$app->response->statusCode = 422;
                    return $model;

                default:
                    Yii::$app->response->statusCode = 400;
                    return ['message' => 'Непредвиденная ошибка: ' . $e->getTraceAsString()];
            }
        }
    }
}
