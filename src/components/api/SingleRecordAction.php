<?php

namespace api\components;

use yii\base\InvalidArgumentException;
use yii\web\NotFoundHttpException;

class SingleRecordAction extends QueryAction
{

    /**
     * @since 2018-05-04 12:22:44
     *
     * @param $id
     *
     * @return array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    protected function findRecord($id)
    {
        try {
            $modelClass = new $this->query->modelClass;
            $record = $this->query->andWhere([$modelClass::tableName() . '.id' => $id])->one();

            if (empty($record)) {
                throw new InvalidArgumentException();
            }
        } catch (InvalidArgumentException $e) {
            throw new NotFoundHttpException(null, $this->apiCodeFailed);
        }

        return $record;
    }
}
