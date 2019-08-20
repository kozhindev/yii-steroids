<?php

namespace steroids\modules\gii\widgets\GiiApplication;

use yii\web\AssetBundle;

/**
 * Debugger asset bundle
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class GiiAsset extends AssetBundle
{
    /**
     * {@inheritdoc}
     */
    public $sourcePath = '@steroids/modules/gii/widgets/GiiApplication/assets';
    /**
     * {@inheritdoc}
     */
    public $css = [
        'bundle-GiiApplication.css',
        'bundle-index.css',
    ];
    /**
     * {@inheritdoc}
     */
    public $js = [
        'bundle-common.js',
        'bundle-index.js',
        'bundle-GiiApplication.js',
    ];
}
