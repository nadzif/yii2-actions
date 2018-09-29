<?php

namespace nadzif\actions\ajax;

use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\Json;

class DeleteAction extends Action
{
    public $activeRecordClass;
    public $condition = true;
    public $recordCondition;

    public $key = 'id';

    public $isSoftDelete        = false;
    public $softDeleteAttribute = 'status';
    public $softDeleteValue     = 'deleted';

    public $successMessage;
    public $errorMessage;

    public $flashKeySuccess = 'success';
    public $flashKeyError   = 'danger';

    public function init()
    {

        if (!isset($this->successMessage)) {
            $this->successMessage[] = [
                'type'    => $this->flashKeySuccess,
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
    public function run($id)
    {

        /** @var ActiveRecord $activeRecord */
        $activeRecord = new $this->activeRecordClass;

        $model = $activeRecord::find()->where([$this->key => $id]);
        if ($this->recordCondition) {
            $model->andWhere($this->recordCondition);
        }

        $model = $model->one();

        $success = false;

        if ($this->condition) {
            if ($this->isSoftDelete) {
                $attr = $this->softDeleteAttribute;

                $model->{$attr} = $this->softDeleteValue;
                $success        = $model->save();
            } else {
                $success = $model->delete();
            }
        }


        if ($success) {
            return Json::encode([
                'data' => [
                    'alert' => $this->successMessage,
                ]
            ]);
        } else {
            return Json::encode([
                'data' => [
                    'alert' => [
                        'type'    => $this->flashKeyError,
                        'title'   => \Yii::t('app', 'Delete Failed'),
                        'message' => $this->errorMessage
                    ]
                ]
            ]);
        }

    }
}