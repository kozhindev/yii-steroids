<?php

namespace app\views;

use steroids\modules\gii\forms\FormEntity;

/* @var $formEntity FormEntity */

$import = [];
$fields = $formEntity->renderJsFields('        ', $import);

?>
import Model from 'yii-steroids/frontend/base/Model';
<?= !empty($import) ? "\n" . implode("\n", array_unique($import)) . "\n" : '' ?>

export default class <?= $formEntity->name ?>Meta extends Model {

    static className = '<?= str_replace('\\', '\\\\', $formEntity->getClassName()) ?>';

    static fields() {
        return <?= $fields ?>;
    }

}
