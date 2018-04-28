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
    public function prepareFieldProps($modelClass, $attribute, &$props, &$import = null)
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
     * @inheritdoc
     */
    public function giiDbType($attributeEntity)
    {
        return Schema::TYPE_STRING . ($attributeEntity->stringLength ? '(' . $attributeEntity->stringLength . ')' : '');
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        $validators = [
            [$attributeEntity->name, 'string', 'max' => $attributeEntity->stringLength ?: 255],
        ];

        switch ($attributeEntity->stringType) {
            case self::TYPE_WORDS:
                $wordsValidatorClass = WordsValidator::className();
                $useClasses[] = $wordsValidatorClass;
                $validators[] = [$attributeEntity->name, new ValueExpression(StringHelper::basename($wordsValidatorClass) . '::className()')];
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
            [
                'attribute' => self::OPTION_TYPE,
                'component' => 'DropDownField',
                'label' => 'Type',
                'selectFirst' => true,
                'items' => [
                    [
                        'id' => self::TYPE_TEXT,
                        'label' => 'Text',
                    ],
                    [
                        'id' => self::TYPE_WORDS,
                        'label' => 'Words',
                    ],
                ],
            ],
            [
                'attribute' => self::OPTION_LENGTH,
                'component' => 'NumberField',
                'label' => 'Length',
            ],
        ];
    }
}