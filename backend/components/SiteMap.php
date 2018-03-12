<?php

namespace steroids\components;

use steroids\base\Module;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\ArrayHelper;
use yii\web\Request;

/**
 * @package steroids\components
 * @property array $items
 * @property array $requestedRoute
 * @property-read array $activeItem
 */
class SiteMap extends Component
{
    /**
     * @var SiteMapItem[]
     */
    private $_items = [];
    private $_requestedRoute;
    private $isModulesFetched = false;

    /**
     * Recursive scan all items and return url rules for `UrlManager` component
     * @param  array $items
     * @return array
     */
    public static function itemsToRules($items)
    {
        $rules = [];
        foreach ($items as $item) {
            $url = ArrayHelper::getValue($item, 'url');
            $urlRule = ArrayHelper::getValue($item, 'urlRule');

            if ($url && $urlRule && is_array($url)) {
                $defaults = $url;
                $route = array_shift($defaults);

                if (is_string($urlRule)) {
                    $rules[] = [
                        'pattern' => Yii::getAlias($urlRule),
                        'route' => $route,
                    ];
                } elseif (is_array($urlRule)) {
                    if (!isset($urlRule['route'])) {
                        $urlRule['route'] = $route;
                    }
                    $rules[] = $urlRule;
                }
            }

            $subItems = ArrayHelper::getValue($item, 'items');
            if (is_array($subItems)) {
                $rules = array_merge(static::itemsToRules($subItems), $rules);
            }
        }
        return $rules;
    }


