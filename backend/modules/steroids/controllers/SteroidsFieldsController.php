<?php

namespace steroids\modules\steroids\controllers;

use steroids\base\Enum;
use steroids\base\FormModel;
use steroids\base\Model;
use steroids\modules\gii\forms\FormEntity;
use steroids\modules\gii\forms\ModelEntity;
use steroids\modules\gii\helpers\GiiHelper;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class SteroidsFieldsController extends Controller
{
    public function actionMetaFetch()
    {
        return ArrayHelper::merge(
            $this->exportModels(\Yii::$app->request->post('models')),
            $this->exportEnums(\Yii::$app->request->post('enums'))
        );
    }

    protected function parseNames($names)
    {
        $result = [];
        foreach ((array)$names as $name) {
            if (!is_string($name)) {
                continue;
            }

            $name = str_replace('\\', '.', $name);
            $className = trim(str_replace('.', '\\', $name), '.');
            if (class_exists($className)) {
                $result[$name] = $className;
            }
        }
        return $result;
    }

    protected function exportEnums($names, $result = [])
    {
        foreach ($this->parseNames($names) as $name => $className) {
            if (is_subclass_of($className, Enum::class)) {
                // TODO Other data?
                /** @type Enum $className */
                $result[$name]['labels'] = $className::toFrontend();
            }
        }

        return $result;
    }

    protected function exportModels($names, $result = [])
    {
        foreach ($this->parseNames($names) as $name => $className) {
            if (is_subclass_of($className, Model::class) || is_subclass_of($className, FormModel::class)) {
                /** @type Model $className */
                $entity = is_subclass_of($className, Model::class)
                    ? ModelEntity::findOne($className)
                    : FormEntity::findOne($className);
                if (!$entity) {
                    $result[$name] = null;
                    continue;
                }

                //$result[$name]['labels'] = $className::asEnum();
                $result[$name]['fields'] = $entity->getJsFields(false);
                $result[$name]['searchFields'] = $entity->getJsFields(true);
                $result[$name]['formatters'] = $entity->getJsFormatters();
                $result = $this->exportModels(GiiHelper::findClassNamesInMeta($result[$name]), $result);
                $result = $this->exportEnums(GiiHelper::findClassNamesInMeta($result[$name]), $result);
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
            $model = str_replace('.', '\\', ArrayHelper::getValue($field, 'model'));
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
