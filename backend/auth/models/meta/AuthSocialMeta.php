<?php

namespace steroids\auth\models\meta;

use steroids\base\Model;
use steroids\auth\enums\SocialEnum;
use steroids\behaviors\TimestampBehavior;
use \Yii;

/**
 * @property string $id
 * @property integer $userId
 * @property string $externalId
 * @property string $socialName
 * @property string $createTime
 * @property string $updateTime
 * @property string $uid
 * @property string $profileJson
 */
abstract class AuthSocialMeta extends Model
{
    public static function tableName()
    {
        return 'auth_socials';
    }

    public function fields()
    {
        return [
            'externalId',
            'socialName',
        ];
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['userId', 'integer'],
            [['externalId', 'socialName'], 'required'],
            [['externalId', 'socialName'], 'string', 'max' => 255],
            ['uid', 'string', 'max' => '36'],
            ['profileJson', 'string'],
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
            'externalId' => [
                'label' => Yii::t('steroids', 'Внешний ИД'),
                'isRequired' => true
            ],
            'socialName' => [
                'appType' => 'enum',
                'isRequired' => true,
                'enumClassName' => SocialEnum::class
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
            'uid' => [
                'label' => Yii::t('steroids', 'Uid'),
                'stringLength' => '36'
            ],
            'profileJson' => [
                'appType' => 'text'
            ]
        ]);
    }
}