    /**
     * @param string $route
     * @return string
     * @throws InvalidParamException
     */
    public static function normalizeRoute($route)
    {
        $route = Yii::getAlias((string)$route);
        if (strncmp($route, '/', 1) === 0) {
            // absolute route
            return trim($route, '/');
        }

        // relative route
        if (Yii::$app->controller === null) {
            throw new InvalidParamException("Unable to resolve the relative route: $route. No active controller is available.");
        }

        if (strpos($route, '/') === false) {
            // empty or an action ID
            return $route === ''
                ? Yii::$app->controller->getRoute()
                : Yii::$app->controller->getUniqueId() . '/' . $route;
        } else {
            // relative to module
            return ltrim(Yii::$app->controller->module->getUniqueId() . '/' . $route, '/');
        }
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function paramGet($name)
    {
        return Yii::$app->request instanceof Request ? Yii::$app->request->get($name) : null;
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public static function paramUser($name)
    {
        if (!Yii::$app->has('user')) {
            return null;
        }
        if (Yii::$app->user->hasProperty($name)) {
            return Yii::$app->user->$name;
        }
        return Yii::$app->user->identity->$name;
    }

    public function init()
    {
        parent::init();

        if (Yii::$app && Yii::$app->has('urlManager')) {
            Yii::$app->urlManager->addRules(static::itemsToRules($this->_items), false);
        }
    }

    /**
     * Add site map items to end of list
     * @param array $items
     */
    public function setItems(array $items)
    {
        $this->addItems($items);
    }

    /**
     * Get all tree site map items
     * @return array
     */
    public function getItems()
    {
        if ($this->isModulesFetched === false) {
            $this->isModulesFetched = true;

            // Fetch items from modules
            if (Yii::$app) {
                foreach (Yii::$app->getModules() as $id => $module) {
                    $this->loadModuleSiteMapRecursive($module);
                }
            }
        }

        return $this->_items;
    }

    /**
     * Add tree site map items
     * @param array $items
     * @param bool|true $append
     */
    public function addItems(array $items, $append = true)
    {
        $this->_items = $this->mergeItems($this->_items, $items, $append);
    }

    /**
     * Returned item with current route and parsed params. Alias Yii::$app->requestedRoute, but also have params
     * @return SiteMapItem|null
     * @throws InvalidConfigException
     */
    public function getActiveItem()
    {
        return $this->getItem($this->getRequestedRoute());
    }

    public function getRequestedRoute()
    {
        if ($this->_requestedRoute === null) {
            // Set active item
            $parseInfo = Yii::$app->urlManager->parseRequest(Yii::$app->request);
            if ($parseInfo) {
                $this->_requestedRoute = array_merge([$parseInfo[0] ? '/' . $parseInfo[0] : ''], $parseInfo[1]);
            } else {
                $this->_requestedRoute = ['/' . Yii::$app->errorHandler->errorAction];
            }
        }
        return $this->_requestedRoute;
    }

    public function setRequestedRoute($value)
    {
        $this->_requestedRoute = $value;
    }

    /**
     * Recursive find site map item by param $item (set null for return root) and return tree
     * items (in format for yii\bootstrap\Nav::items). In param $custom you can overwrite items
     * configuration, if set it as array. Set param $custom as integer for limit tree levels.
     * For example, getNavItems(null, 2) return two-level site map
     * @param array $fromItem
     * @param int $level Level limit
     * @return array
     * @throws InvalidConfigException
     */
    public function getNavItems($fromItem = null, $level = null)
    {
        $itemModels = [];
        if ($fromItem) {
            $item = $this->getItem($fromItem);
            if ($item !== null) {
                $itemModels = $item->items;
            }
        } else {
            $itemModels = $this->getItems();
        }

        if (is_int($level)) {
            // Level limit
            return $this->sliceTreeItems($itemModels, $level);
        }

        return array_map(function ($itemModel) {
            /** @type SiteMapItem $itemModel */
            return $itemModel->toArray();
        }, $itemModels);
    }

    /**
     * Find item by url (ot current page) label and return it
     * @param array|null $url Child url or route, default - current route
     * @return string
     */
    public function getTitle($url = null)
    {
        $titles = array_reverse($this->getBreadcrumbs($url));
        return !empty($titles) ? reset($titles)['label'] : '';
    }

    /**
     * Find item by url (or current page) and return item label with all parent labels
     * @param array|null $url Child url or route, default - current route
     * @param string $separator Separator, default is " - "
     * @return string
     */
    public function getFullTitle($url = null, $separator = ' — ')
    {
        $title = [];
        foreach (array_reverse($this->getBreadcrumbs($url)) as $item) {
            $title[] = $item['label'];
        }
        $title[] = Yii::$app->name;
        return implode($separator, $title);
    }

    /**
     * Return breadcrumbs links for widget \yii\widgets\Breadcrumbs
     * @param array|null $url Child url or route, default - current route
     * @return array
     */
    public function getBreadcrumbs($url = null)
    {
        $url = $url ?: $this->getRequestedRoute();

        // Find child and it parents by url
        $itemModel = $this->getItem($url, $parents);

        if (!$itemModel || (empty($parents) && $this->isHomeUrl($itemModel->normalizedUrl))) {
            return [];
        }

        $parents = array_reverse((array)$parents);
        $parents[] = [
            'label' => $itemModel->modelLabel,
            'url' => $itemModel->normalizedUrl,
            'linkOptions' => is_array($itemModel->linkOptions) ? $itemModel->linkOptions : [],
        ];

        foreach ($parents as &$parent) {
            if (isset($parent['linkOptions'])) {
                $parent = array_merge($parent, $parent['linkOptions']);
                unset($parent['linkOptions']);
            }
        }

        return $parents;
    }

    /**
     * Find item by item url or route. In param $parents will be added all parent items
     * @param string|array $item
     * @param array $parents
     * @return SiteMapItem|null
     * @throws InvalidConfigException
     */
    public function getItem($item, &$parents = [])
    {
        if (is_array($item) && !$this->isRoute($item)) {
            $item = $item['url'];
        }
        if (is_string($item) && strpos($item, '/') === false) {
            $item = implode('.items.', explode('.', $item));
            return ArrayHelper::getValue($this->getItems(), $item);
        }
        return $this->findItemRecursive($item, $this->getItems(), $parents);
    }

    /**
     * Find item by url or route and return it url
     * @param $item
     * @return array|null|string
     */
    public function getItemUrl($item)
    {
        $item = $this->getItem($item);
        return $item ? $item->normalizedUrl : null;
    }

    /**
     * @param string|array|SiteMapItem $url1
     * @param string|array $url2
     * @return bool
     */
    public function isUrlEquals($url1, $url2)
    {
        if ($url1 instanceof SiteMapItem) {
            $url1 = $url1->normalizedUrl;
        }
        if ($url2 instanceof SiteMapItem) {
            $url2 = $url2->normalizedUrl;
        }

        // Is routes
        if ($this->isRoute($url1) && $this->isRoute($url2)) {
            if (static::normalizeRoute($url1[0]) !== static::normalizeRoute($url2[0])) {
                return false;
            }

            $params1 = array_slice($url1, 1);
            $params2 = array_slice($url2, 1);

            // Compare routes' parameters by checking if keys are identical
            if (count(array_diff_key($params1, $params2)) || count(array_diff_key($params2, $params1))) {
                return false;
            }

            foreach ($params1 as $key => $value) {
                if (is_string($key) && $key !== '#') {
                    if (!array_key_exists($key, $params2)) {
                        return false;
                    }

                    if ($value !== null && $params2[$key] !== null && $params2[$key] != $value) {
                        return false;
                    }
                }
            }

            return true;
        }

        // Is urls
        if (is_string($url1) && is_string($url2)) {
            return $url1 === $url2;
        }

        return false;
    }

    public function isAllowAccess($url)
    {
        $item = $this->getItem($url);
        if (!$item) {
            return true;
        }

        return $item->checkVisible($url);
    }

    /**
     * @param string|array $url
     * @return bool
     */
    protected function isHomeUrl($url)
    {
        if ($this->isRoute($url)) {
            return $this->isUrlEquals(['/' . Yii::$app->defaultRoute], $url);
        }
        return $url === Yii::$app->homeUrl;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    protected function isRoute($value)
    {
        return is_array($value) && isset($value[0]) && is_string($value[0]);
    }

    /**
     * @param SiteMapItem[] $items
     * @param int $level
     * @return array
     */
    protected function sliceTreeItems(array $items, $level = 1)
    {
        if ($level <= 0) {
            return [];
        }

        $nav = [];
        foreach ($items as $key => $itemModel) {
            $item = $itemModel->toArray();
            $nextLevel = $level;

            if (!empty($itemModel->items)) {
                if ($itemModel->redirectToChild) {
                    $childModel = null;
                    if ($itemModel->redirectToChild === true) {
                        $childModel = reset($itemModel->items);
                    } elseif (is_string($itemModel->redirectToChild) || is_int($itemModel->redirectToChild)) {
                        $childModel = ArrayHelper::getValue($itemModel->items, $itemModel->redirectToChild);
                    }
                    if ($childModel) {
                        $item['url'] = $childModel->normalizedUrl;
                    }
                    $nextLevel--;
                } elseif ($itemModel->normalizedUrl !== null) {
                    $nextLevel--;
                }
            }

            $item['items'] = $this->sliceTreeItems($itemModel->items, $nextLevel);
            if (empty($item['items'])) {
                $item['items'] = null;
            }
            $nav[$key] = $item;
        }
        return $nav;
    }

    /**
     * @param string|array $url
     * @param SiteMapItem[] $items
     * @param array $parents
     * @return SiteMapItem
     */
    protected function findItemRecursive($url, array $items, &$parents)
    {
        foreach ($items as $itemModel) {
            if ($itemModel->normalizedUrl && $this->isUrlEquals($url, $itemModel->normalizedUrl)) {
                return $itemModel;
            }

            if (!empty($itemModel->items)) {
                $foundItem = $this->findItemRecursive($url, $itemModel->items, $parents);
                if ($foundItem) {
                    $parentItem = $itemModel->toArray();
                    unset($parentItem['items']);
                    $parents[] = $parentItem;

                    return $foundItem;
                }
            }
        }

        return null;
    }

    protected function mergeItems($baseItems, $items, $append, $parentItem = null)
    {
        foreach ($items as $id => $item) {
            // Merge item with group (as key)
            if (is_string($id) && isset($baseItems[$id])) {
                foreach ($item as $key => $value) {
                    if ($key === 'items') {
                        $baseItems[$id]->$key = $this->mergeItems($baseItems[$id]->$key, $value, $append, $baseItems[$id]);
                    } elseif (is_array($baseItems[$id]) && is_array($value)) {
                        $baseItems[$id]->$key = $append ?
                            ArrayHelper::merge($baseItems[$id]->$key, $value) :
                            ArrayHelper::merge($value, $baseItems[$id]->$key);
                    } elseif ($append || $baseItems[$id]->$key === null) {
                        $baseItems[$id]->$key = $value;
                    }
                }
            } else {
                // Create instance
                if (!($item instanceof SiteMapItem)) {
                    $item = new SiteMapItem(array_merge(
                        $item,
                        [
                            'id' => $id,
                            'owner' => $this,
                            'parent' => $parentItem,
                        ]
                    ));
                    $item->items = $this->mergeItems([], $item->items, true, $item);
                }

                // Append or prepend item
                if (is_int($id)) {
                    if ($append) {
                        $baseItems[] = $item;
                    } else {
                        array_unshift($baseItems, $item);
                    }
                } else {
                    if ($append) {
                        $baseItems[$id] = $item;
                    } else {
                        $baseItems = array_merge([$id => $item], $baseItems);
                    }
                }
            }
        }

        ArrayHelper::multisort($baseItems, 'order');

        return $baseItems;
    }

    /**
     * @param Module|array|string $module
     */
    protected function loadModuleSiteMapRecursive($module)
    {
        /** @var Module|string $moduleClass */
        $moduleClass = null;
        $children = [];
        if (is_object($module)) {
            $moduleClass = $module::className();
            $children = $module->getModules();
        } elseif (is_array($module)) {
            $moduleClass = $module['class'];
            $children = ArrayHelper::getValue($module, 'modules', []);
        } else {
            $moduleClass = $module;
        }

        // Append site map
        if (method_exists($moduleClass, 'siteMap')) {
            $this->addItems($moduleClass::siteMap(), true);
        }

        // Load sub modules
        foreach ($children as $subModule) {
            $this->loadModuleSiteMapRecursive($subModule);
        }
    }
}
