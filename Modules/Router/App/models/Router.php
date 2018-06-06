<?php

class Modules_Router_Model_Router extends Zend_Db_Table
{
    protected static $_instance;

    protected $_name = 'routes';
    protected $_routesTree = array();	// информация о роутах в виде дерева
    protected $_routesData = array();	// информация о роутах в плоском виде
    protected $_current;

    /**
     * singleton
     *
     * @return Modules_Router_Model_Router
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public static function resetInstance()
    {
        self::$_instance = new self();
    }

    public function __construct($config = array(), $definition = null)
    {
        parent::__construct($config, $definition);

        $this->_buildRoutesTree();
    }

    /**
     * Делаем выборку из дерева роутов
     *
     * Дальнейшие операции для производительности идут через масив $this->_routesTree или через getter "$this->getRoutesTree"
     *
     * @param int $parent_id
     * @param string $parent_url
     * @return array
     */
    protected function _buildRoutesTree($childs = false, $parent_url = '')
    {
        if ($childs == false) {
            $select = $this->select()
                /* ->where('disable IS NULL')->orWhere('disable = 0') */
                ->order('sort ASC');

            $items = $this->fetchAll($select)->toArray();
            $childs = System_Functions::toForest($items, 'route_id', 'parent_route_id');
        }

        $cleanRoutes = array();
        $i = 0;
        foreach ($childs as $row) {
            $cleanRoutes[$i] = $row;
            $cleanRoutes[$i]['url'] = $parent_url . $row['uri'] . '/';

            if (sizeof($row['childs'])) {
                $cleanRoutes[$i]['childs'] = $this->_buildRoutesTree($row['childs'], $cleanRoutes[$i]['url']);
            }

            $cleanRoutes[$i]['module'] = strtolower($cleanRoutes[$i]['module']);
            $cleanRoutes[$i]['controller'] = strtolower($cleanRoutes[$i]['controller']);
            $cleanRoutes[$i]['action'] = strtolower($cleanRoutes[$i]['action']);

            $this->_routesData[$row['route_id']] = $cleanRoutes[$i];

            $i++;
        }

        if ($parent_url == '') {
            return ($this->_routesTree = $cleanRoutes);
        } else {
            return $cleanRoutes;
        }
    }

    public function getRoutesTree()
    {
        return $this->_routesTree;
    }

    public function getRoutesTreeHash($parents = false, $level = 0)
    {
        $array = array();
        if ($parents == false) {
            $parents = $this->_routesTree;
        }

        foreach ($parents as $row) {
            $array[$row['route_id']] = str_repeat('-', $level) . ' ' . $row['route_id'] . ': ' . $row['name'];
            if (sizeof($row['childs'])) {
                $array = $array + $this->getRoutesTreeHash($row['childs'], $level + 1);
            }
        }

        return $array;
    }

    /**
     * Ищем раздел по URL
     *
     * @return Zend_Db_Row
     */
    public function current()
    {
        if (!$this->_current) {
            $request = Zend_Controller_Front::getInstance()->getRequest();

            $url = $request->getPathInfo();
            $url = $url ? $url : '/';
            $this->_current = $this->findByUrl($url);
        }

        return $this->_current;
    }

    /**
     * Поиск маршрута по URL
     *
     * @return Zend_Db_Row
     */
    public function findByUrl($url)
    {
        $urlClean = $url;

        if (preg_match('|(.*/)(.*\.html)|', $url, $matches)) {	// отрубаем *.html от пути
            $urlClean = $matches[1];
        }

        foreach ($this->_routesData as $route) {
            if ($route['url'] == $urlClean) {
                return $route;
            }
        }

        return false;
    }

