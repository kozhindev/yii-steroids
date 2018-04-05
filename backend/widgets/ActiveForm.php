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
        return $this->renderReact([
            'formId' => self::FORM_ID_PREFIX . $this->id,
            'action' => $this->action,
            'layout' => $this->layout,
            'layoutProps' => $this->layoutProps,
            'initialValues' => $this->getInitialValues(),
            'submitLabel' => $this->submitLabel,
            'fields' => $this->getFieldsConfig(),
        ], false);
    }

    protected function getFieldsConfig()
    {
        $model = $this->model;
        $meta = $model::meta();
        $config = [];

        foreach ($this->fields as $field) {
            $attribute = $field['attribute'];
            $metaItem = ArrayHelper::getValue($meta, $attribute, []);
            $appType = ArrayHelper::getValue($metaItem, 'appType', 'string');
            $type = \Yii::$app->types->getType($appType);
            if (!$type) {
                throw new InvalidConfigException('Not found app type `' . $appType . '`');
            }

            $config[] = array_merge(
                [
                    'label' => $model->getAttributeLabel($attribute),
                    'hint' => $model->getAttributeHint($attribute),
                    'required' => $model->isAttributeRequired($attribute),
                ],
                $type->getFieldProps($model, $attribute, $metaItem)
            );
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
            $initialValues = $this->model->$attribute;
        }

        // Apply form name
        $formName = $this->model->formName();
        if ($formName) {
            $initialValues = [$formName => $initialValues];
        }

        return $initialValues;
    }

}