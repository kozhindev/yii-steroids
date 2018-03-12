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

    protected function renderFilterCellContent()
    {
        $model = $this->grid->filterModel;
        if ($this->filter === null && $this->attribute && ($model instanceof Model || $model instanceof FormModel)) {
            if ($this->controllerMeta && !ArrayHelper::getValue($this->controllerMeta, 'formModelAttributes.' . $this->attribute . '.showInFilter')) {
                return $this->grid->emptyCell;
            }
            if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
                return \Yii::$app->types->renderField($model, $this->attribute, null, ['layout' => 'inline']);
            }
        }

        return parent::renderFilterCellContent();
    }

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