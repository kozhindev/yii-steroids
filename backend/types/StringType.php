<?php

namespace steroids\types;

use steroids\base\Type;
use steroids\modules\gii\models\MetaItem;
use steroids\modules\gii\models\ValueExpression;
use steroids\validators\WordsValidator;
use yii\db\Schema;
use yii\helpers\StringHelper;

class StringType extends Type
{
    const OPTION_TYPE = 'stringType';
    const OPTION_LENGTH = 'stringLength';

    const TYPE_TEXT = 'text';
    const TYPE_WORDS = 'words';

    /**
     * @inheritdoc
     */
    public function prepareFieldProps($model, $attribute, &$props)
    {
        $props = array_merge(
            [
                'component' => 'InputField',
                'attribute' => $attribute,
            ],
            $props
        );
    }

    /**
     * @param MetaItem $metaItem
     * @return string|false
     */
    public function giiDbType($metaItem)
    {
        return Schema::TYPE_STRING . ($metaItem->stringLength ? '(' . $metaItem->stringLength . ')' : '');
    }

    /**
     * @inheritdoc
     */
    public function giiRules($metaItem, &$useClasses = [])
    {
        $validators = [
            [$metaItem->name, 'string', 'max' => $metaItem->stringLength ?: 255],
        ];

        switch ($metaItem->stringType) {
            case self::TYPE_WORDS:
                $wordsValidatorClass = WordsValidator::className();
                $useClasses[] = $wordsValidatorClass;
                $validators[] = [$metaItem->name, new ValueExpression(StringHelper::basename($wordsValidatorClass) . '::className()')];
                break;
        }

        return $validators;
    }

    /**
     * @inheritdoc
     */
    public function giiOptions()
    {
        return [
            self::OPTION_TYPE => [
                'component' => 'select',
                'label' => 'Type',
                'options' => [
                    self::TYPE_TEXT => 'Text',
                    self::TYPE_WORDS => 'Words',
                ],
            ],
            self::OPTION_LENGTH => [
                'component' => 'input',
                'type' => 'number',
                'label' => 'Length',
                'style' => [
                    'width' => '80px'
                ]
            ],
        ];
    }
}