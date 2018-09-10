<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 6/25/2018
 * Time: 12:47 PM
 */

namespace nadzif\actions\ajax;


use nadzif\actions\BaseForm;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;

class UpdateAction extends Action
{
    public $formModel;
    public $scenario     = BaseForm::SCENARIO_UPDATE;
    public $modalOptions = [];
    public $activeRecordClass;
    public $refreshGrid  = true;
    public $gridViewId;
    public $key          = 'id';
    public $form         = '@backend/actions/layouts/_form';

    public $successAlert;
    public $errorMessage;
    public $showError = true;


    public function init()
    {

        if (!isset($this->successAlert)) {
            $this->successAlert[] = [
                'type'    => 'success',
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
        $formModel           = $this->formModel;
        $formModel->scenario = $this->scenario;

        /** @var ActiveRecord $activeRecordClass */
        $activeRecordClass = new $this->activeRecordClass;

        /** @var ActiveRecord $activeRecordClass */
        $formModel->model = $activeRecordClass::findOne($requestParam);

        $formModel->loadAttributes();

        if (\Yii::$app->request->isAjax) {
            if ($formModel->load(\Yii::$app->request->post())) {

                if ($formModel->save()) {
                    return Json::encode(['data' => ['alert' => $this->successAlert]]);
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

                return $this->controller->renderAjax($this->form, $pageOptions);
            }
        }
    }
}