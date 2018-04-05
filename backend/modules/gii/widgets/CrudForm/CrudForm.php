<?php

namespace steroids\modules\gii\widgets\CrudForm;

use steroids\base\Widget;
use steroids\modules\gii\models\ControllerClass;
use steroids\modules\gii\models\FormModelClass;
use steroids\modules\gii\models\ModelClass;
use steroids\modules\gii\models\ModuleClass;

class CrudForm extends Widget
{
    public $initialValues;

    public function init()
    {
        echo $this->renderReact([
            'initialValues' => !empty($this->initialValues) ? $this->initialValues : null,
            'csrfToken' => \Yii::$app->request->csrfToken,
            'modules' => array_map(function($moduleClass) {
                /** @type ModuleClass $moduleClass */
                return [
                    'id' => $moduleClass->id,
                    'className' => $moduleClass->className,
                ];
            }, ModuleClass::findAll()),
            'models' => ModelClass::findAll(),
            'formModels' => FormModelClass::findAll(),
            'controllers' => ControllerClass::findAll(),
        ]);
    }


}