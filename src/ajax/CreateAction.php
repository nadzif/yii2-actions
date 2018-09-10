<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 5/28/2018
 * Time: 2:57 AM
 */

namespace nadzif\actions\ajax;


use nadzif\actions\BaseForm;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class CreateAction extends Action
{
    public $formClass;
    public $scenario  = BaseForm::SCENARIO_CREATE;
    public $model;
    public $successAlert;
    public $errorMessage;
    public $showError = true;

    public function init()
    {

        if (!isset($this->successAlert)) {
            $this->successAlert[] = [
                'type'    => 'success',
                'title'   => \Yii::t('app', 'Create Success'),
                'message' => \Yii::t('app', 'Record has been Created.'),
            ];
        }

        if (!isset($this->errorMessage)) {
            $this->errorMessage = \Yii::t('app', 'Failed while creating record.');
        }

        parent::init();
    }

    public function run()
    {
        /** @var BaseForm $formModel */
        $formModel        = new $this->formClass;
        $formModel->model = $this->model;

        $formModel->setScenario($this->scenario);

        if ($formModel->load(\Yii::$app->request->post())) {

            if ($formModel->save()) {
                return Json::encode(['data' => ['alert' => $this->successAlert]]);

            } else {
                $errorAlerts = [
                    [
                        'type'    => 'warning',
                        'title'   => \Yii::t('app', 'Action Error'),
                        'message' => Html::ul(ArrayHelper::toArray($formModel->getErrors()))
                    ]
                ];

                if ($this->showError) {
                    $errorAlerts[] = [
                        'type'    => 'danger',
                        'title'   => \Yii::t('app', 'Create Failed'),
                        'message' => $this->errorMessage
                    ];
                }

                return Json::encode([
                    'data' => ['alert' => $errorAlerts]
                ]);
            }
        }
    }
}