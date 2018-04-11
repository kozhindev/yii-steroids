<?php

namespace steroids\widgets;

use steroids\base\FormModel;
use steroids\base\Model;
use yii\helpers\ArrayHelper;

class DataColumn extends \yii\grid\DataColumn
{
    /**
     * @var array
     */
    public $controllerMeta;

    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->content === null && $this->value === null && $this->format === 'text' && $this->attribute && $model instanceof Model) {
            $options = $this->options;
            $options['forTable'] = true;
            return \Yii::$app->types->renderValue($model, $this->attribute, $options);
        }

        return parent::renderDataCellContent($model, $this->attribute, $index);
    }
}