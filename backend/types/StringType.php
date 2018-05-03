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
        $length = $attributeEntity->getCustomProperty(self::OPTION_LENGTH);
        return Schema::TYPE_STRING . ($length ? '(' . $length . ')' : '');
    }

    /**
     * @inheritdoc
     */
    public function giiRules($attributeEntity, &$useClasses = [])
    {
        $length = $attributeEntity->getCustomProperty(self::OPTION_LENGTH);
        $validators = [
            [$attributeEntity->name, 'string', 'max' => $length ?: 255],
        ];

        switch ($attributeEntity->getCustomProperty(self::OPTION_TYPE)) {
            case self::TYPE_WORDS:
                $wordsValidatorClass = WordsValidator::class;
                $useClasses[] = $wordsValidatorClass;
                $validators[] = [$attributeEntity->name, new ValueExpression(StringHelper::basename($wordsValidatorClass) . '::class')];
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