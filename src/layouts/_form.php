<?php


use demogorgorn\ajax\AjaxSubmitButton;
use nadzif\actions\base\BaseForm;
use nadzif\actions\widgets\Modal;
use rmrevin\yii\fontawesome\FAS;
use rmrevin\yii\fontawesome\FontAwesome;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var \yii\web\View $this
 * @var BaseForm      $model
 * @var string        $scenario
 * @var bool          $asModal
 * @var bool          $submitAjax
 * @var array|string  $actionUrl
 * @var array         $modalOptions
 */


$formAsModal      = isset($asModal) && $asModal == true;
$submitAsAjax     = isset($submitAjax) && $submitAjax == true;
$scenario         = $model->getScenario();
$activeFormConfig = [];

if ($formAsModal) {
    $modal = Modal::begin([
        'title'   => Html::tag('h6', ArrayHelper::getValue($modalOptions, 'title', Yii::t('app', 'Form')),
            ['class' => 'tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold']
        ),
        'options' => [
            'class' => $scenario . '-form-action',
            'data'  => [
                'model' => (new ReflectionClass($model))->getShortName()
            ]
        ]

    ]);

} else {
    if (isset($activeFormConfig['action'])) {
        $activeFormConfig['action'] = $actionUrl;
    }
}

$form      = ActiveForm::begin($activeFormConfig);
$formRules = $model->formRules();

foreach ($formRules as $attributeName => $attributeOptions) {
    if (!ArrayHelper::isIn($attributeName, $model->scenarios()[$scenario])) {
        continue;
    }

    $inputType    = ArrayHelper::getValue($attributeOptions, 'inputType', BaseForm::INPUT_TYPE_TEXT);
    $inputOptions = ArrayHelper::getValue($attributeOptions, 'inputOptions', []);

    $inputId   = $model->scenario . '-' . Html::getInputId($model, $attributeName);
    $formField = $form->field($model, $attributeName);

    switch ($attributeOptions['inputType']) {
        case BaseForm::INPUT_TYPE_TEXT:
            $inputOptions['id'] = $inputId;
            $formField->textInput($inputOptions);
            break;
        case BaseForm::INPUT_TYPE_TEXTAREA:
            $inputOptions['id'] = $inputId;
            $formField->textarea($inputOptions);
            break;
        case BaseForm::INPUT_TYPE_PASSWORD:
            $inputOptions['id'] = $inputId;
            $formField->passwordInput($inputOptions);
            break;
        default:
            if (ArrayHelper::isIn($attributeOptions['inputType'], ['backend\widgets\Select2'])) {
                if ($model->$attributeName) {
                    $inputOptions['initValueText'] = $model->$attributeName;
                }
            }
            $inputOptions['id']            = $inputId;
            $inputOptions['options']['id'] = $inputId;
            $formField->widget($inputType, $inputOptions);
            break;
    }

    echo $formField;
}

switch ($model->scenario) {
    case $model::SCENARIO_CREATE:
        $submitLabel = Yii::t('app', 'Create');
        break;
    case $model::SCENARIO_UPDATE:
        $submitLabel = Yii::t('app', 'Update');
        break;
    default:
        $submitLabel = Yii::t('app', 'Submit');
        break;
}

if ($submitAsAjax) {
    $formId      = $form->getId();
    $modalId     = $modal->getId();
    $iconSuccess = FontAwesome::icon(FAS::_CHECK_CIRCLE);
    $iconWarning = FontAwesome::icon(FAS::_EXCLAMATION_CIRCLE);
    $iconDanger  = FontAwesome::icon(FAS::_TIMES_CIRCLE);
    $iconInfo    = FontAwesome::icon(FAS::_INFO_CIRCLE);

    $submitSuccess = <<<JS
    (function(html) {
        html = JSON.parse(html);
        console.log(html);
        console.log(typeof html);
        $('#output').html(html);
        
        if($("#$gridId-pjax").length){
            $.pjax.reload({container:"#$gridId-pjax"});
        }
        
        if(html.data !== undefined && html.data.alert != undefined){
            var alertObject = html.data.alert;
            if(Array.isArray(alertObject)){
    
                for (var i in alertObject){
                var alertData = alertObject[i];

                    switch (alertData.type){
                        case 'warning':
                            var alertIcon = '$iconWarning';
                            break;
                        case 'danger':
                            var alertIcon = '$iconDanger';
                            break;
                        case 'info':
                            var alertIcon = '$iconInfo';
                            break;
                        default:
                            var alertIcon = '$iconSuccess';
                    }
                    
                    window.FloatAlert.alert(alertData.title, alertData.message, alertData.type, alertIcon);
                } 
            }else{
                window.FloatAlert.alert(alertObject.title, alertObject.message, alertObject.type, '$iconSuccess');
            }
        }
        
        $("#$formId")[0].reset();
        if ('$asModal') {
            $("#$modalId").modal('hide');
        }
    })
JS;

    $ajaxOptions = [
        'type'    => 'POST',
        'success' => new JsExpression($submitSuccess)
    ];

    if ($scenario === $model::SCENARIO_CREATE) {
        $ajaxOptions['url'] = Url::to($actionUrl);
    }

    echo AjaxSubmitButton::widget([
        'label'             => $submitLabel,
        'useWithActiveForm' => $formId,
        'ajaxOptions'       => $ajaxOptions,
        'options'           => ['class' => 'btn btn-info pull-right', 'type' => 'submit'],
    ]);
} else {
    echo Html::submitButton($submitLabel, ['class' => 'btn btn-secondary']);
}

echo Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-secondary pull-right mr-2']);

ActiveForm::end();

if ($formAsModal) {
    Modal::end();
    if ($scenario === $model::SCENARIO_UPDATE) {
        $this->registerJs(new JsExpression('$("#' . $modalId . '").prev("button").hide()'));
    }
}
