<?php

namespace steroids\widgets;

use steroids\base\Model;
use steroids\components\AuthManager;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class GridView extends \yii\grid\GridView
{
    public $dataColumnClass = '\app\core\widgets\AppDataColumn';
    public $tableOptions = ['class' => 'table table-hover'];
    public $layout = "<div class='table-responsive'>{items}</div>\n{pager}";

    /**
     * @var array
     */
    public $actions = [];

    /**
     * @var array
     */
    public $actionParams = [];

    /**
     * @var string
     */
    public $pkParam;

    /**
     * @var array
     */
    public $controllerMeta;

    protected function guessColumns()
    {
        if ($this->dataProvider instanceof ActiveDataProvider
            && $this->dataProvider->query instanceof ActiveQuery) {
            /** @var ActiveQuery $query */
            $query = $this->dataProvider->query;

            /** @var Model $modelClass */
            $modelClass = $query->modelClass;

            foreach ($modelClass::meta() as $attribute => $item) {
                if (ArrayHelper::getValue($this->controllerMeta, 'modelAttributes.' . $attribute . '.showInTable')) {
                    $this->columns[] = [
                        'controllerMeta' => $this->controllerMeta,
                        'attribute' => $attribute,
                        'label' => $item['label'],
                        'format' => !empty($item['formatter']) ? $item['formatter'] : 'text',
                    ];
                }
            }
        } else {
            parent::guessColumns();
        }
    }

    protected function initColumns()
    {
        parent::initColumns();

        // Column access
        if ($this->dataProvider instanceof ActiveDataProvider) {
            $authManager = Yii::$app->has('authManager') && Yii::$app->authManager instanceof AuthManager
                ? Yii::$app->authManager
                : null;
            if ($authManager) {
                /** @var ActiveQuery $query */
                $query = $this->dataProvider->query;

                foreach ($this->columns as $i => $column) {
                    /** @type DataColumn $column */
                    if (!$column->attribute) {
                        continue;
                    }
                    if ($column->attribute && !$authManager->checkAttributeAccess(Yii::$app->user->model, $query->modelClass, $column->attribute, AuthManager::RULE_MODEL_VIEW)) {
                        unset($this->columns[$i]);
                    }
                }
            }
        }

        if (!empty($this->actions)) {
            $buttons = [];
            $templateButtons = [];

            foreach ($this->actions as $name => $action) {
                if (is_string($action)) {
                    $templateButtons[] = $action;
                } else {
                    $templateButtons[] = $name;
                    $buttons[$name] = $action;
                }
            }

            $this->columns[] = Yii::createObject([
                'class' => ActionColumn::class,
                'grid' => $this,
                'template' => '{' . implode('} {', $templateButtons) . '}',
                'buttons' => $buttons,
                'urlCreator' => function($action, Model $model) {
                    $pkParam = $this->pkParam ?: $model::getRequestParamName();

                    return Url::to(array_merge([$action, $pkParam => $model->primaryKey], $this->actionParams));
                },
                'visibleButtons' => [
                    'view' => function(Model $model) {
                        $pkParam = $this->pkParam ?: $model::getRequestParamName();
                        $url = array_merge(['view', $pkParam => $model->primaryKey], $this->actionParams);

                        return \Yii::$app->siteMap->isAllowAccess($url) && $model->canView(Yii::$app->user->model);
                    },
                    'update' => function(Model $model) {
                        $pkParam = $this->pkParam ?: $model::getRequestParamName();
                        $url = array_merge(['update', $pkParam => $model->primaryKey], $this->actionParams);

                        return \Yii::$app->siteMap->isAllowAccess($url) && $model->canUpdate(Yii::$app->user->model);
                    },
                    'delete' => function(Model $model) {
                        $pkParam = $this->pkParam ?: $model::getRequestParamName();
                        $url = array_merge(['delete', $pkParam => $model->primaryKey], $this->actionParams);

                        return \Yii::$app->siteMap->isAllowAccess($url) && $model->canDelete(Yii::$app->user->model);
                    },
                ],
            ]);
        }
    }
}