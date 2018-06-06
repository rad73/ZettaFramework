<?php

/**
 * Расширяем стандартный View
 *
 */
class Zetta_Bootstrap_Resource_View extends Zend_Application_Resource_View
{
    protected $_view;

    public function init()
    {
        $this->getBootstrap()->bootstrap('Frontcontroller');

        $this->_view = parent::init();

        $this
            ->_saveInRegistry()
            ->_setLayoutPath()
            ->_setHeadFilesFramework()
            ->_setPaginationStyle();

        return $this->_view;
    }

    /**
     * Сохраняем объект view в реестре
     * Теперь к нему можно обратиться Zend_Registry::get('view')
     *
     * @return Zetta_Bootstrap_Resource_View
     */
    protected function _saveInRegistry()
    {
        Zend_Registry::set('view', $this->_view);

        return $this;
    }

    /**
     * Устанавливаем путь к общим шаблонам
     *
     * @return Zetta_Bootstrap_Resource_View
     */
    protected function _setLayoutPath()
    {
        $options = $this->getOptions();
        $this->_view->addScriptPath($options['layoutPath']);

        return $this;
    }

    /**
     * Добавляем к стилевые и js файлам файлы ZettaCMS
     *
     * @return Zetta_Bootstrap_Resource_View
     */
    protected function _setHeadFilesFramework()
    {
        $this->_view->addHelperPath(MODULES_PATH . '/Application/App/views/helpers', 'Zetta_View_Helper_');

        $this->_view->headScript()->setAllowArbitraryAttributes(true);

        /* подключаем ко всем шаблонам общий global.css */
        $this->_view->headLink()
            ->prependStylesheet($this->_view->libUrl('/css/global.css'));

        /* подключаем ко всем шаблонам общий jquery и базовые переменные */
        $this->_view->headScript()
            ->prependFile($this->_view->libUrl('/js/jquery.min.js'))
            ->prependScript(
                '
				var _baseUrl = "' . $this->_view->baseUrl() . '",
					_currentUrl = "' . $this->_view->currentUrl() . '";'
            );

        $this->_view->headMeta()
            ->setName('generator', 'ZettaCMS')
            ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');

        return $this;
    }

    /**
     * Устанавливаем стиль пейджинатора
     *
     * @return Zetta_Bootstrap_Resource_View
     */
    protected function _setPaginationStyle()
    {
        Zend_Paginator::setDefaultScrollingStyle('All');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination_control.phtml');

        return $this;
    }
}
