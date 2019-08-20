<?php

namespace steroids\modules\gii\models;

use yii\base\BaseObject;

class ValueExpression extends BaseObject
{
    public $expression;

    /**
     * @param string $expression
     * @param array $config
     */
    public function __construct($expression, $config = [])
    {
        $this->expression = $expression;
        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->expression;
    }
}
