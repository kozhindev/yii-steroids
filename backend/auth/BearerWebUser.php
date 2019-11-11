<?php

namespace steroids\auth;

use Yii;
use steroids\auth\models\AuthLogin;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\IdentityInterface;

/**
 * @property-read bool $isGuest
 * @property-read int|null $id
 * @property-read UserInterface|null $identity
 * @property-read UserInterface|null $model
 * @property-read string|null $accessToken
 */
class BearerWebUser extends \yii\web\User
{
    public $defaultRole = 'user';

    /**
     * @var AuthLogin
     */
    private $_login = false;

    /**
     * @var UserInterface
     */
    private $_identity = false;

    /**
     * @var string
     */
    private $_accessToken = false;

    /**
     * @return bool
     */
    public function getIsGuest()
    {
        return !$this->getIdentity();
    }

    /**
     * @return AuthLogin|null
     */
    public function getLogin()
    {
        if ($this->_login === false) {
            $this->_login = AuthLogin::findByToken($this->accessToken);
        }
        return $this->_login;
    }

    /**
     * @inheritdoc
     */
    public function getIdentity($autoRenew = true)
    {
        if ($this->_identity !== false) {
            return $this->_identity;
        }
        return ArrayHelper::getValue($this->getLogin(), 'user');
    }

    public function setIdentity($value)
    {
        $this->_identity = $value;
    }

    /**
     * @return UserInterface|null
     */
    public function getModel()
    {
        return $this->getIdentity();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return ArrayHelper::getValue($this->getLogin(), 'userId');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return ArrayHelper::getValue($this->getModel(), 'name');
    }

    /**
     * @inheritdoc
     */
    public function login(IdentityInterface $identity, $duration = 0)
    {
        $this->_login = AuthLogin::create($identity, \Yii::$app->request);
        $this->_accessToken = false;
        $this->regenerateCsrfToken();
    }

    /**
     * @inheritdoc
     */
    public function switchIdentity($user, $duration = 0)
    {
        if ($user) {
            $this->login($user);
        } else {
            $this->logout();
        }
    }

    /**
     * @inheritdoc
     */
    public function logout($destroySession = true)
    {
        $login = $this->getLogin();
        if ($login) {
            $login->logout();
            $this->_login = false;
            $this->_accessToken = false;
            $this->regenerateCsrfToken();
        }
    }

    /**
     * @return string|null
     */
    public function getAccessToken()
    {
        if ($this->_accessToken === false) {
            if ($this->_login) {
                $this->_accessToken = $this->_login->accessToken;
            } else {
                $authHeader = \Yii::$app->request->headers->get('Authorization');
                $this->_accessToken = $authHeader && preg_match('/^Bearer\s+(.*)$/', $authHeader, $match)
                    ? trim($match[1])
                    : null;
            }
        }
        return $this->_accessToken;
    }

    /**
     * Regenerates CSRF token
     */
    protected function regenerateCsrfToken()
    {
        $request = Yii::$app->getRequest();
        if ($request->enableCsrfCookie) {
            $request->getCsrfToken(true);
        }
    }

    public function loginRequired($checkAjax = true, $checkAcceptHeader = true)
    {
        throw new ForbiddenHttpException(Yii::t('yii', 'Login Required'));
    }
}
