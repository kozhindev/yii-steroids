<?php

namespace steroids\modules\gii\widgets\EnumEditor;

use steroids\base\Widget;
use steroids\modules\gii\models\EnumClass;
use steroids\modules\gii\models\ModuleClass;

class EnumEditor extends Widget
{
    public $initialValues;

    public function init()
    {
        echo $this->renderReact([
            'initialValues' => !empty($this->initialValues) ? $this->initialValues : null,
            'csrfToken' => \Yii::$app->request->csrfToken,
            'modules' => ModuleClass::findAll(),
            'enums' => EnumClass::findAll(),
        ]);
    }


}