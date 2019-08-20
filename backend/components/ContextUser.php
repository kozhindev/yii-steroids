<?php

namespace steroids\components;

use yii\web\User;

/**
 * @property-read \app\user\models\User|\steroids\modules\user\models\User $model
 * @property-read string $id
 * @property-read string $name
 * @package app\core\components
 */
class ContextUser extends User
{
    /**
     * @return \app\user\models\User|\steroids\modules\user\models\User
     */
    public function getModel()
    {
        return $this->identity;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->getModel() ? $this->getModel()->id : null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getModel() ? $this->getModel()->name : '';
    }

}
