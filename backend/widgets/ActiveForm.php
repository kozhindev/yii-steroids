<?php

namespace steroids\widgets;

use steroids\base\FormModel;
use Yii;
use steroids\base\Model;
use steroids\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\View;

class ActiveForm extends Widget
{
    /**
     * @var array|string
     */
    public $action = '';

    /**
     * @var string
     */
    public $method = 'post';

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var string
     */
    public $layout = 'horizontal';

    /**
     * @var string
     */
    public $layoutCols = [3, 6];

    /**
     * @var string
     */
    public $fieldClass = 'steroids\widgets\ActiveField';

    /**
     * @var array
     */
    public $fieldConfig = [];

    /**
     * @var array
     */
    public $initialValues = [];

    /**
     * @param Model|FormModel $model
     * @param string|null $formName
     * @return array
     */
    public static function renderAjax($model, $formName = null)
    {
        $result = [];
        if ($model->hasErrors()) {
            $formName = $formName !== null ? $formName : $model->formName();
            if ($formName) {
                $result['errors'][$formName] = $model->getErrors();
            } else {
                $result['errors'] = $model->getErrors();
            }
        }
        return $result;
    }

    /**
     * Initializes the widget.
     * This renders the form open tag.
     */
    public function init()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        ob_start();
        ob_implicit_flush(false);

        $props = [
            'formId' => $this->id,
            'contentId' => $this->id . '-content',
            'action' => $this->action,
            'method' => $this->method,
            'layout' => $this->layout,
            'layoutCols' => $this->layoutCols,
        ];

        if (Yii::$app->has('frontendState')) {
            Yii::$app->frontendState->add('config.types.toRenderForm', [$this->id, $props]);
        } else {
            $jsArgs = [Json::encode($this->id), Json::encode($props)];
            \Yii::$app->view->registerJs('__appTypes.renderForm(' . implode(', ', $jsArgs) . ')', View::POS_END, $this->id . '-form');
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        $content = ob_get_clean();
        echo Html::tag('span', '', ['id' => $this->id]);
        echo Html::tag('span', $content, ['id' => $this->id . '-content']);

        if (Yii::$app->has('frontendState')) {
            Yii::$app->frontendState->set('form.' . $this->id . '.values', $this->initialValues);
        } else {
            $state = [
                'form' => [
                    $this->id => [
                        'values' => $this->initialValues,
                    ],
                ],
            ];
            \Yii::$app->view->registerJs('window.APP_REDUX_PRELOAD_STATES = [];', View::POS_HEAD);
            \Yii::$app->view->registerJs('window.APP_REDUX_PRELOAD_STATES.push(' . Json::encode($state) . ')', View::POS_HEAD, $this->id . '-state');
        }
    }

    /**
     * @param Model|FormModel $model
     * @param string $attribute
     * @param array $options
     * @return ActiveField
     */
    public function field($model, $attribute, $options = [])
    {
        $config = $this->fieldConfig;
        if ($config instanceof \Closure) {
            $config = call_user_func($config, $model, $attribute);
        }
        if (!isset($config['class'])) {
            $config['class'] = $this->fieldClass;
        }

        if ($model instanceof Model) {
            $authManager = Yii::$app->has('authManager') && get_class(Yii::$app->authManager) === 'steroids\components\AuthManager'
                ? Yii::$app->authManager
                : null;
            $ruleName = $model->isNewRecord ? 'create' : 'update';
            if ($authManager && !$authManager->checkAttributeAccess(Yii::$app->user->model, $model, $attribute, $ruleName)) {
                $config['visible'] = false;
            }
        }

        return Yii::createObject(ArrayHelper::merge($config, $options, [
            'model' => $model,
            'attribute' => $attribute,
            'form' => $this,
        ]));
    }

    /**
     * @param string $label
     * @param array $options
     * @return string
     */
    public function submitButton($label = 'Сохранить', $options = [])
    {
        $buttonStr = Html::submitButton($label, array_merge($options, ['class' => 'btn btn-primary']));
        if ($this->layout == 'horizontal') {
            return "<div class=\"form-group\"><div class=\"col-sm-offset-3 col-sm-6\">$buttonStr</div></div>";
        } else {
            return "<div class=\"form-group\">$buttonStr</div>";
        }
    }

    /**
     * @param Model|FormModel $model
     * @param string[] $attributes
     * @return string
     */
    public function fields($model, $attributes = null)
    {
        if ($attributes === null) {
            $attributes = $model->safeAttributes();
        }

        $html = [];
        foreach ($attributes as $attribute) {
            $html[] = $this->field($model, $attribute);
        }
        return implode("\n", $html);
    }

    /**
     * @param Model $model
     * @param array $buttons
     * @return string
     */
    public function controls($model, $buttons = [])
    {
        $defaultButtons = [
            'submit' => [
                'label' => $model->isNewRecord ? 'Добавить' : 'Сохранить',
                'order' => 0,
            ],
            'cancel' => [
                'label' => 'Назад',
                'url' => ['index'],
                'order' => 10,
            ],
        ];
        $buttons = array_merge($defaultButtons, $buttons);
        ArrayHelper::multisort($buttons, 'order');

        $buttonHtmls = [];
        foreach ($buttons as $id => $button) {
            if (!$button) {
                continue;
            }

            if (isset($defaultButtons[$id])) {
                $button = array_merge($defaultButtons[$id], $button);
            }

            ArrayHelper::remove($button, 'order');
            $label = ArrayHelper::remove($button, 'label');
            $url = ArrayHelper::remove($button, 'url');

            if ($id === 'submit') {
                $buttonHtmls[] = Html::submitButton($label, array_merge(['class' => 'btn btn-primary'], $button));
            } else {
                $buttonHtmls[] = Html::a($label, $url, array_merge(['class' => 'btn btn-default'], $button));
            }
        }

        $html = implode(' ', $buttonHtmls);
        if ($this->layout == 'horizontal') {
            return "<div class=\"form-group\"><div class=\"col-sm-offset-3 col-sm-6\">$html</div></div>";
        } else {
            return "<div class=\"form-group\">$html</div>";
        }
    }

    public function beginFieldset($title, $options = [])
    {
        $optionsStr = '';
        foreach ($options as $key => $value) {
            $optionsStr .= " $key=\"$value\"";
        }

        return "<fieldset $optionsStr><div class=\"form-group\"><div class=\"col-sm-offset-3 col-sm-6\"><b>$title</b></div></div><div style=\"margin-left: 30px\">";
    }

    public function endFieldset()
    {
        return "</div></fieldset>";
    }

}