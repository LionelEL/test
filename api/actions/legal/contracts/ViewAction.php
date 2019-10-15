<?php

namespace api\actions\legal\contracts;

use Yii;
use yii\base\Action;
use yii\web\Response;
use common\components\contracts\ContractComponent;

/**
 * View метод для получения договора
 * @package api\actions\legal\contracts
 */
class ViewAction extends Action
{
    protected $component;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->component = new ContractComponent;
    }

    /**
     * @param array $filters
     * @return array
     */
    public function run($id)
    {
        return $this->component->buildOne($id);
    }
}
