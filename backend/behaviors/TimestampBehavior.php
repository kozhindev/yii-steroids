<?php

namespace steroids\behaviors;

use steroids\base\Model;
use yii\helpers\ArrayHelper;

/**
 * @property-read Model $owner
 */
class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    public $createdAtAttribute = 'createTime';
    public $updatedAtAttribute = 'updateTime';

    public function evaluateAttributes($event)
    {
        if ($this->createdAtAttribute && !$this->owner->hasAttribute($this->createdAtAttribute)) {
            foreach ($this->attributes as &$attributes) {
                ArrayHelper::removeValue($attributes, $this->createdAtAttribute);
            }
        }
        if ($this->updatedAtAttribute && !$this->owner->hasAttribute($this->updatedAtAttribute)) {
            foreach ($this->attributes as &$attributes) {
                $attributes = (array) $attributes;
                ArrayHelper::removeValue($attributes, $this->updatedAtAttribute);
            }
        }

        parent::evaluateAttributes($event);
    }

    public function getValue($event)
    {
        return date('Y-m-d H:i:s');
    }

}
