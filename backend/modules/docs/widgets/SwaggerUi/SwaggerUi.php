<?php

namespace steroids\modules\docs\widgets\SwaggerUi;

use steroids\base\Widget;
use yii\helpers\Url;

class SwaggerUi extends Widget
{
    public function run()
    {
        return $this->renderReact([
            'swaggerUrl' => Url::to(['/docs/docs/json']),
        ]);
    }
}