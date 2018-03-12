<?php

namespace app\views;

use app\core\widgets\CrudControls;
use steroids\base\CrudController;
use steroids\base\FormModel;
use steroids\widgets\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */
/* @var $searchModel FormModel|null */

/** @var CrudController $controller */
$controller = $this->context;

/** @var array $meta */
$meta = $controller::meta();

?>
<h1><?= \Yii::$app->siteMap->getTitle() ?></h1>

<?= CrudControls::widget([
    'actionParams' => $controller::getMetaUrlParams(),
]) ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'controllerMeta' => $meta,
    'actions' => [
        ArrayHelper::getValue($meta, 'createActionView') ? 'view' : null,
        ArrayHelper::getValue($meta, 'createActionUpdate') ? 'update' : null,
        ArrayHelper::getValue($meta, 'withDelete') ? 'delete' : null,
    ],
    'actionParams' => $controller::getMetaUrlParams(),
]); ?>