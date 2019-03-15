<?php

namespace steroids\modules\steroids\controllers;

use steroids\base\Enum;
use steroids\base\Model;
use steroids\modules\gii\forms\ModelEntity;
use steroids\modules\gii\helpers\GiiHelper;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class SteroidsFieldsController extends Controller
{
    public function actionMetaFetch()
    {
        return $this->exportMetas(\Yii::$app->request->post('names'));
    }

    protected function exportMetas($names, $result = [])
    {
        foreach ((array)$names as $name) {
            if (!is_string($name)) {
                continue;
            }

            $className = str_replace('.', '\\', $name);
            if (!class_exists($className)) {
                $result[$name] = null;
                continue;
            }

            if (is_subclass_of($className, Enum::class)) {
                // TODO Other data?
                /** @type Enum $className */
                $result[$name]['labels'] = $className::toFrontend();
            } elseif (is_subclass_of($className, Model::class)) {
                /** @type Model $className */
                $entity = ModelEntity::findOne($className);
                if (!$entity) {
                    $result[$name] = null;
                    continue;
                }

                $result[$name]['fields'] = $entity->getJsFields();
                $result = $this->exportMetas(GiiHelper::findClassNamesInMeta($result[$name]['fields']), $result);
            }
        }

        return $result;
    }

    public function actionFieldsFetch()
    {
        $result = [];
        $fields = \Yii::$app->request->post('fields', []);
        foreach ($fields as $field) {
            // Get model and attribute properties
            $fieldId = ArrayHelper::getValue($field, 'fieldId');
            $model = ArrayHelper::getValue($field, 'model');
            $attribute = ArrayHelper::getValue($field, 'attribute');
            if (!$fieldId || !$model || !$attribute) {
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
                'fieldId' => $fieldId,
                'model' => $model,
                'attribute' => $attribute,
                'props' => !empty($props) ? $props : null,
            ];
        }

        return $result;
    }
}
