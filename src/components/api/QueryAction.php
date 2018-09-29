<?php

namespace nadzif\actions\components\api;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\web\NotFoundHttpException;

class QueryAction extends \yii\base\Action
{
    /**
     * @var ActiveQuery|callable
     */
    public $query;

    public $toArrayProperties = [];

    /** @var bool Whether the user can access this action or not */
    public $canAccess = true;

    /** @var int */
    public $apiCodeSuccess = 0;
    public $apiCodeFailed  = 0;

    public $successMessage;

    /**
     * @since 2018-05-04 00:05:20
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->query === null) {
            throw new InvalidConfigException(get_class($this) . '::$query must be set.');
        }
    }

    /**
     * @since 2018-05-04 00:40:48
     * @return bool
     * @throws NotFoundHttpException
     */
    protected function beforeRun()
    {
        if ($this->canAccess instanceof \Closure) {
            $this->canAccess = \call_user_func($this->canAccess);
        }

        if (!$this->canAccess) {
            throw new NotFoundHttpException(null, $this->apiCodeFailed);
        }

        /** @var ActiveQuery $originalQuery */
        if (is_callable($this->query)) {
            $this->query = \call_user_func($this->query);
        }

        return true;
    }
}
