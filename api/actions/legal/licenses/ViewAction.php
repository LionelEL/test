<?php

namespace api\actions\legal\licenses;

use Yii;
use yii\base\Action;
use yii\web\Response;
use common\components\contracts\ContractLicenseComponent;

/**
 * Метод просмотра доп. соглашения
 * @package api\actions\legal\licenses
 */
class ViewAction extends Action
{
    protected $component;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->component = new ContractLicenseComponent;
    }

    /**
     * @param array $id
     * @return array
     */
    public function run($id)
    {
        return $this->component->buildOne($id);
    }
}
