<?php

namespace backend\actions;

use backend\base\ActiveRecord;
use backend\base\FormModel;

/**
 * Class UpdateAction
 *
 * @package backend\actions
 * @property string    $hashId,
 * @property FormModel $formModel
 */
class UpdateAction extends \yii\base\Action
{

    public $title;
    public $subtitle;
    public $breadcrumbs;
    public $recordIdentifier = 'name';

    public $formModel;
    public $scenario = FormModel::SCENARIO_UPDATE;
    public $activeRecordClass;

    public $redirectUrl;
    public $successMessage;
    public $errorMessage;

    public $form = '@backend/actions/layouts/_form';


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

    public function run()
    {
        $formModel = $this->formModel;
        /** @var ActiveRecord $model */
        $model = new $this->activeRecordClass;

        $formModel->model = $model::findOne(\Yii::$app->request->get('hashId'));

        $formModel->setScenario($this->scenario);
        $formModel->loadAttributes();

        if (\Yii::$app->request->isPost && $formModel->load(\Yii::$app->request->post())) {
            if ($formModel->save()) {
                \Yii::$app->session->setFlash('success', $this->successMessage);
                return $this->controller->redirect($this->redirectUrl);
            } else {
                \Yii::$app->session->setFlash('danger', $this->errorMessage);
            }
        }

        $view                          = $this->controller->getView();
        $view->title                   = $this->title;
        $view->params['breadcrumbs']   = $this->breadcrumbs;
        $view->params['breadcrumbs'][] = $formModel->model->getAttribute($this->recordIdentifier);

        return $this->controller->render($this->form, [
            'model' => $formModel,
        ]);
    }
}