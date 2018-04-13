<?php

namespace steroids\views;

use steroids\modules\gii\models\EnumClass;
use steroids\modules\gii\models\FormModelClass;
use steroids\modules\gii\models\ModelClass;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $modules array */

?>

<?php
foreach ($modules as $moduleId => $items) {
    ?>
    <h3><?= $moduleId ?></h3>

    <div class="row">

        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= Html::a(
                        '<span class="glyphicon glyphicon-plus"></span>',
                        ['model', 'moduleId' => $moduleId],
                        [
                            'class' => 'btn btn-xs btn-default',
                            'style' => 'position: absolute; top: 23px; right: 32px;',
                        ]
                    ) ?>
                    <?= GridView::widget([
                        'dataProvider' => new ArrayDataProvider(['allModels' => !empty($items['models']) ? $items['models'] : []]),
                        'layout' => '{items}',
                        'emptyText' => '',
                        'columns' => [
                            [
                                'label' => 'Model',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    /** @type ModelClass $model */
                                    return Html::a($model->name, ['/gii/gii/model', 'moduleId' => $model->moduleClass->id, 'modelName' => $model->name])
                                        . ' '
                                        . Html::a(
                                            '<span class="glyphicon glyphicon-refresh"></span>',
                                            ['/gii/gii/model'],
                                            [
                                                'data-method' => 'post',
                                                'data-params' => [
                                                    'refresh' => 1,
                                                    'moduleId' => $model->moduleClass->id,
                                                    'modelName' => $model->name
                                                ]
                                            ]
                                        );
                                }
                            ],
                            'tableName'
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= Html::a(
                        '<span class="glyphicon glyphicon-plus"></span>',
                        ['crud', 'moduleId' => $moduleId],
                        [
                            'class' => 'btn btn-xs btn-default',
                            'style' => 'position: absolute; top: 23px; right: 32px;',
                        ]
                    ) ?>
                    <?= GridView::widget([
                        'dataProvider' => new ArrayDataProvider(['allModels' => !empty($items['cruds']) ? $items['cruds'] : []]),
                        'layout' => '{items}',
                        'emptyText' => '',
                        'columns' => [
                            [
                                'label' => 'CRUD',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    /** @type FormModelClass $model */
                                    return Html::a($model->name, ['/gii/gii/crud', 'moduleId' => $model->moduleClass->id, 'controllerName' => $model->name])
                                        . ' '
                                        . Html::a(
                                            '<span class="glyphicon glyphicon-refresh"></span>',
                                            ['/gii/gii/crud'],
                                            [
                                                'data-method' => 'post',
                                                'data-params' => [
                                                    'refresh' => 1,
                                                    'moduleId' => $model->moduleClass->id,
                                                    'controllerName' => $model->name
                                                ]
                                            ]
                                        );
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= Html::a(
                        '<span class="glyphicon glyphicon-plus"></span>',
                        ['form-model', 'moduleId' => $moduleId],
                        [
                            'class' => 'btn btn-xs btn-default',
                            'style' => 'position: absolute; top: 23px; right: 32px;',
                        ]
                    ) ?>
                    <?= GridView::widget([
                        'dataProvider' => new ArrayDataProvider(['allModels' => !empty($items['formModels']) ? $items['formModels'] : []]),
                        'layout' => '{items}',
                        'emptyText' => '',
                        'columns' => [
                            [
                                'label' => 'Form',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    /** @type FormModelClass $model */
                                    return Html::a($model->name, ['/gii/gii/form-model', 'moduleId' => $model->moduleClass->id, 'formModelName' => $model->name])
                                        . ' '
                                        . Html::a(
                                            '<span class="glyphicon glyphicon-refresh"></span>',
                                            ['/gii/gii/form-model'],
                                            [
                                                'data-method' => 'post',
                                                'data-params' => [
                                                    'refresh' => 1,
                                                    'moduleId' => $model->moduleClass->id,
                                                    'formModelName' => $model->name
                                                ]
                                            ]
                                        );
                                }
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= Html::a(
                        '<span class="glyphicon glyphicon-plus"></span>',
                        ['enum', 'moduleId' => $moduleId],
                        [
                            'class' => 'btn btn-xs btn-default',
                            'style' => 'position: absolute; top: 23px; right: 32px;',
                        ]
                    ) ?>
                    <?= GridView::widget([
                        'dataProvider' => new ArrayDataProvider(['allModels' => !empty($items['enums']) ? $items['enums'] : []]),
                        'layout' => '{items}',
                        'emptyText' => '',
                        'columns' => [
                            [
                                'attribute' => 'Enum',
                                'format' => 'raw',
                                'value' => function ($model) {
                                    /** @type EnumClass $model */
                                    return Html::a($model->name, ['/gii/gii/enum', 'moduleId' => $model->moduleClass->id, 'enumName' => $model->name])
                                        . ' '
                                        . Html::a(
                                            '<span class="glyphicon glyphicon-refresh"></span>',
                                            ['/gii/gii/enum'],
                                            [
                                                'data-method' => 'post',
                                                'data-params' => [
                                                    'refresh' => 1,
                                                    'moduleId' => $model->moduleClass->id,
                                                    'enumName' => $model->name
                                                ]
                                            ]
                                        );
                                }
                            ]
                        ],
                    ]) ?>
                </div>
            </div>
        </div>

    </div>
<?php } ?>
