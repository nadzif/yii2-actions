<?php

namespace api\actions;

use api\components\Response;
use api\components\SingleRecordAction;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class ViewAction extends SingleRecordAction
{
    /**
     * @since 2018-02-26 13:16:39
     *
     * @param string $id actually it is hashId
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function run($id)
    {
        $record = $this->findRecord($id);

        $response          = new Response();
        $response->name    = 'Success';
        $response->message = $this->successMessage;
        $response->code    = $this->apiCodeSuccess;
        $response->status  = 200;
        $response->data    = ArrayHelper::toArray($record, $this->toArrayProperties);

        return $response;
    }
}
