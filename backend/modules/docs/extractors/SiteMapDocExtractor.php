<?php

namespace steroids\modules\docs\extractors;

use steroids\components\SiteMapItem;

class SiteMapDocExtractor extends BaseDocExtractor
{
    /**
     * @var SiteMapItem[]
     */
    public $items;

    public function run()
    {
        $this->recursiveExtract($this->items);
    }

    /**
     * @param SiteMapItem[] $items
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    protected function recursiveExtract($items)
    {
        foreach ($items as $item) {
            $url = $item->normalizedUrl;

            if ($this->isRoute($url)) {
                (new ControllerDocExtractor([
                    'swaggerJson' => $this->swaggerJson,
                    'route' => $url[0],
                    'url' => $item->urlRule,
                    'title' => $item->label,
                ]))->run();
            }
            if (is_array($item->items)) {
                $this->recursiveExtract($item->items);
            }
        }

        // Add refs
        foreach (SwaggerTypeExtractor::getInstance()->refs as $name => $ref) {
            $this->swaggerJson->addDefinition($name, $ref);
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isRoute($value)
    {
        return is_array($value) && isset($value[0]) && is_string($value[0]);
    }
}




