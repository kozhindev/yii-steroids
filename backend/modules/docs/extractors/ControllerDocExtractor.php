<?php

namespace steroids\modules\docs\extractors;

use steroids\modules\docs\helpers\ExtractorHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\web\Controller;

/**
 * Class ControllerDocExtractor
 * @property-read string|null $actionId
 * @property-read string|null $controllerId
 * @property-read string|null $moduleId
 */
class ControllerDocExtractor extends BaseDocExtractor
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $route;

    /**
     * @var string
     */
    public $url;

    /**
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function run()
    {
        $controller = \Yii::$app->createController($this->route)[0];
        $method = $this->findActionMethodInfo($controller, $this->actionId);
        if (!$method) {
            return null;
        }

        $url = $this->url;
        $httpMethod = 'get';
        if (preg_match('/^([A-Z,]+) .+/', $url, $match)) {
            $url = substr($url, strlen($match[1]) + 1);
            $httpMethod = strtolower(explode(',', $match[1])[0]);
        }
        $url = preg_replace('/^\/?api\/[^\\/]+\//', '', $url);
        $url = preg_replace('/<([^>:]+)(:[^>]+)?>/', '{$1}', $url);
        $this->swaggerJson->addPath($url, $httpMethod, [
            'summary' => $this->title ?: $url,
            'tags' => [
                $this->moduleId,
            ],
            'consumes' => [
                'application/json'
            ],
            'responses' => [
                200 => [
                    'description' => 'Successful operation',
                ],
            ],
        ]);

        if (preg_match('/@return ([a-z0-9_]+)/i', $method->getDocComment(), $match)) {
            $type = ExtractorHelper::resolveType($match[1], get_class($controller));
            $extractor = $this->createTypeExtractor($type, $url, $httpMethod);
            if ($extractor) {
                if (preg_match_all('/@request-listen-relation\s+([^\s]+)/i', $method->getDocComment(), $match)) {
                    $extractor->listenRelations = $match[1];
                }
                $extractor->run();
            }

            // Find first comment line as title
            if (!$this->title) {
                foreach (explode("\n", $method->getDocComment()) as $line) {
                    $line = preg_replace('/^\s*\\/?\*+/', '', $line);
                    $line = trim($line);
                    if ($line && $line !== '/' && substr($line, 0, 1) !== '@') {
                        $this->title = $line;
                        $this->swaggerJson->updatePath($url, $httpMethod, [
                            'summary' => '/' . $url . ' ' . $this->title,
                        ]);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @return string|null
     */
    public function getActionId()
    {
        $parts = explode('/', $this->route);
        return ArrayHelper::getValue($parts, count($parts) - 1);
    }

    /**
     * @return string|null
     */
    public function getControllerId()
    {
        $parts = explode('/', $this->route);
        return ArrayHelper::getValue($parts, count($parts) - 2);
    }

    /**
     * @return string|null
     */
    public function getModuleId()
    {
        $parts = explode('/', $this->route);
        return ArrayHelper::getValue($parts, count($parts) - 3);
    }

    /**
     * @param Controller $controller
     * @param string $actionId
     * @return \ReflectionMethod|null
     * @throws \ReflectionException
     */
    protected function findActionMethodInfo($controller, $actionId)
    {
        $actionMethodName = 'action' . Inflector::id2camel($actionId);
        $controllerInfo = new \ReflectionClass(get_class($controller));

        foreach ($controllerInfo->getMethods() as $method) {
            if ($method->name === $actionMethodName) {
                return $method;
            }
        }
        return null;
    }

}
