<?php

namespace api\components;

use yii\base\Model;

abstract class BaseForm extends Model
{
    /**
     * @since 2018-05-06 23:08:20
     * @return mixed
     *
     * What to do when submit. This will be called in the FormAction
     */
    abstract public function submit();

    /**
     * @since 2018-05-06 23:08:33
     * @return mixed
     *
     * How to format the data
     */
    abstract public function response();

    /**
     * @since 2018-05-10 10:44:29
     * @return array
     *
     * Format meta for this form
     */
    public function meta() {
        return [];
    }
}