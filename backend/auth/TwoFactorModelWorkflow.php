<?php

namespace steroids\auth;

use steroids\base\FormModel;
use steroids\base\Model;
use yii\web\IdentityInterface;
use yii\web\Session;

/**
 * @property-read Session $session
 */
class TwoFactorModelWorkflow extends BaseTwoFactorWorkflow
{
    /**
     * @var FormModel|Model
     */
    public $model;

    /**
     * @var array
     */
    public $data = [];

    /**
     * @var callable
     */
    public $onBeforeAuth;

    /**
     * @var callable
     */
    public $onAfterAuth;

    /**
     * @return \yii\web\Session
     */
    public function getSession()
    {
        return \Yii::$app->session;
    }

    public function run()
    {
        if ($this->session->has('2fa_isProcessStart')) {
            $provider = $this->getProvider($this->session->get('2fa_provider'));
            if ($provider->end()) {
                $this->model->load($this->session->get('2fa_data'));
                if ($this->model->validate()) {
                    return call_user_func($this->onAfterAuth);
                } else {
                    return [
                        'formErrors' => $this->model->errors,
                    ];
                }
            } else {
                return $provider->response;
            }
        } else {
            if ($this->model->load($this->data) && $this->model->validate()) {
                call_user_func($this->onBeforeAuth);

                foreach ($this->providers as $providerName) {
                    $provider = $this->getProvider($providerName);
                    if ($provider->enable) {
                        if ($this->model instanceof IdentityInterface && !$provider->isEnableFor($this->model)) {
                            continue;
                        }
                        $this->session->set('2fa_isProcessStart', true);
                        $this->session->set('2fa_provider', $providerName);
                        $this->session->set('2fa_data', $this->data);
                        return $provider->start();
                    }
                }

                return call_user_func($this->onAfterAuth);
            }
        }
    }
}
