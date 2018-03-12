<?php

namespace steroids\widgets;

use steroids\base\Enum;
use steroids\base\Model;
use yii\bootstrap\Html;
use yii\helpers\Json;
use yii\web\View;
use yii\widgets\InputWidget;

class FrontendField extends InputWidget
{

    private static $idCounter = 0;

    /**
     * Runs the widget.
     */
    public function run()
    {
        /** @var Model $model */
        $model = $this->model;
        $options = $this->options;
        unset($options['id']);

        // Add model meta
        $modelClassName = $model::className();
        $modelMeta = $model::meta();
        if (\Yii::$app->has('frontendState')) {
            \Yii::$app->frontendState->set('config.types.metas.' . $modelClassName, $modelMeta);
        } else {
            $jsArgs = [Json::encode($modelClassName), Json::encode($modelMeta)];
            \Yii::$app->view->registerJs('__appTypes.addModelMeta(' . implode(', ', $jsArgs) . ')', View::POS_END, 'app-form-' . $model::className());
        }

        // Add enums
        foreach ($model::meta() as $item) {
            if (!empty($item['enumClassName'])) {
                /** @var Enum $enumClass */
                $enumClass = $item['enumClassName'];
                $enumClassName = $enumClass::className();
                $props = [
                    'labels' => $enumClass::getLabels(),
                ];

                if (\Yii::$app->has('frontendState')) {
                    \Yii::$app->frontendState->set('config.types.enums.' . $enumClassName, $props);
                } else {
                    $jsArgs = [Json::encode($enumClassName), Json::encode($props)];
                    \Yii::$app->view->registerJs('__appTypes.addEnum(' . implode(', ', $jsArgs) . ')', View::POS_END, 'app-enum-' . $enumClass::className());
                }
            }
        }

        // Render field
        $props = array_merge([
            'formId' => $this->field ? $this->field->form->id : 'f' . ++self::$idCounter,
            'model' => $model::className(),
            'prefix' => $model->formName(),
            'attribute' => Html::getAttributeName($this->attribute),
        ], $options);
        if (\Yii::$app->has('frontendState')) {
            \Yii::$app->frontendState->add('config.types.toRenderField', [$this->id, $props]);
        } else {
            $jsArgs = [Json::encode($this->id), Json::encode($props)];
            \Yii::$app->view->registerJs('__appTypes.renderField(' . implode(', ', $jsArgs) . ')', View::POS_END, $this->id);
        }
        return Html::tag('span', '', ['id' => $this->id]);
    }

}