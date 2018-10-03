<?php

namespace app\views;

use Yii;
use yii\helpers\Html;
use yii\web\View;

/* @var $this \yii\web\View */
/* @var $content string */

Yii::$app->frontendState->register($this);
$this->registerJsFile('@static/assets/bundle-common.js', ['position' => View::POS_BEGIN]);
$this->registerJsFile('@static/assets/bundle-index.js', ['position' => View::POS_BEGIN]);


?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= \Yii::$app->language ?>">
<head>
    <meta charset="<?= \Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode(Yii::$app->siteMap->getFullTitle()) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>