    /**
     * Получаю раздел к которому подключен Module/Controller/Action
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return Zend_Db_Row
     */
    public function getRoute($module, $controller, $action = false)
    {
        $module = strtolower($module);
        $controller = strtolower($controller);
        $action = strtolower($action);

        $find_M_C = $find_M_C_A = false;

        foreach ($this->_routesData as $route) {
            if ($route['module'] == $module && $route['controller'] == $controller) {
                $find_M_C = $route;
            }

            if (
                $route['module'] == $module
                && $route['controller'] == $controller
                && $action != false && $route['action'] == $action
            ) {
                $find_M_C_A = $route;

                break;
            }
        }

        return $find_M_C_A ? $find_M_C_A : $find_M_C;
    }

    /**
     * Получаю разделов к которому подключен Module/Controller/Action
     *
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return Zend_Db_Row
     */
    public function getRoutes($module, $controller)
    {
        $module = strtolower($module);
        $controller = strtolower($controller);

        $find_M_C_A = array();

        foreach ($this->_routesData as $route) {
            if ($route['module'] == $module && $route['controller'] == $controller) {
                $find_M_C_A[] = $route;
            }
        }

        return $find_M_C_A;
    }

    public function getItem($route_id)
    {
        return array_key_exists($route_id, $this->_routesData) ? $this->_routesData[$route_id] : false;
    }

    public function getParentsId($route_id)
    {
        if ($route_id) {
            $parent_id = $this->_routesData[$route_id]['parent_route_id'];
            $return = array($parent_id);

            while ($parent_id) {
                $parent = $this->_routesData[$parent_id];
                if ($parent && $parent_id = $parent['parent_route_id']) {
                    $return[] = $parent_id;
                }
            }

            return $return;
        }
    }

    /**
     * Получаем предустановленные модули для списка "Модуль" при тип маршрута: по умолчанию
     *
     * @return array
     */
    public function getDefaultModules()
    {
        $return = array();

        $heap_controllers = glob(HEAP_PATH . '/*/App/controllers/*Controller.php', GLOB_NOSORT);

        foreach ($heap_controllers as $controller) {
            require_once $controller;
            $arrayOfClasses = System_Functions::get_php_classes(file_get_contents($controller));

            foreach ($arrayOfClasses as $namespace => $classes) {
                foreach ($classes as $class) {
                    $className = ($namespace ? $namespace . '\\' : '') . $class;

                    if (preg_match('/(.+)(_|\\\\)(.+)Controller/', $className, $matches)) {
                        $moduleName = System_String::StrToLower($matches[1]);
                        $controllerName = System_String::StrToLower($matches[3]);

                        $class = new ReflectionClass($className);
                        if (preg_match('/@description (.*)/', $class->getDocComment(), $matches)) {
                            $return[$moduleName . '~' . $controllerName] = $matches[1];
                        }
                    }
                }
            }
        }

        return $return;
    }

    /**
     * Получаем предустановленные действия для списка "Действие", когда тип маршрута: по умолчанию
     *
     * @return array
     */
    public function getDefaultActions($module, $controller)
    {
        $return = array();

        $controller = HEAP_PATH . '/' . ucfirst($module) . '/App/controllers/' . ucfirst($controller) . 'Controller.php';

        require_once $controller;
        $arrayOfClasses = System_Functions::get_php_classes(file_get_contents($controller));

        foreach ($arrayOfClasses as $namespace => $classes) {
            foreach ($classes as $class) {
                $className = ($namespace ? $namespace . '\\' : '') . $class;

                if (preg_match('/(.+)(_|\\\\)(.+)Controller/', $className, $matches)) {
                    $moduleName = System_String::StrToLower($matches[1]);
                    $controllerName = System_String::StrToLower($matches[3]);

                    $class = new ReflectionClass($className);
                    $methods = $class->getMethods();

                    foreach ($methods as $method) {
                        if ($method->isPublic() && stristr($method->getName(), 'Action') && preg_match('/@description (.*)/', $method->getDocComment(), $matches)) {
                            $return[$moduleName . '~' . $controllerName . '~' . str_replace('Action', '', $method->getName())] = $matches[1];
                        }
                    }
                }
            }
        }

        return $return;
    }
}
