<?php

namespace nadzif\actions;

use nadzif\actions\base\BaseForm;
use yii\db\ActiveRecord;

class CreateAction extends \yii\base\Action
{
    public $title;
    public $breadcrumbs;
    public $recordIdentifier = 'name';

    public $view = '@nadzif/actions/layouts/_form';

    public $form;
    public $scenario = BaseForm::SCENARIO_CREATE;

    public $activeRecordClass;
    public $activeRecordAttributes = [];
    public $canSave                = true;

    public $flashKeySuccess = 'success';
    public $flashKeyError   = 'danger';

    public $successMessage;
    public $errorMessage;

    public $redirectUrl;


    public function init()
    {
        if (!isset($this->successMessage)) {
            $this->successMessage = \Yii::t('app', 'Record created successfully.');
        }

        if (!isset($this->errorMessage)) {
            $this->errorMessage = \Yii::t('app', 'Failed while creating record.');
        }

        parent::init();
    }

    public function run()
    {
        /** @var BaseForm $form */
        $form = $this->form;
        /** @var ActiveRecord $activeRecord */
        $activeRecord = new $this->activeRecordClass;

        $form->model = $activeRecord;
        $form->setAttributes($this->activeRecordAttributes);
        $form->setScenario($this->scenario);
        $form->loadAttributes();

        if (\Yii::$app->request->isPost && $form->load(\Yii::$app->request->post())) {
            if ($this->canSave && $form->save()) {
                \Yii::$app->session->setFlash('success', $this->successMessage);
                return $this->controller->redirect($this->redirectUrl);
            } else {
                \Yii::$app->session->setFlash('danger', $this->errorMessage);
            }
        }

        $view                        = $this->controller->getView();
        $view->title                 = $this->title;
        $view->params['breadcrumbs'] = $this->breadcrumbs;
        if ($form->model->hasAttribute($this->recordIdentifier)) {
            $view->params['breadcrumbs'][] = $form->model->getAttribute($this->recordIdentifier);
        }

        return $this->controller->render($this->view, ['model' => $form]);
    }
}