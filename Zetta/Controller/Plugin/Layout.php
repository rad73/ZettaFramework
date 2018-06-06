<?php

/**
 * Плагин для поиск шаблонов вида
 *
 * Если найдены одинаковый шаблон в разных папках поиска,
 * приоритет отдаётся последнему найденному
 *
 * Это позволяет переопределять системные шаблоны, без клонирования всего модуля
 *
 */
class Zetta_Controller_Plugin_Layout extends Zend_Controller_Plugin_Abstract
{
    protected $_moduleKey = ':module';
    
    /**
     * Zend_View
     *
     * @var Zend_View
     */
    protected $_view;
    
    /**
     * Непосредственный поиск шаблонов вида
     *
     * @param Zend_Controller_Request_Abstract $request	Объект запроса
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_view = Zend_Layout::startMvc()->getView();

        $dirs = array(MODULES_PATH . DS . $this->_getModuleName(), HEAP_PATH . DS . $this->_getModuleName());
        
        $inView = $this->_view->getScriptPaths();
        foreach ($dirs as $dir) {
            self::addBasePath($dir);
        }
    }

    /**
     * Получаем имя текущего модуля
     *
     * @return string
     */
    protected function _getModuleName()
    {
        $front = Zend_Controller_Front::getInstance();

        $moduleName = ($moduleName = $front->getRequest()->getModuleName())
                            ? $moduleName
                            : $front->getDefaultModule();

        return $moduleName;
    }
    
    /**
     * Добавляем путь к view с учётом проверки есть ли уже путь
     *
     * @param string $path
     */
    public static function addBasePath($path)
    {
        $_view = Zend_Layout::startMvc()->getView();
        $inView = $_view->getScriptPaths();
        
        // не добавляем шаблоны если они уже были ранее добавлены
        $add = true;
        foreach ($inView as $in_view_dir) {
            if (stristr($in_view_dir, $path)) {
                $add = false;
            }
        }
        
        if ($add && Zend_Loader::isReadable($path)) {
            $_view->addBasePath($path . '/App/views/');
        }
    }
}
