<?php

namespace nadzif\actions\ajax;

use yii\base\Action;
use yii\db\ActiveRecord;
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

    /**
     * @return string
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function run()
    {
        $requestParam      = \Yii::$app->request->get($this->key);

        /** @var ActiveRecord $activeRecordClass */
        $activeRecordClass = new $this->activeRecordClass;

        /** @var ActiveRecord $model */
        $model = $activeRecordClass::findOne($requestParam);

        if ($this->condition && $model->delete()) {
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