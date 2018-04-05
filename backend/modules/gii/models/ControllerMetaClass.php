<?php

namespace steroids\modules\gii\models;

use steroids\modules\gii\helpers\GiiHelper;
use yii\helpers\ArrayHelper;

/**
 * @property array $meta
 * @property string[] $customColumns
 */
class ControllerMetaClass extends ControllerClass
{
    /**
     * @var ControllerClass
     */
    public $controllerClass;

    /**
     * @var string
     */
    public $modelClassName;

    /**
     * @var ModelClass
     */
    public $modelClass;

    /**
     * @var array
     */
    public $modelAttributes;

    /**
     * @var string
     */
    public $formModelClassName;

    /**
     * @var FormModelClass
     */
    public $formModelClass;

    /**
     * @var array
     */
    public $formModelAttributes;

    /**
     * @var string
     */
    public $url;

    /**
     * @var string|string[]
     */
    public $roles;

    /**
     * @var string
     */
    public $title;

    /**
     * @var bool
     */
    public $createActionIndex;

    /**
     * @var bool
     */
    public $withDelete;

    /**
     * @var bool
     */
    public $withSearch;

    /**
     * @var bool
     */
    public $createActionCreate;

    /**
     * @var bool
     */
    public $createActionUpdate;

    /**
     * @var bool
     */
    public $createActionView;

    /**
     * @return array[]
     */
    public function getMeta()
    {
        $meta = ArrayHelper::toArray($this, [
            static::className() => [
                'modelClassName',
                'modelAttributes',
                'formModelClassName',
                'formModelAttributes',
                'url',
                'roles',
                'title',
                'createActionIndex',
                'withDelete',
                'withSearch',
                'createActionCreate',
                'createActionUpdate',
                'createActionView',
            ]
        ]);

        foreach (['modelAttributes', 'formModelAttributes'] as $key) {
            $attributes = [];
            foreach ($meta[$key] ?: [] as $name => $params) {
                $attributes[] = array_merge($params, [
                    'name' => $name
                ]);
            }
            $meta[$key] = $attributes;
        }

        return $meta;
    }

    /**
     * @param array $params
     */
    public function setMeta($params)
    {
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'modelClassName':
                    $this->$key = $value;
                    $this->modelClass = ModelClass::findOne($value);
                    break;

                case 'formModelClassName':
                    $this->$key = $value;
                    $this->formModelClass = FormModelClass::findOne($value);
                    break;

                case 'roles':
                    $this->$key = is_string($value)
                        ? preg_split('/[^\w\d@*-_]+/', $value, -1, PREG_SPLIT_NO_EMPTY)
                        : $value;
                    if (empty($this->$key)) {
                        $this->$key = null;
                    }
                    break;

                case 'modelAttributes':
                case 'formModelAttributes':
                    if (!ArrayHelper::isAssociative($value)) {
                        $attributes = [];
                        foreach ($value as $item) {
                            $name = $item['name'];
                            unset($item['name']);

                            if (!empty($item)) {
                                $attributes[$name] = $item;
                            }
                        }
                        $this->$key = !empty($attributes) ? $attributes : null;
                    } else {
                        $this->$key = $value;
                    }
                    break;

                case 'createActionIndex':
                case 'withDelete':
                case 'withSearch':
                case 'createActionCreate':
                case 'createActionUpdate':
                case 'createActionView':
                    $this->$key = (bool)$value;
                    break;

                default:
                    $this->$key = $value;
            }
        }
    }

    /**
     * @param string $indent
     * @param array $useClasses
     * @return mixed|string
     */
    public function renderMeta($indent = '', &$useClasses = [])
    {
        $meta = $this->getMeta();
        $meta['modelAttributes'] = $this->modelAttributes;
        $meta['formModelAttributes'] = $this->formModelAttributes;

        if ($this->modelClass) {
            $meta['modelClassName'] = new ValueExpression($this->modelClass->name . '::className()');
            $useClasses[] = $this->modelClass->className;
        }
        if ($this->formModelClass) {
            $meta['formModelClassName'] = new ValueExpression($this->formModelClass->name . '::className()');
            $useClasses[] = $this->formModelClass->className;
        }

        foreach ($meta as $key => $value) {
            if (is_null($value) || $value === '') {
                unset($meta[$key]);
            }
        }

        foreach (['title'] as $key) {
            if (!empty($meta[$key])) {
                $meta[$key] = new ValueExpression('Yii::t(\'app\', ' . GiiHelper::varExport($meta[$key]) . ')');
            }
        }

        return GiiHelper::varExport($meta, $indent);
    }

    public function fields()
    {
        return [
            'className',
            'name',
            'meta',
        ];
    }

}