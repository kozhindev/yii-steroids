<?php

namespace steroids\auth;

use steroids\base\Model;
use yii\web\IdentityInterface;

interface UserInterface extends IdentityInterface
{
    /**
     * @param string $email
     * @return UserInterface|Model
     */
    public static function findByEmail($email);

    /**
     * @param string $password
     * @return bool
     */
    public function validatePassword($password);

    /**
     * @param string $templateName
     * @param array $params
     * @return void
     */
    public function sendNotify($templateName, $params = []);
}
