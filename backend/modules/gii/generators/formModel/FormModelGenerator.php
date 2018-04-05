<?php

namespace steroids\modules\gii\generators\formModel;

use steroids\modules\gii\models\FormModelClass;
use steroids\modules\gii\models\ModelClass;
use yii\gii\CodeFile;
use yii\gii\Generator;

class FormModelGenerator extends Generator
{
    /**
     * @var FormModelClass
     */
    public $formModelClass;

    /**
     * @var ModelClass|null
     */
    public $modelClass;

    /**
     * @var string
     */
    public $template = 'default';

    public function getName()
    {
        return 'form-model';
    }

    public function requiredTemplates()
    {
        return ['meta', 'meta_js', 'model'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        // Create/update meta information
        (new CodeFile(
            $this->formModelClass->metaClass->filePath,
            $this->render('meta.php', [
                'modelClass' => $this->modelClass,
                'formModelClass' => $this->formModelClass,
            ])
        ))->save();
        (new CodeFile(
            $this->formModelClass->metaClass->jsFilePath,
            $this->render('meta_js.php', [
                'modelClass' => $this->modelClass,
                'formModelClass' => $this->formModelClass,
            ])
        ))->save();
        \Yii::$app->session->addFlash('success', 'Мета информция модели формы ' . $this->formModelClass->metaClass->name . ' обновлена');

        // Create model, if not exists
        if (!file_exists($this->formModelClass->filePath)) {
            (new CodeFile(
                $this->formModelClass->filePath,
                $this->render('model.php', [
                    'modelClass' => $this->modelClass,
                    'formModelClass' => $this->formModelClass,
                ])
            ))->save();
            \Yii::$app->session->addFlash('success', 'Добавлена модель формы ' . $this->formModelClass->name);
        }
    }

}