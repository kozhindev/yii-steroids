<?php

namespace steroids\auth\models;

use steroids\auth\SocialProfile;
use steroids\auth\models\meta\AuthSocialMeta;
use steroids\auth\UserInterface;
use steroids\base\Model;
use steroids\behaviors\UidBehavior;
use yii\db\ActiveQuery;
use yii\helpers\Json;
use steroids\exceptions\ModelSaveException;

/**
 * @property-read SocialProfile $profile
 * @property-read bool $isEmailNeed
 * @property-read UserInterface|Model $user
 */
class AuthSocial extends AuthSocialMeta
{
    public static function findOrCreate($name, SocialProfile $profile)
    {
        $params = [
            'socialName' => $name,
            'externalId' => $profile->id,
        ];
        $model = static::findOne($params) ?: new static($params);
        $model->profileJson = Json::encode($profile);
        $model->saveOrPanic();

        if ($profile->email) {
            $model->appendUser($profile->email);
        }

        return $model;
    }

    public function fields()
    {
        return array_merge(parent::fields(), [
            'profile',
        ]);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            UidBehavior::class,
        ]);
    }

    /**
     * @return bool
     */
    public function getIsEmailNeed()
    {
        return !$this->userId;
    }

    public function getProfile()
    {
        return $this->profileJson
            ? new SocialProfile(Json::decode($this->profileJson))
            : null;
    }

    /**
     * @param string $email
     * @throws ModelSaveException
     */
    public function appendUser($email)
    {
        /** @var UserInterface|Model $userClass */
        $userClass = \Yii::$app->user->identityClass;

        $user = $userClass::findByEmail($email);
        if (!$user) {
            /** @var UserInterface|Model $user */
            $user = new $userClass();
            $user->attributes = [
                'role' => \Yii::$app->user->defaultRole,
                'email' => $email,
                'username' => $this->profile->name,
            ];
            $user->saveOrPanic();
        }

        $this->userId = $user->primaryKey;
        $this->populateRelation('user', $user);
        $this->saveOrPanic();
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        /** @var UserInterface|Model $userClass */
        $userClass = \Yii::$app->user->identityClass;

        return $this->hasOne($userClass, ['id' => 'userId']);
    }
}
