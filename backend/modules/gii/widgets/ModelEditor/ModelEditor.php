<?php

namespace steroids\modules\gii\widgets\ModelEditor;

use steroids\base\Type;
use steroids\base\Widget;
use steroids\modules\gii\helpers\GiiHelper;
use steroids\modules\gii\models\ModelClass;
use steroids\modules\gii\models\ModuleClass;

class ModelEditor extends Widget
{
    public $initialValues;

    public function init()
    {
        echo $this->renderReact([
            'initialValues' => !empty($this->initialValues) ? $this->initialValues : null,
            'csrfToken' => \Yii::$app->request->csrfToken,
            'modules' => ModuleClass::findAll(),
            'models' => ModelClass::findAll(),
            'tableNames' => GiiHelper::getTableNames(),
            'dbTypes' => GiiHelper::getDbTypes(),
            'appTypes' => array_map(function($appType) {
                /** @type Type $appType */
                return [
                    'name' => $appType->name,
                    'title' => ucfirst($appType->name),
                    'fieldProps' => $appType->giiOptions()
                ];
            }, \Yii::$app->types->getTypes()),
        ]);
    }


}