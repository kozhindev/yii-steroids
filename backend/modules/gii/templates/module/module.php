<?php

namespace app\views;

use steroids\modules\gii\generators\module\ModuleGenerator;
use yii\web\View;

/* @var $this View */
/* @var $generator ModuleGenerator */
/* @var $namespace string */
/* @var $className string */

echo "<?php\n";
?>

namespace <?= $namespace ?>;

use app\core\base\AppModule;

class <?= $className ?> extends AppModule
{
}
