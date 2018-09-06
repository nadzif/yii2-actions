<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 6/25/2018
 * Time: 1:20 PM
 */

namespace nadzif\actions\ajax;


use yii\base\Action;
use yii\helpers\Json;

class DeleteAction extends Action
{
    public $activeRecordClass;
    public $condition = true;

    public $key = 'id';

    public $successAlert;
    public $errorMessage;

    public function init()
    {

        if (!isset($this->successAlert)) {
            $this->successAlert[] = [
                'type'    => 'success',
                'title'   => \Yii::t('app', 'Delete Success'),
                'message' => \Yii::t('app', 'Record has been removed.'),
            ];
        }

        if (!isset($this->errorMessage)) {
            $this->errorMessage = \Yii::t('app', 'Failed while deleting record.');
        }

        parent::init();
    }

    public function run()
    {
        $requestParam = \Yii::$app->request->get($this->key);
        $model        = new $this->activeRecordClass;
        $data         = $model::findOne($requestParam);

        if ($this->condition && $data->delete()) {
            return Json::encode([
                'data' => [
                    'alert' => $this->successAlert,
                ]
            ]);
        } else {
            return Json::encode([
                'data' => [
                    'alert' => [
                        'type'    => 'danger',
                        'title'   => \Yii::t('app', 'Delete Failed'),
                        'message' => $this->errorMessage
                    ]
                ]
            ]);
        }
    }
}