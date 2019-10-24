<?php

namespace steroids\auth\models\meta;

use \Yii;
use steroids\base\Model;
use steroids\behaviors\TimestampBehavior;

/**
 * @property string $id
 * @property integer $userId
 * @property string $email
 * @property string $code
 * @property boolean $isConfirmed
 * @property string $createTime
 * @property string $updateTime
 * @property string $expireTime
 */
abstract class AuthConfirmMeta extends Model
{
    public static function tableName()
    {
        return 'auth_confirms';
    }

    public function fields()
    {
        return [
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['userId', 'integer'],
            [['email', 'code'], 'required'],
            ['email', 'string', 'max' => 255],
            ['email', 'email'],
            ['code', 'string', 'max' => '32'],
            ['isConfirmed', 'boolean'],
            ['expireTime', 'date', 'format' => 'php:Y-m-d H:i:s'],
        ]);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            TimestampBehavior::class,
        ]);
    }

    public static function meta()
    {
        return array_merge(parent::meta(), [
            'id' => [
                'label' => Yii::t('steroids', 'ID'),
                'appType' => 'primaryKey'
            ],
            'userId' => [
                'label' => Yii::t('steroids', 'Пользователь'),
                'appType' => 'integer',
                'isRequired' => true
            ],
            'email' => [
                'label' => Yii::t('steroids', 'Email'),
                'appType' => 'email',
            ],
            'code' => [
                'label' => Yii::t('steroids', 'Код'),
                'isRequired' => true,
                'stringLength' => '32'
            ],
            'isConfirmed' => [
                'label' => Yii::t('steroids', 'Подтвержден?'),
                'appType' => 'boolean'
            ],
            'createTime' => [
                'label' => Yii::t('steroids', 'Добавлен'),
                'appType' => 'autoTime'
            ],
            'updateTime' => [
                'label' => Yii::t('steroids', 'Обновлен'),
                'appType' => 'autoTime',
                'touchOnUpdate' => true
            ],
            'expireTime' => [
                'label' => Yii::t('steroids', 'Дата действия кода'),
                'appType' => 'dateTime'
            ]
        ]);
    }
}
