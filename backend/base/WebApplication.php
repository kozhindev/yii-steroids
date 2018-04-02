<?php

namespace steroids\base;

use Yii;
use steroids\components\AuthManager;
use steroids\components\FrontendState;
use steroids\components\SiteMap;
use yii\web\Application;

/**
 * @property-read AuthManager $authManager
 * @property-read FrontendState $frontendState
 * @property-read SiteMap $siteMap
 */
class WebApplication extends Application
{
    /**
     * @inheritdoc
     */
    protected function bootstrap()
    {
        $versionFilePath = STEROIDS_ROOT_DIR . '/version.txt';
        if (file_exists($versionFilePath)) {
            $this->version = trim(file_get_contents($versionFilePath));
        }

        Yii::setAlias('@static', $this->getRequest()->getBaseUrl() . '/static/' . $this->version);

        parent::bootstrap();
    }
}
