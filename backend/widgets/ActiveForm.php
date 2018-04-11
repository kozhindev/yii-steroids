<?php

namespace steroids\widgets;

use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\Widget;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class ActiveForm extends Widget
{
    const FORM_ID_PREFIX = 'ActiveForm_';

    /**
     * @var array|string
     */
    public $action = '';

    /**
     * @var Model|FormModel
     */
    public $model;

    /**
     * @var array
     */
    public $fields;

    /**
     * @var array
     */
    public $layout = 'horizontal';

    /**
     * @var array
     */
    public $layoutProps;

    /**
     * @var array
     */
    public $submitLabel;

    public function init()
    {
        parent::init();

        // Normalize fields
        foreach ($this->fields as $key => $field) {
            if (is_string($field)) {
                $this->fields[$key] = ['attribute' => $field];
            }
            if (!isset($field['attribute']) && is_string($key)) {
                $this->fields[$key]['attribute'] = $key;
            }
        }
    }

    /**
     * @param Model|FormModel $model
     * @param string|null $formName
     * @return array
     * @throws
     */
    public static function renderAjax($model, $formName = null)
    {
        $result = [];
        if ($model->hasErrors()) {
            $errors = $model->getErrors();

            // Apply form name
            $formName = $formName !== null ? $formName : $model->formName();
            if ($formName) {
                $errors = [$formName => $errors];
            }

            return ['errors' => $errors];
        }
        return $result;
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        $submitLabel = $this->submitLabel;
        if (!$submitLabel) {
            $submitLabel = $this->model->isNewRecord
                ? \Yii::t('app', 'Добавить')
                : \Yii::t('app', 'Сохранить');
        }

        return $this->renderReact([
            'formId' => self::FORM_ID_PREFIX . $this->id,
            'action' => $this->action,
            'prefix' => $this->model->formName(),
            'layout' => $this->layout,
            'layoutProps' => $this->layoutProps,
            'initialValues' => $this->getInitialValues(),
            'submitLabel' => $submitLabel,
            'fields' => $this->getFieldsConfig(),
        ], false);
    }

    protected function getFieldsConfig()
    {
        $config = [];
        foreach ($this->fields as $field) {
            $attribute = $field['attribute'];
            $field = array_merge(
                [
                    'label' => $this->model->getAttributeLabel($attribute),
                    'hint' => $this->model->getAttributeHint($attribute),
                    'required' => $this->model->isAttributeRequired($attribute),
                ],
                $field
            );

            $type = \Yii::$app->types->getTypeByModel($this->model, $attribute);
            $type->prepareFieldProps($this->model, $attribute, $field);

            $config[] = $field;
        }
        return $config;
    }

    /**
     * @return array|mixed
     * @throws \yii\base\InvalidConfigException
     */
    protected function getInitialValues()
    {
        // Load defaults
        if ($this->model instanceof Model && $this->model->isNewRecord) {
            $this->model->loadDefaultValues();
        }

        // Store init values
        $initialValues = [];
        foreach ($this->fields as $field) {
            $attribute = $field['attribute'];
            $initialValues[$attribute] = $this->model->$attribute;
        }

        // Apply form name
        $formName = $this->model->formName();
        if ($formName) {
            $initialValues = [$formName => $initialValues];
        }

        return $initialValues;
    }

}