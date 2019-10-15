<?php

namespace api\actions\legal\contracts;

use Yii;
use yii\base\Action;
use yii\web\Response;
use common\components\contracts\ContractComponent;

/**
 * Index метод для получения договоров
 * @package api\actions\legal\contracts
 */
class IndexAction extends Action
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
    public function run(array $filters = [])
    {
        return $this->component->build($filters);
    }
}
