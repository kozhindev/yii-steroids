<?php

namespace steroids\views;

use steroids\components\SiteMapItem;
use yii\web\View;

/* @var $this View */
/* @var $items SiteMapItem[] */
/* @var $testItem SiteMapItem */
/* @var $testUrl string */

$testItemData = null;

/**
 * @param SiteMapItem[] $items
 * @param View $view
 * @param string[] $parentIds
 * @param int $index
 * @param int $level
 * @return string
 */
$renderTree = null;
$renderTree = function ($items, $view, $parentIds = [], &$index = 0, $level = 0) use (&$renderTree, $testItem, &$testItemData)
{
    $html = '';
    foreach ($items as $id => $siteMapItem) {
        $ids = array_merge($parentIds, [$id]);
        $viewData = [
            'siteMapItem' => $siteMapItem,
            'index' => $index++,
            'level' => $level,
            'id' => implode('.', $ids),
            'active' => $siteMapItem === $testItem,
        ];

        if ($siteMapItem === $testItem) {
            $testItemData = $viewData;
        }
        $html .= $view->render('_item', $viewData);
        $html .= $renderTree($siteMapItem->items, $view, $ids, $index, $level + 1);
    }
    return $html;
};

$tree = $renderTree($items, $this);

?>

<form class="form-inline" method="get">
    <div class="form-group">
        <input type="text" name="url" class="form-control" placeholder="Test url" value="<?= $testUrl ?>">
    </div>
    <button type="submit" class="btn btn-default">Test</button>
</form>


<table class="table table-striped table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>Id</th>
        <th>Url</th>
        <th>Label</th>
        <th>Icon</th>
        <th>Roles</th>
        <th>Visible</th>
        <th>Order</th>
        <th>Redirect to</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($testItem) { ?>
        <tr>
            <td colspan="9" class="info">
                Test result:
            </td>
        </tr>
        <?= $this->render('_item', $testItemData); ?>
        <tr>
            <td colspan="9" class="info" style="height: 15px;">

            </td>
        </tr>
    <?php } ?>
    <?= $tree ?>
    </tbody>
</table>
