<?php

namespace steroids\modules\user\models\meta;

use steroids\base\Model;
use steroids\behaviors\TimestampBehavior;
use \Yii;
use app\cruises\enums\CabinBedConfiguration;

/**
 * @property string $id
 * @property string $login
 * @property string $email
 * @property string $phone
 * @property string $role
 * @property string $passwordHash
 * @property string $sessionKey
 * @property string $language
 * @property string $lastLoginIp
 * @property string $emailConfirmKey
 * @property string $createTime
 * @property string $updateTime
 * @property string $emailConfirmTime
 * @property string $blockedTime
 * @property string $lastLoginTime
 * @property string $name
 */
abstract class UserMeta extends Model
{
    public static function tableName()
    {
        return 'users';
    }

    public function fields()
    {
        return [
        ];
    }

    public function rules()
    {
        return [
            [['login', 'email', 'role', 'name'], 'string', 'max' => 255],
            ['email', 'email'],
            ['email', 'required'],
            ['phone', 'string', 'max' => 32],
            ['passwordHash', 'string'],
            [['sessionKey', 'emailConfirmKey'], 'string', 'max' => '32'],
            ['language', 'string', 'max' => '10'],
            ['lastLoginIp', 'string', 'max' => '45'],
            [['emailConfirmTime', 'blockedTime', 'lastLoginTime'], 'date', 'format' => 'php:Y-m-d H:i'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function meta()
    {
        return [
            'id' => [
                'label' => Yii::t('app', 'ИД'),
                'appType' => 'primaryKey'
            ],
            'login' => [
                'label' => Yii::t('app', 'Логин')
            ],
            'email' => [
                'label' => Yii::t('app', 'Email'),
                'appType' => 'email',
                'isRequired' => true
            ],
            'phone' => [
                'label' => Yii::t('app', 'Телефон'),
                'appType' => 'phone'
            ],
            'role' => [
                'label' => Yii::t('app', 'Роль'),
                'enumClassName' => CabinBedConfiguration::class
            ],
            'passwordHash' => [
                'label' => Yii::t('app', 'Пароль'),
                'appType' => 'text'
            ],
            'sessionKey' => [
                'label' => Yii::t('app', 'Ключ сессии'),
                'stringLength' => '32'
            ],
            'language' => [
                'label' => Yii::t('app', 'Язык'),
                'stringLength' => '10'
            ],
            'lastLoginIp' => [
                'label' => Yii::t('app', 'IP последнего входа'),
                'stringLength' => '45'
            ],
            'emailConfirmKey' => [
                'label' => Yii::t('app', 'Ключ подтверждения почты'),
                'stringLength' => '32'
            ],
            'createTime' => [
                'label' => Yii::t('app', 'Дата регистрации'),
                'appType' => 'autoTime'
            ],
            'updateTime' => [
                'label' => Yii::t('app', 'Дата обновления'),
                'appType' => 'autoTime',
                'touchOnUpdate' => true
            ],
            'emailConfirmTime' => [
                'label' => Yii::t('app', 'Дата подтверждения почты'),
                'appType' => 'dateTime'
            ],
            'blockedTime' => [
                'label' => Yii::t('app', 'Дата блокировки'),
                'appType' => 'dateTime'
            ],
            'lastLoginTime' => [
                'label' => Yii::t('app', 'Дата последнего входа'),
                'appType' => 'dateTime'
            ],
            'name' => [
                'label' => Yii::t('app', 'Имя')
            ]
        ];
    }
}
