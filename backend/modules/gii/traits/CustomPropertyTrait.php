<?php

namespace steroids\modules\gii\traits;

/**
 * @property-read array $customProperties
 */
trait CustomPropertyTrait
{
    private $_customProperties = [];

    public function getCustomProperties()
    {
        return $this->_customProperties;
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->_customProperties)) {
            return $this->_customProperties[$name];
        } else {
            return parent::__get($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            parent::__set($name, $value);
        } else {
            $this->_customProperties[$name] = $value;
        }
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->_customProperties)) {
            return isset($this->_customProperties[$name]);
        } else {
            return parent::__isset($name);
        }
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->_customProperties)) {
            unset($this->_customProperties[$name]);
        } else {
            parent::__unset($name);
        }
    }
}