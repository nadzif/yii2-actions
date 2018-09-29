<?php

namespace api\actions;

use api\components\HttpException;
use api\components\Response;
use api\components\SingleRecordAction;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class UpdateAction extends SingleRecordAction
{
    /** @var string */
    public $scenario;

    public function init()
    {
        parent::init();

        if ($this->scenario === null) {
            throw new InvalidConfigException('$scenario must be set');
        }
    }

    /**
     * @since 2018-02-15 13:17:24
     *
     * @param $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     * @throws HttpException
     */
    public function run($id)
    {
        $record = $this->findRecord($id);

        $record->scenario   = $this->scenario;
        $record->attributes = \Yii::$app->request->getBodyParams();

        if ($record->validate()) {
            $record->save();
            $record->refresh();

            $response          = new Response();
            $response->name    = 'Success';
            $response->message = $this->successMessage;
            $response->code    = $this->apiCodeSuccess;
            $response->status  = 200;
            $response->data    = ArrayHelper::toArray($record, $this->toArrayProperties);

            return $response;
        }

        throw new HttpException(400, 'Update data failed', $record->errors, $this->apiCodeFailed);
    }
}
