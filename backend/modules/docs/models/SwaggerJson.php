<?php

namespace steroids\modules\docs\models;

use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class SwaggerJson extends BaseObject
{
    public $version = '1.0.0';
    public $siteName;
    public $hostName;
    public $adminEmail;

    protected $tags = [];
    protected $paths = [];
    protected $definitions;

    public function addMethod()
    {

    }

    /**
     * @param string $url
     * @param array $params
     * @throws InvalidConfigException
     */
    public function addPath($url, $params)
    {
        // Normalize
        $url = '/' . ltrim($url, '/');

        // Auto add tags
        $tags = ArrayHelper::getValue($params, 'tags', []);
        foreach ($tags as $tag) {
            $this->addTag($tag);
        }

        // Add
        $this->paths[$url] = $params;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $params
     */
    public function updatePath($url, $method, array $params)
    {
        // Normalize
        $url = '/' . ltrim($url, '/');

        // Update
        $this->paths[$url][$method] = array_merge($this->paths[$url][$method], $params);
    }

    /**
     * @param string|array $tag
     * @throws InvalidConfigException
     */
    public function addTag($tag)
    {
        // Normalize
        if (is_string($tag)) {
            $tag = [
                'name' => $tag,
            ];
        }

        if (!isset($tag['name'])) {
            throw new InvalidConfigException('Name is required for tag.');
        }

        // Check exists
        $tagIds = ArrayHelper::getColumn($this->tags, 'name');
        if (in_array($tag['name'], $tagIds)) {
            return;
        }

        // Add
        $this->tags[] = $tag;
    }

    /**
     * @param string $name
     * @param array $params
     */
    public function addDefinition($name, $params)
    {
        $this->definitions[$name] = $params;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'swagger' => '2.0',
            'info' => [
                'version' => $this->version,
                'title' => $this->siteName . ' API',
                'description' => $this->siteName . ' API',
                'termsOfService' => 'http://swagger.io/terms/',
                'contact' => $this->adminEmail ? ['email' => $this->adminEmail] : (object)[],
                'license' => [
                    'name' => 'Apache 2.0',
                    'url' => 'http://www.apache.org/licenses/LICENSE-2.0.html',
                ]
            ],
            'host' => $this->hostName,
            'basePath' => $this->getBasePath(),
            'schemes' => \Yii::$app->request->isSecureConnection ? 'https' : 'http',
            'tags' => $this->tags,
            'paths' => $this->paths,
            'definitions' => $this->definitions ?: (object)[],
        ];
    }

    public function getBasePath()
    {
        return '/api/' . preg_replace('/\.[^\.]+$/', '', $this->version);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES);
    }
}




