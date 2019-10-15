<?php

namespace api\modules\v1\modules\legal\controllers;


use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;

class LicensesController extends ActiveController
{
    public $modelClass = 'common\models\legal\ContractLicense';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
        ];

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['create', 'update', 'delete', 'options'],
            'rules' => [
                [
                    'actions' => ['create', 'update', 'delete', 'options'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['create'] = [
            'class' => 'api\actions\legal\licenses\CreateAction',
        ];

        $actions['update'] = [
            'class' => 'api\actions\legal\licenses\UpdateAction',
        ];

        $actions['view'] = [
            'class' => 'api\actions\legal\licenses\ViewAction',
        ];

        $actions['load-license-file'] = [
            'class' => 'api\actions\legal\licenses\LoadFileAction',
        ];

        return $actions;
    }
}
