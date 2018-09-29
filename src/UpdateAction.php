<?php

namespace nadzif\actions;

use nadzif\actions\base\BaseForm;
use yii\db\ActiveRecord;

class UpdateAction extends \yii\base\Action
{

    public $title;
    public $breadcrumbs;
    public $recordIdentifier = 'name';

    public $view = '@nadzif/actions/layouts/_form';

    public $form;
    public $scenario = BaseForm::SCENARIO_UPDATE;

    public $activeRecordClass;
    public $key = 'id';
    public $condition;

    public $flashKeySuccess = 'success';
    public $flashKeyError   = 'danger';

    public $successMessage;
    public $errorMessage;

    public $redirectUrl;


    public function init()
    {
        if (!isset($this->successMessage)) {
            $this->successMessage = \Yii::t('app', 'Record updated successfully.');
        }

        if (!isset($this->errorMessage)) {
            $this->errorMessage = \Yii::t('app', 'Failed while updating record.');
        }

        parent::init();
    }

    public function run($id)
    {
        /** @var BaseForm $form */
        $form = $this->form;
        /** @var ActiveRecord $model */
        $activeRecord = new $this->activeRecordClass;
        $model        = $activeRecord::find()->where([$this->key => $id]);

        if ($this->condition) {
            $model->andWhere($this->condition);
        }

        $model = $model->one();

        $form->model = $model;
        $form->setScenario($this->scenario);
        $form->loadAttributes();

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post())) {
            if ($form->save()) {
                \Yii::$app->session->setFlash('success', $this->successMessage);
                return $this->controller->redirect($this->redirectUrl);
            } else {
                \Yii::$app->session->setFlash('danger', $this->errorMessage);
            }
        }

        $view                          = $this->controller->getView();
        $view->title                   = $this->title;
        $view->params['breadcrumbs']   = $this->breadcrumbs;
        $view->params['breadcrumbs'][] = $form->model->getAttribute($this->recordIdentifier);

        return $this->controller->render($this->view, ['model' => $form]);
    }
}