<?php

/**
 * Переписанный action helper
 * Теперь вызывать можно любой модуль, а не только тот в котором находится текущая обработка
 *
 */
class Zetta_View_Helper_RenderWidget extends Zend_View_Helper_Action {

	protected static $_COUNTER = 1;

    public function renderWidget($scriptPath, $widgetPath, $params = array()) {

    	$view = $this->cloneView();

    	if (sizeof($params)) {
    		foreach ($params as $k=>$v) {
		    	$view->$k = $v;
    		}
    	}

		$view->addBasePath($scriptPath);

		$this->view->placeholder('z_panel_modules')->{self::$_COUNTER} = $view->render($widgetPath);
		self::$_COUNTER++;

    }

}
