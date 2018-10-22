<?php

namespace steroids\auth;

use steroids\base\FormModel;
use yii\web\IdentityInterface;

/**
 * @property-read array $data
 * @property-read array $response
 */
class BaseTwoFactorProvider extends FormModel
{
    /**
     * @var bool
     */
    public $enable = false;

    public function start()
    {

    }

    public function end()
    {
        $this->load($this->data);
        if ($this->validate()) {
            $this->endInternal();
        }
    }

    public function endInternal()
    {

    }

    /**
     * @param IdentityInterface $identity
     * @return bool
     */
    public function isEnableFor($identity)
    {
        return true;
    }

    /**
     * @return array
     */
    public function getResponse()
    {
        return [
            'providerErrors' => $this->errors,
        ];
    }

    /**
     * @return array
     */
    public function getData()
    {
        return array_merge(
            \Yii::$app->request->post(),
            \Yii::$app->request->get()
        );
    }
}
