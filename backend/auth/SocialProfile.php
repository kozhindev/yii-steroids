<?php

namespace steroids\auth;

use yii\base\Arrayable;
use yii\base\ArrayableTrait;
use yii\base\BaseObject;

class SocialProfile extends BaseObject implements Arrayable
{
    use ArrayableTrait;

    /**
     * @var string|int
     */
    public $id;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $avatarUrl;

    public function fields()
    {
        return [
            'id',
            'name',
            'avatarUrl',
        ];
    }
}
