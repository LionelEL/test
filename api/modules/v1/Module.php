<?php

namespace api\modules\v1;


class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

    public function init()
    {
        parent::init();

        $this->modules = [
            'legal' => [
                'class' => 'api\modules\v1\modules\legal\Legal',
            ],
        ];
    }
}
