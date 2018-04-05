<?php

namespace steroids\views;

use steroids\components\SiteMapItem;
use steroids\modules\gii\helpers\GiiHelper;

/* @var $this \yii\web\View */
/* @var $siteMapItem SiteMapItem */
/* @var $index int */
/* @var $level int */
/* @var $id string */
/* @var $active bool */

?>

<tr <?= $active ? 'class="info"' : '' ?>>
    <td><?= $index + 1 ?></td>
    <td>
        <code>
            <?= $id ?>
        </code>
    </td>
    <td>
        <code>
            <?= $siteMapItem->url ? str_replace('0 => ', '', GiiHelper::varExport($siteMapItem->normalizedUrl)) : '' ?>
        </code>
    </td>
    <td>
            <?= str_repeat('—', $level) ?>
        <?= $siteMapItem->label ?>
    </td>
    <td>
        <?= $siteMapItem->icon ?>
    </td>
    <td>
        <?php
        $access = [];
        foreach (array_merge((array) $siteMapItem->roles, (array) $siteMapItem->accessCheck) as $accessItem) {
            foreach ((array)$accessItem as $accessSubItem) {
                $access[] = is_callable($accessSubItem) ? 'func()' : (string) $accessSubItem;
            }
        }
        ?>
        <?= implode(', ', $access) ?>
    </td>
    <td class="<?= $siteMapItem->getVisible() ? 'text-success' : 'text-danger' ?>">
        <?= \Yii::$app->formatter->asBoolean($siteMapItem->visible) ?>
    </td>
    <td>
        <?= \Yii::$app->formatter->asInteger($siteMapItem->order) ?>
    </td>
    <td>
        <?= $siteMapItem->redirectToChild
            ? ($siteMapItem->redirectToChild === true ? 'child' : GiiHelper::varExport($siteMapItem->redirectToChild))
            : '' ?>
    </td>
</tr>