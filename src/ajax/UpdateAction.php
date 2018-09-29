<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 6/25/2018
 * Time: 12:47 PM
 */

namespace nadzif\actions\ajax;


use nadzif\actions\base\BaseForm;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class UpdateAction extends Action
{
    public $view         = '@nadzif/actions/layouts/_form';

    public $form;
    public $scenario     = BaseForm::SCENARIO_UPDATE;
    public $modalOptions = [];

    public $activeRecordClass;
    public $key          = 'id';

    public $refreshGrid  = true;
    public $gridViewId;

    public $flashKeySuccess = 'success';
    public $flashKeyError   = 'danger';

    public $successMessage;
    public $errorMessage;

    public $showError = true;

    public function init()
    {

        if (!isset($this->successMessage)) {
            $this->successMessage[] = [
                'type'    => $this->flashKeySuccess,
                'title'   => \Yii::t('app', 'Update Success'),
                'message' => \Yii::t('app', 'Record has been updated.'),
            ];
        }

        if (!isset($this->errorMessage)) {
            $this->errorMessage = \Yii::t('app', 'Failed while updating record.');
        }

        parent::init();
    }

    public function run()
    {

        $requestParam = \Yii::$app->request->get($this->key);

        /** @var BaseForm $formModel */
        $formModel           = $this->form;
        $formModel->scenario = $this->scenario;

        /** @var ActiveRecord $activeRecordClass */
        $activeRecordClass = new $this->activeRecordClass;

        /** @var ActiveRecord $activeRecordClass */
        $formModel->model = $activeRecordClass::findOne($requestParam);

        $formModel->loadAttributes();

        if (\Yii::$app->request->isAjax) {
            if ($formModel->load(\Yii::$app->request->post())) {

                if ($formModel->save()) {
                    return Json::encode(['data' => ['alert' => $this->successMessage]]);
                } else {
                    $errorAlerts = [
                        [
                            'type'    => 'info',
                            'message' => Html::ul(ArrayHelper::toArray($formModel->getErrors()))
                        ]
                    ];

                    if ($this->showError) {
                        $errorAlerts[] = [
                            'type'    => 'danger',
                            'title'   => \Yii::t('app', 'Update Failed'),
                            'message' => $this->errorMessage
                        ];
                    }

                    return Json::encode([
                        'data' => ['alert' => $errorAlerts]
                    ]);
                }

            } else {
                $pageOptions = [
                    'model'        => $formModel,
                    'asModal'      => true,
                    'modalOptions' => ArrayHelper::merge([
                        'title' => \Yii::t('app', 'Update {tableName}', [
                            'tableName' => $formModel->model->tableSchema->name
                        ])
                    ], $this->modalOptions),
                    'submitAjax'   => true,
                    'actionUrl'    => [$this->controller->getRoute(), $this->key => $requestParam],
                ];

                if ($this->refreshGrid) {
                    $pageOptions['gridViewId'] = $this->gridViewId;
                }

                return $this->controller->renderAjax($this->view, $pageOptions);
            }
        }
    }
}