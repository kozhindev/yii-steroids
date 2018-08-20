<?php

namespace steroids\components;

use yii\web\Application;
use yii\base\BootstrapInterface;
use yii\web\UrlNormalizer;
use yii\web\UrlNormalizerRedirectException;

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
                '#^api#' => '#^api#',
                '#^debug/#' => '#^debug/#',
                '#^gii#' => '#^gii#',
            ],
            $this->ignoreLanguageUrlPatterns ?: []
        );

        parent::init();
    }

    public function bootstrap($app)
    {
        if ($this->enableLocaleUrls && $app instanceof Application && !YII_ENV_TEST) {
            // Set Application language before request

            $request = \Yii::$app->request;

            try {
                $this->parseRequest($request);
            }
            // Suppress the UrlNormalizerRedirectException when parsing service (ignoreLanguageUrlPatterns) urls
            catch (UrlNormalizerRedirectException $e) {
                $pathInfo = $request->getPathInfo();
                $isInIgnoredPatterns = false;

                foreach ($this->ignoreLanguageUrlPatterns as $k => $pattern) {
                    if (preg_match($pattern, $pathInfo)) {
                        $isInIgnoredPatterns = true;
                        break;
                    }
                }

                if (!$isInIgnoredPatterns) {
                    throw $e;
                }
            }

            // Fix language for api
            $language = \Yii::$app->session->get($this->languageSessionKey);
            if ($language) {
                \Yii::$app->language = $language;
            }
        }
    }
}