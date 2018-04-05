<?php

namespace steroids\modules\gii\generators\enum;

use steroids\modules\gii\models\enumClass;
use yii\gii\CodeFile;
use yii\gii\Generator;

class EnumGenerator extends Generator
{
    /**
     * @var enumClass
     */
    public $enumClass;

    /**
     * @var string
     */
    public $template = 'default';

    public function getName()
    {
        return 'enum';
    }

    public function requiredTemplates()
    {
        return ['meta', 'enum'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        // Create/update meta information
        (new CodeFile(
            $this->enumClass->metaClass->filePath,
            $this->render('meta.php', [
                'enumClass' => $this->enumClass,
            ])
        ))->save();
        (new CodeFile(
            $this->enumClass->metaClass->jsFilePath,
            $this->render('meta_js.php', [
                'enumClass' => $this->enumClass,
            ])
        ))->save();
        \Yii::$app->session->addFlash('success', 'Мета информция enum ' . $this->enumClass->metaClass->name . ' обновлена');

        // Create enum, if not exists
        if (!file_exists($this->enumClass->filePath)) {
            (new CodeFile(
                $this->enumClass->filePath,
                $this->render('enum.php', [
                    'enumClass' => $this->enumClass,
                ])
            ))->save();
            \Yii::$app->session->addFlash('success', 'Добавлен enum ' . $this->enumClass->name);
        }
    }

}