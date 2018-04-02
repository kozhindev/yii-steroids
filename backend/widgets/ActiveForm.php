<?php

namespace steroids\widgets;

use steroids\base\FormModel;
use steroids\base\Model;
use steroids\base\Widget;

class ActiveForm extends Widget
{
    /**
     * @var array|string
     */
    public $action = '';

    /**
     * @var Model|FormModel
     */
    public $model = '';

    /**
     * @var array
     */
    public $fields;

    /**
     * @var array
     */
    public $submitLabel;

    /**
     * @var array
     */
    public $props = [];

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


    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        //$content = ob_get_clean();
        //echo Html::tag('span', $content, ['id' => $this->id . '-content']);

        /*if (Yii::$app->has('frontendState')) {
            Yii::$app->frontendState->add('config.types.toRenderForm', [$this->id, $props]);
        } else {
            $jsArgs = [Json::encode($this->id), Json::encode($props)];
            \Yii::$app->view->registerJs('__appTypes.renderForm(' . implode(', ', $jsArgs) . ')', View::POS_END, $this->id . '-form');
        }*/


        // TODO
        // TODO
        // TODO
        // TODO
    }

}