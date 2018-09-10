<?php


use demogorgorn\ajax\AjaxSubmitButton;
use nadzif\actions\BaseForm;
use nadzif\actions\widgets\Modal;
use rmrevin\yii\fontawesome\FAS;
use rmrevin\yii\fontawesome\FontAwesome;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/**
 * @var BaseForm     $model
 * @var string       $scenario
 * @var bool         $asModal
 * @var bool         $submitAjax
 * @var array|string $actionUrl
 * @var array        $modalOptions
 */


$formAsModal      = isset($asModal) && $asModal == true;
$submitAsAjax     = isset($submitAjax) && $submitAjax == true;
$scenario         = $model->getScenario();
$activeFormConfig = [];

if ($formAsModal) {
    $modal = Modal::begin([
        'title' => Html::tag('h6', ArrayHelper::getValue($modalOptions, 'title', Yii::t('app', 'Form')),
            ['class' => 'tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold']
        )
    ]);

} else {
    if(isset($activeFormConfig['action'])){
        $activeFormConfig['action'] = $actionUrl;
    }
}

$form      = ActiveForm::begin($activeFormConfig);
$formRules = $model->formRules();

foreach ($formRules as $attributeName => $attributeOptions) {
    if (!ArrayHelper::isIn($attributeName, $model->scenarios()[$scenario])) {
        continue;
    }

    $inputType    = ArrayHelper::getValue($attributeOptions, 'inputType', 'text');
    $inputOptions = ArrayHelper::getValue($attributeOptions, 'inputOptions', []);

//    $inputId   = $model->scenario . '-' . Html::getInputId($model, $attributeName);
    $formField = $form->field($model, $attributeName);

    switch ($attributeOptions['inputType']) {
        case 'text':
//            $inputOptions['id'] = $inputId;
            $formField->textInput($inputOptions);
            break;
        case 'textarea':
//            $inputOptions['id'] = $inputId;
            $formField->textarea($inputOptions);
            break;
        case 'password':
//            $inputOptions['id'] = $inputId;
            $formField->passwordInput($inputOptions);
            break;
        default:
            if (ArrayHelper::isIn($attributeOptions['inputType'], ['backend\widgets\Select2'])) {
                if ($model->$attributeName) {
                    $inputOptions['initValueText'] = $model->$attributeName;
                }
            }
//            $inputOptions['id']            = $inputId;
//            $inputOptions['options']['id'] = $inputId;
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

    echo AjaxSubmitButton::widget([
        'label'             => $submitLabel,
        'useWithActiveForm' => $formId,
        'ajaxOptions'       => [
            'type'    => 'POST',
            'url'     => Url::to($actionUrl),
            'success' => new JsExpression($submitSuccess),
        ],
        'options'           => ['class' => 'btn btn-info', 'type' => 'submit'],
    ]);
} else {
    echo Html::submitButton($submitLabel);
}

echo Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-secondary pull-right']);

ActiveForm::end();

if ($formAsModal) {
    Modal::end();
}
