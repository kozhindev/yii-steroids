<?php

namespace steroids\auth;

/**
 * @property int $id
 * @property string $passwordHash
 */
trait UserTrait
{
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findByEmail($email)
    {
        return static::find()->where(['email' => mb_strtolower(trim($email))])->limit(1)->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return false;
    }

    /**
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password)
    {
        return \Yii::$app->security->validatePassword($password, $this->passwordHash);
    }
}
