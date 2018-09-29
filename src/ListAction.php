<?php

namespace backend\actions;

use common\base\GridModel;
use yii\base\InvalidConfigException;

class ListAction extends \yii\base\Action {

    /**
     * @var GridModel
     */
    public $gridModel;
    public $query = false;
    public $columns;
    public $title;
    public $breadcrumbs = [];
    public $description;
    public $showToggleData = false;
    public $_list = '@backend/actions/layouts/_list';
    public $pageSizeData = [
        1 => 1,
        10 => 10,
        25 => 25,
        50 => 50,
        100 => 100,
    ];
    public $showCreateButton = true;
    public $createConfig = [];

    public function init() {
        if ($this->showCreateButton) {
            if (!isset($this->createConfig['model']) && !isset($this->createConfig['button'])) {
                throw new InvalidConfigException(\Yii::t('app', 'Set model'));

                if (!isset($this->createConfig['button']) && !isset($this->createConfig['actionUrl'])) {
                    throw new InvalidConfigException(\Yii::t('app', 'Set url action for create'));
                }
            }
        }

        if ($this->gridModel === null) {
            throw new InvalidConfigException(get_class($this) . '::$gridModel must be set.');
        }
    }

    public function run() {
        if ($this->title) {
            $this->controller->getView()->title = $this->title;
        }

        if ($this->breadcrumbs) {
            $this->controller->getView()->params['breadcrumbs'] = $this->breadcrumbs;
        }
        if ($this->description) {
            $this->controller->getView()->params['description']  = $this->description;
        }

        $dataProvider = $this->gridModel->getDataProvider($this->query);
        $columns = $this->columns ?: $this->gridModel->getColumns();

        return $this->controller->render($this->_list, [
                    'gridModel' => $this->gridModel,
                    'dataProvider' => $dataProvider,
                    'columns' => $columns,
                    'pageSizeData' => $this->pageSizeData,
                    'showCreateButton' => $this->showCreateButton,
                    'createConfig' => $this->createConfig,
                    'showToggleData' => $this->showToggleData,
        ]);
    }

}
