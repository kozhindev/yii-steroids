<?php

namespace steroids\auth;

use steroids\base\FormModel;
use steroids\base\Model;
use yii\web\IdentityInterface;
use yii\web\Request;
use yii\web\Session;
use Yii;

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
     * @var Request
     */
    public $request;

    /**
     * @var callable
     */
    public $onBeforeAuth;

    /**
     * @var callable
     */
    public $onAfterAuth;

    /**
     * @var callable
     */
    public $showForm;

    /**
     * @return \yii\web\Session
     */
    public function getSession()
    {
        return \Yii::$app->session;
    }

    public function run($config)
    {
        // @todo ??
        Yii::configure($this, $config);

        $postData = $this->request->post();
        $provider = $this->getProviderForModel($this->model);

        $isModelValidated = $this->model->load($postData) && $this->model->validate();

        if ($isModelValidated) {
            $beforeAuthResult = call_user_func($this->onBeforeAuth, $this);

            if ($provider) {
                Yii::configure($provider, $beforeAuthResult);
                $provider->start();
            }
            return call_user_func($this->onAfterAuth);
        } else {
            return call_user_func($this->showForm, $this->model);
        }
    }

//    public function run($config)
//    {
//        // @todo ??
//        Yii::configure($this, $config);
//
//        if ($this->session->has('2fa_isProcessStart')) {
//            $provider = $this->getProvider($this->session->get('2fa_provider'));
//            if ($provider->end()) {
//                $this->model->load($this->session->get('2fa_data'));
//                if ($this->model->validate()) {
//                    return call_user_func($this->onAfterAuth);
//                } else {
//                    return [
//                        'formErrors' => $this->model->errors,
//                    ];
//                }
//            } else {
//                return $provider->response;
//            }
//        } else {
//            if ($this->model->load($this->data) && $this->model->validate()) {
//                $beforeAuthResult = call_user_func($this->onBeforeAuth, $this);
//
//                foreach ($this->providers as $providerName) {
//                    $provider = $this->getProvider($providerName);
//
//                    Yii::configure($provider, $beforeAuthResult);
//
//                    if ($provider && $provider->enable) {
//                        if ($this->model instanceof IdentityInterface && !$provider->isEnableFor($this->model)) {
//                            continue;
//                        }
//                        $this->session->set('2fa_isProcessStart', true);
//                        $this->session->set('2fa_provider', $providerName);
//                        $this->session->set('2fa_data', $this->data);
//                        $providerResult = $provider->start();
//
//                        if (!empty($providerResult['continueExecution'])) {
//                            break;
//                        } else {
//                            return $providerResult;
//                        }
//                    }
//                }
//
//                return call_user_func($this->onAfterAuth);
//            } else {
//                return call_user_func($this->showForm, $this->model);
//            }
//        }
//    }
}
