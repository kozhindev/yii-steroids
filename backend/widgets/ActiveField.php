<?php

namespace steroids\widgets;

use steroids\base\Enum;
use steroids\base\Model;
use steroids\types\MoneyType;
use yii\base\Component;
use yii\base\ErrorHandler;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

class ActiveField extends Component
{

    public static $idCounter = 1;

    /**
     * @var ActiveForm
     */
    public $id;

    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var array
     */
    public $metaItem = [];

    /**
     * @var array
     */
    public $componentProps = [];

    /**
     * @var bool
     */
    public $visible = true;

    public function init()
    {
        parent::init();

        if (!$this->id) {
            $this->id = 'f' . self::$idCounter++;
        }
    }

    public function label($label)
    {
        $this->metaItem['label'] = $label;
        return $this;
    }

    public function hint($label)
    {
        $this->metaItem['label'] = $label;
        return $this;
    }

    public function render()
    {
        if (!$this->visible) {
            return '';
        }

        // Get meta item
        $model = $this->model;
        $meta = $model::meta();
        $attribute = Html::getAttributeName($this->attribute);
        $metaItem = isset($meta[$attribute]) ? $meta[$attribute] : [];
        $metaItem = array_merge($metaItem, $this->metaItem);

        // Get app type
        $appType = !empty($metaItem['appType']) ? $metaItem['appType'] : 'string';
        $type = \Yii::$app->types->getType($appType);
        if (!$type) {
            throw new Exception('Not found app type `' . $appType . '`');
        }

        // Initial values
        if ($model->formName()) {
            $this->form->initialValues[$model->formName()][$attribute] = $model->$attribute;
        } else {
            $this->form->initialValues[$attribute] = $model->$attribute;
        }
        foreach (ArrayHelper::getValue($type->frontendConfig(), 'field.refAttributeOptions', []) as $key) {
            $refAttribute = ArrayHelper::getValue($metaItem, $key);
            if ($refAttribute) {
                if ($model->formName()) {
                    $this->form->initialValues[$model->formName()][$refAttribute] = $model->$refAttribute;
                } else {
                    $this->form->initialValues[$refAttribute] = $model->$refAttribute;
                }
            }
        }

        $props = array_merge([
            'layout' => $this->form->layout,
            'layoutCols' => $this->form->layoutCols,
        ], $this->componentProps);

        // Render field
        return \Yii::$app->types->renderField($model, $attribute, $this, $props);
    }


    /**
     * @param array $props
     * @return static
     */
    public function textInput($props = [])
    {
        return $this->setAppType('string', $props);
    }

    /**
     * @param array $props
     * @return static
     */
    public function passwordInput($props = [])
    {
        return $this->setAppType('password', $props);
    }

    /**
     * @param array $props
     * @return static
     */
    public function email($props = [])
    {
        return $this->setAppType('email', $props);
    }

    /**
     * @param array $props
     * @return static
     */
    public function phone($props = [])
    {
        return $this->setAppType('phone', $props);
    }

    /**
     * @param array $props
     * @return static
     */
    public function file($props = [])
    {
        return $this->setAppType('file', $props);
    }

    /**
     * @param array $props
     * @return static
     */
    public function files($props = [])
    {
        return $this->setAppType('files', $props);
    }

    /**
     * @param array $props
     * @return static
     */
    public function date($props = [])
    {
        return $this->setAppType('date', $props);
    }

    /**
     * @param array $props
     * @return static
     */
    public function dateTime($props = [])
    {
        return $this->setAppType('dateTime', $props);
    }

    /**
     * @param string|Enum $enumClassName
     * @param array $props
     * @return static
     */
    public function enum($enumClassName, $props = [])
    {
        return $this->setAppType('dropDown', $props, [
            'items' => $enumClassName::getLabels(),
        ]);
    }

    /**
     * @param array $props
     * @return static
     */
    public function wysiwyg($props = [])
    {
        return $this->setAppType('html', $props);
    }

    /**
     * @param string $currency
     * @param array $props
     * @return static
     */
    public function money($currency, $props = [])
    {
        return $this->setAppType('money', $props, [
            MoneyType::OPTION_CURRENCY => $currency,
        ]);
    }

    /**
     * PHP magic method that returns the string representation of this object.
     * @return string the string representation of this object.
     */
    public function __toString()
    {
        // __toString cannot throw exception
        // use trigger_error to bypass this limitation
        try {
            return $this->render();
        } catch (\Exception $e) {
            ErrorHandler::convertExceptionToError($e);
            return '';
        }
    }

    protected function setAppType($name, $props = [], $config = [])
    {
        $this->metaItem = array_merge($this->metaItem, [
            'appType' => $name,
        ], $config);
        $this->componentProps = array_merge($this->componentProps, $props);
        return $this;
    }

}