<?php

namespace api\actions\legal\contracts;

use Yii;
use yii\base\Action;
use yii\web\Response;
use common\components\contracts\ContractComponent;

/**
 * Загрузка документа
 * @package api\actions\legal\contracts
 */
class LoadFileAction extends Action
{
    /** Куда загружаем файлы */
    const UPLOAD_DIR = "@frontend/web/files/contracts";

    /**
     *
     *
     * @param integer $id Идентификатор договора
     * @return \yii\web\Request
     */
    public function run()
    {
        try {
            return $this->loadFile($_FILES["file"]);
        } catch (Exception $e) {
            return ["message" => $e->getMessage()];
        }
    }

    /**
     * Загрузка файла
     *
     * @return void
     */
    protected function loadFile($file = null)
    {
        if (!$file) {
            throw new Exception("Файл не найден", 1);
        }

        $folder = Yii::$app->request->post("folder");

        $fullDir = \Yii::getAlias(self::UPLOAD_DIR . "/" . $folder);
        $dir = self::UPLOAD_DIR . "/" . $folder;
        $fileName = uniqid() . "_" . $file['name'];
        $path = $fullDir . "/" . $fileName;


        if (move_uploaded_file($file['tmp_name'], $path)) {
            return [
                "name" => $file["name"] . " (загружен - " . (new \DateTime())->format("d.m.Y") . ")",
                "path" => $dir . "/" . $fileName,
                "public_path" => "/files/contracts/" . $folder . "/" . $fileName,
            ];
        }

        throw new \Exception("Файл не загружен", 1);

    }
}
