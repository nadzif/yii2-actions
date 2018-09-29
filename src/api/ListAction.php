<?php

namespace api\actions;

use api\components\QueryAction;
use api\components\Response;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ListAction extends QueryAction
{

    /**
     * @since 2018-05-04 00:41:53
     * @return Response
     */
    public function run()
    {
        $modelClass = new $this->query->modelClass;
        $tableName  = $modelClass::tableName();

        $this->query
            ->andWhere(['<=', $tableName . '.createdAt', $this->controller->firstRequestTime])
            ->addOrderBy([$tableName . '.createdAt' => \SORT_DESC]);

        // setup data provider
        $dataProvider        = new ActiveDataProvider();
        $dataProvider->query = $this->query;

        $getAll = (\Yii::$app->request->get('page') == 'all');

        if ($getAll) {
            $dataProvider->setPagination(false);
        }

        // get the result and pagination
        $result     = $dataProvider->getModels();
        $pagination = $dataProvider->getPagination();

        $meta = [
            'record' => [
                'current' => $dataProvider->getCount(),
                'total'   => $dataProvider->getTotalCount()
            ],
        ];

        if ($pagination instanceof Pagination) {
            $meta['page']  = [
                'current' => $pagination->getPage() + 1,
                'total'   => $pagination->getPageCount()
            ];
            $meta['links'] = $pagination->getLinks();
        }


        $response          = new Response();
        $response->name    = 'Success';
        $response->status  = 200;
        $response->message = $this->successMessage;
        $response->code    = 0;
        $response->data    = ArrayHelper::toArray($result, $this->toArrayProperties);
        $response->meta    = $meta;

        return $response;
    }
}