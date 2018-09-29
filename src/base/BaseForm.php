<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 5/24/2018
 * Time: 1:59 AM
 */

namespace nadzif\actions\base;


use yii\base\Model;
use yii\db\ActiveRecord;

class BaseForm extends Model
{

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const INPUT_TYPE_TEXT     = 'text';
    const INPUT_TYPE_TEXTAREA = 'textarea';
    const INPUT_TYPE_PASSWORD = 'password';

    /** @var ActiveRecord */
    public $model;

    public $validateForm = true;

    public function formRules()
    {
        $defaultRules = [];
        foreach ($this->attributes as $attributeKey => $defaultRule) {
            $defaultRules[$attributeKey] = ['inputType' => self::INPUT_TYPE_TEXT];
        }

        return $defaultRules;
    }

    public function loadAttributes($attributes = [])
    {
        if ($attributes == []) {
            $this->attributes = $this->getModel()->attributes;
        } else {
            $this->attributes = $attributes;
        }
    }


    public function getModel()
    {
        return $this->model;
    }

    public function save($runValidation = true)
    {
        $this->setModelAttributes();
        $validated = $runValidation ? ($this->validateForm ? $this->validate() : true) : true;
        return $validated && $this->getModel()->save();
    }

    public function setModelAttributes()
    {
        $createAttributes = $this->scenarios()[$this->getScenario()];
        foreach ($createAttributes as $createAttribute) {
            try {
                $this->getModel()->$createAttribute = $this->$createAttribute;
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    protected function getAttributesKey()
    {
        return array_keys($this->attributes);
    }


}