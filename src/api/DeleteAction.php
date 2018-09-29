<?php

namespace nadzif\actions\api;

use nadzif\actions\components\api\Response;
use nadzif\actions\components\api\SingleRecordAction;
use yii\web\NotFoundHttpException;

class DeleteAction extends SingleRecordAction
{
    public $isSoftDelete = false;

    public $softDeleteAttribute = 'status';

    public $softDeleteValue = 'deleted';

    /**
     * @since 2018-05-04 12:14:55
     *
     * @param $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function run($id)
    {
        $record = $this->findRecord($id);

        if ($this->isSoftDelete) {
            $attr = $this->softDeleteAttribute;

            $record->{$attr} = $this->softDeleteValue;
            $record->save();
        } else {
            $record->delete();
        }

        $response          = new Response();
        $response->name    = 'Success';
        $response->message = 'Data has been deleted';
        $response->code    = $this->apiCodeSuccess;
        $response->status  = 200;
        $response->data    = [];

        return $response;
    }
}
