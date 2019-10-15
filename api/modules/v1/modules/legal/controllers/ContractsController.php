<?php

namespace api\modules\v1\modules\legal\controllers;


use yii\filters\AccessControl;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;

class ContractsController extends ActiveController
{
    public $modelClass = 'common\models\legal\NewContract';

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

        $actions['index'] = [
            'class' => 'api\actions\legal\contracts\IndexAction',
        ];

        $actions['view'] = [
            'class' => 'api\actions\legal\contracts\ViewAction',
        ];

        $actions['create'] = [
            'class' => 'api\actions\legal\contracts\CreateAction',
        ];

        $actions['update'] = [
            'class' => 'api\actions\legal\contracts\UpdateAction',
        ];

        $actions['toggle-archive'] = [
            'class' => 'api\actions\legal\contracts\ArchiveAction',
        ];

        $actions['prolongate'] = [
            'class' => 'api\actions\legal\contracts\ProlongateAction',
        ];

        $actions['annul'] = [
            'class' => 'api\actions\legal\contracts\AnnulAction',
        ];

        $actions['change-responsible'] = [
            'class' => 'api\actions\legal\contracts\ChangeResponsibleAction',
        ];

        $actions['load-contract-file'] = [
            'class' => 'api\actions\legal\contracts\LoadFileAction',
        ];

        return $actions;
    }
}
