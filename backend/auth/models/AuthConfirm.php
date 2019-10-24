<?php

namespace steroids\auth\models;

use steroids\auth\models\meta\AuthConfirmMeta;
use steroids\auth\UserInterface;
use steroids\base\Model;
use \steroids\exceptions\ModelSaveException;
use yii\db\ActiveQuery;

/**
 * @property-read UserInterface|Model $user
 */
class AuthConfirm extends AuthConfirmMeta
{
    const CODE_LENGTH = 4;
    const EXPIRE_AFTER_MINUTES = 60;
    const TEMPLATE_NAME = 'authConfirm';

    /**
     * @param int|null $length
     * @return int
     * @throws \Exception
     */
    public static function generateCode($length = null)
    {
        $length = $length ?: static::CODE_LENGTH;
        $length = max(1, $length);
        $number = random_int(pow(10, $length - 1), pow(10, $length) - 1);
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $email
     * @return static
     * @throws \Exception
     */
    public static function create($email)
    {
        /** @var UserInterface|Model $userClass */
        $userClass = \Yii::$app->user->identityClass;

        /** @var UserInterface|Model $user */
        $user = $userClass::findByEmail($email) ?: new $userClass(['email' => $email]);

        $model = new static([
            'userId' => $user->id,
            'email' => $user->email,
            'code' => static::generateCode(),
            'isConfirmed' => false,
            'expireTime' => date('Y-m-d H:i:s', strtotime('+' . static::EXPIRE_AFTER_MINUTES . ' minutes')),
        ]);
        $model->saveOrPanic();

        // Send mail
        $user->sendNotify(static::TEMPLATE_NAME, [
            'confirm' => $model,
        ]);

        return $model;
    }

    /**
     * @param string $email
     * @param string $code
     * @return static
     * @throws ModelSaveException
     */
    public static function findByCode($email, $code)
    {
        return static::find()
            ->where([
                'LOWER(email)' => mb_strtolower(trim($email)),
                'code' => $code,
            ])
            ->andWhere(['>=', 'expireTime', date('Y-m-d H:i:s')])
            ->limit(1)
            ->one() ?: null;
    }

    public function markConfirmed()
    {
        $this->isConfirmed = true;
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
