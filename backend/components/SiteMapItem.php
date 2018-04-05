<?php

namespace steroids\components;

use Yii;
use yii\base\BaseObject;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\UrlRule;

/**
 * @package steroids\components
 * @property callable|callable[]|null $accessCheck
 * @property bool $active
 * @property-read string $modelLabel
 * @property-read string|array $normalizedUrl
 * @property-read array $pathIds
 */
class SiteMapItem extends BaseObject
{
    /**
     * @var int|string
     */
    public $id;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string|array
     */
    public $url;

    /**
     * Value format is identical to item from \yii\web\UrlManager::rules
     * @var string|array|UrlRule
     */
    public $urlRule;

    /**
     * @var bool
     */
    private $_visible = true;

    /**
     * @var bool
     */
    public $encode;

    /**
     * @var float
     */
    public $order = 0;

    /**
     * @var SiteMapItem[]
     */
    public $items = [];

    /**
     * @var array
     */
    public $options = [];

    /**
     * @var array
     */
    public $linkOptions = [];

    /**
     * @var SiteMap
     */
    public $owner;

    /**
     * @var SiteMapItem
     */
    public $parent;

    /**
     * @var bool|string|int
     */
    public $redirectToChild = false;

    /**
     * @var bool
     */
    public $_active;

    /**
     * @var callable|callable[]
     */
    private $_accessCheck;

    /**
     * @var string
     */
    public $icon;

    /**
     * @var int|string
     */
    public $badge;

    /**
     * @var string
     */
    public $modelClass;

    private $_modelLabel;

    /**
     * @return callable|callable[]|null
     */
    public function getAccessCheck()
    {
        if ($this->_accessCheck === null && $this->parent) {
            return $this->parent->getAccessCheck();
        }
        return $this->_accessCheck;
    }

    /**
     * @param callable|callable[]|null $value
     */
    public function setAccessCheck($value)
    {
        $this->_accessCheck = $value;
    }

    /**
     * @return bool
     */
    public function getActive()
    {
        if ($this->_active === null) {
            $this->_active = false;

            if ($this->normalizedUrl && $this->owner->isUrlEquals($this->normalizedUrl, $this->owner->getRequestedRoute())) {
                $this->_active = true;
            } else {
                foreach ($this->items as $itemModel) {
                    if ($itemModel->active) {
                        $this->_active = true;
                        break;
                    }
                }
            }
        }
        return $this->_active;
    }

    /**
     * @param bool $value
     */
    public function setActive($value)
    {
        $this->_active = (bool)$value;
    }

    /**
     * @return bool
     */
    public function getVisible()
    {
        return $this->_visible && $this->checkVisible($this->normalizedUrl);
    }

    /**
     * @param bool
     */
    public function setVisible($value)
    {
        $this->_visible = $value;
    }

    /**
     * @return array
     */
    public function getPathIds()
    {
        return array_merge(ArrayHelper::getValue($this->parent, 'pathIds', []), [$this->id]);
    }

    /**
     * @param array $url
     * @return bool|mixed
     */
    public function checkVisible($url)
    {
        if (is_callable($this->accessCheck)) {
            return call_user_func($this->accessCheck, $url);
        }

        if (Yii::$app && Yii::$app->has('authManager') && Yii::$app->authManager instanceof AuthManager) {
            return Yii::$app->authManager->checkMenuAccess(Yii::$app->user->identity, $this);
        }

        return false;
    }

    public function getNormalizedUrl()
    {
        if (is_array($this->url)) {
            $url = [$this->url[0]];

            foreach ($this->url as $key => $value) {
                if (strpos($value, ':') !== false) {
                    list($getter, $name) = explode(':', $value);

                    if (is_int($key) && $key > 0) {
                        $key = $name;
                    }

                    switch ($getter) {
                        case 'user':
                            $url[$key] = SiteMap::paramUser($name);
                            break;

                        case '':
                        case 'get':
                            $url[$key] = SiteMap::paramGet($name);
                            break;
                    }
                } elseif ($value !== null) {
                    $url[$key] = $value;
                }
            }

            // Append keys from url rule
            if (is_string($this->urlRule)) {
                preg_match_all('/<([^:>]+)[:>]/', $this->urlRule, $matches);
                foreach ($matches[1] as $key) {
                    if (!isset($url[$key])) {
                        $url[$key] = SiteMap::paramGet($key);
                    }
                }
            }

            return $url;
        }
        return $this->url;
    }

    public function getModelLabel() {
        if ($this->_modelLabel === null) {
            $this->_modelLabel = $this->label;

            /** @var \steroids\base\Model $modelClass */
            $modelClass = $this->modelClass;
            $coreModelClassName = '\steroids\base\Model';
            if ($modelClass && class_exists($coreModelClassName) && is_subclass_of($modelClass, $coreModelClassName)) {
                $pkParam = $modelClass::getRequestParamName();
                $primaryKey = SiteMap::paramGet($pkParam);
                if ($primaryKey) {
                    $model = $modelClass::findOne($primaryKey);
                    if ($model) {
                        $this->_modelLabel = $model->getModelLabel();
                    }
                }
            }
        }
        return $this->_modelLabel;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'label' => $this->modelLabel,
            'url' => $this->getNormalizedUrl(),
            'visible' => $this->getVisible(),
            'encode' => $this->encode,
            'active' => $this->active,
            'icon' => $this->icon,
            'badge' => $this->badge,
            'items' => $this->items,
            'options' => $this->options,
            'linkOptions' => $this->linkOptions,
        ];
    }
}
