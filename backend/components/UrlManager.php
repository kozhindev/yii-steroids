<?php

namespace steroids\components;

use steroids\base\WebApplication;
use yii\base\BootstrapInterface;
use yii\web\UrlNormalizer;

class UrlManager extends \codemix\localeurls\UrlManager implements BootstrapInterface
{
    public $showScriptName = false;
    public $enablePrettyUrl = true;
    public $enableDefaultLanguageUrlCode = true;

    public function init()
    {
        $this->enableLocaleUrls = count($this->languages) > 0;
        $this->normalizer = array_merge(
            [
                'class' => UrlNormalizer::class,
                'collapseSlashes' => true,
                'normalizeTrailingSlash' => true,
            ],
            $this->normalizer ?: []
        );
        $this->ignoreLanguageUrlPatterns = array_merge(
            [
                '#^api/#' => '#^api/#',
                '#^debug/#' => '#^debug/#',
            ],
            $this->ignoreLanguageUrlPatterns ?: []
        );

        parent::init();
    }

    public function bootstrap($app)
    {
        if ($this->enableLocaleUrls && $app instanceof WebApplication) {
            // Set Application language before request
            $this->parseRequest(\Yii::$app->request);

            // Fix language for api
            $language = \Yii::$app->session->get($this->languageSessionKey);
            if ($language) {
                \Yii::$app->language = $language;
            }
        }
    }
}