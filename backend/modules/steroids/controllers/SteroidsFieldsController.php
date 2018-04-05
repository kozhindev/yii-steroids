<?php

namespace steroids\modules\steroids\controllers;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class SteroidsFieldsController extends Controller
{
    public function actionFetch()
    {
        $result = [];
        $fields = \Yii::$app->request->post('fields', []);
        foreach ($fields as $field) {
            // Get model and attribute properties
            $model = ArrayHelper::getValue($field, 'model');
            $attribute = ArrayHelper::getValue($field, 'attribute');
            if (!$model || !$attribute) {
                continue;
            }

            // Check model exists
            if (!class_exists($model)) {
                throw new InvalidConfigException('Not found model `' . $model . '`');
            }

            // Get type
            $metaItem = ArrayHelper::getValue($model::meta(), $attribute, []);
            $appType = ArrayHelper::getValue($metaItem, 'appType', 'string');
            $type = \Yii::$app->types->getType($appType);
            if (!$type) {
                throw new InvalidConfigException('Not found app type `' . $appType . '`');
            }

            $props = $type->getFieldData($metaItem, ArrayHelper::getValue($field, 'params', []));
            $result[] = [
                'model' => $model,
                'attribute' => $attribute,
                'props' => !empty($props) ? $props : null,
            ];
        }

        return $result;
    }
}