<?php

namespace api\actions;

use api\components\HttpException;
use api\components\Response;
use common\base\ActiveRecord;
use yii\base\Action;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class CreateAction extends Action
{
    /** @var string */
    public $scenario;

    /** @var ActiveRecord|string */
    public $modelClass;

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
        if ($this->modelClass === null) {
            throw new InvalidConfigException(get_class($this) . '::$modelClass must be set.');
        }
    }

    /**
     * @since 2018-05-04 12:39:29
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

        return true;
    }

    /**
     * @since 2018-02-28 13:11:32
     * @return Response
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function run()
    {
        $modelClass = $this->modelClass;

        /** @var ActiveRecord $record */
        $record             = new $modelClass();
        $record->scenario   = $this->scenario;
        $record->attributes = \Yii::$app->request->getBodyParams();

        if ($record->validate()) {
            $record->save();
            $record->refresh();

            $response          = new Response();
            $response->name    = 'Success';
            $response->message = $this->successMessage;
            $response->code    = $this->apiCodeSuccess;
            $response->status  = 201;
            $response->data    = ArrayHelper::toArray($record, $this->toArrayProperties);;

            return $response;
        }

        throw new HttpException(400, 'Creating failed.', $record->errors,
            $this->apiCodeFailed);
    }
}
