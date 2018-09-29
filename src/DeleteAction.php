<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 6/25/2018
 * Time: 1:20 PM
 */

namespace nadzif\actions;

use yii\base\Action;
use yii\db\ActiveRecord;

class DeleteAction extends Action
{
    public $isSoftDelete = false;

    public $softDeleteAttribute = 'status';

    public $softDeleteValue = 'deleted';

    public $activeRecordClass;
    public $key = 'id';

    public $flashKeySuccess = 'success';
    public $flashKeyError   = 'danger';

    public $successMessage;
    public $errorMessage;

    public $redirectUrl;

    public function init()
    {
        if (!isset($this->successMessage)) {
            $this->successMessage = \Yii::t('app', 'Record deleted successfully.');
        }

        if (!isset($this->errorMessage)) {
            $this->errorMessage = \Yii::t('app', 'Failed while deleting record.');
        }

        parent::init();
    }

    public function run($id)
    {
        /** @var ActiveRecord $newActiveRecord */
        $newActiveRecord = new $this->activeRecordClass;
        $activeRecord    = $newActiveRecord::find()->where([$this->key => $id])->one();

        if ($this->isSoftDelete) {
            $attr = $this->softDeleteAttribute;

            $activeRecord->{$attr} = $this->softDeleteValue;
            $success               = $activeRecord->save();
        } else {
            $success = $activeRecord->delete();
        }

        if ($success) {
            \Yii::$app->session->setFlash($this->flashKeySuccess, $this->successMessage);
        } else {
            \Yii::$app->session->setFlash($this->flashKeyError, $this->errorMessage);
        }

        return $this->controller->redirect($this->redirectUrl);
    }
}