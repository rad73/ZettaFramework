<?php

class Modules_Admin_Plugin_Panel extends Zend_Controller_Plugin_Abstract {

	protected $_view = null;

	protected $_savedBody = '';

	protected $_panelHTML = '';


	public function __construct() {
		$this->_view = Zend_Registry::get('view');
	}

	public function routeStartup(Zend_Controller_Request_Abstract $request) {

		$this->_view->headLink()
			->appendStylesheet($this->_view->libUrl('/css/ui.jquery/jquery.ui.css'))
			->appendStylesheet($this->_view->libUrl('/Admin/public/css/panel.css'))
			->appendStylesheet($this->_view->libUrl('/Editor/public/js/imperavi/redactor.css'))
			->appendStylesheet($this->_view->libUrl('/Editor/public/js/imperavi/plugins/alignment/alignment.css'))
			
			->appendStylesheet($this->_view->libUrl('/Editor/public/js/elFinder/css/elfinder.min.css'))
			->appendStylesheet($this->_view->libUrl('/Editor/public/js/elFinder/css/theme.css'))

			->appendStylesheet($this->_view->libUrl('/css/font-awesome.css'));

		$this->_view->headScript()
			->appendFile($this->_view->libUrl('/js/jquery/jquery.ui.js'))
			->appendFile($this->_view->libUrl('/js/jquery/jquery.nestedSortable.js'))
			->appendFile($this->_view->libUrl('/Admin/public/js/framework.js'))
			->appendFile($this->_view->libUrl('/js/jquery/jquery.form.js'))
			->appendFile($this->_view->libUrl('/js/jquery/jquery.browser.js'))
			->appendFile($this->_view->libUrl('/js/jquery/jquery.history.js'))
			->appendFile($this->_view->libUrl('/js/jquery/jquery.cookie.js'))

			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/redactor.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/fontfamily/fontfamily.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/fontsize/fontsize.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/fontcolor/fontcolor.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/undoredo/undoredo.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/table/table.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/clearformatting/clearformatting.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/pin/pin.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/filemanager/filemanager.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/source/source.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/alignment/alignment.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/plugins/video/video.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/imperavi/lang/ru.js'))

			->appendFile($this->_view->libUrl('/Editor/public/js/elFinder/js/elfinder.full.js'))
			->appendFile($this->_view->libUrl('/Editor/public/js/elFinder/js/i18n/elfinder.ru.js'))

			->appendFile($this->_view->libUrl('/Editor/public/js/editor.js'))
			->appendFile($this->_view->libUrl('/Admin/public/js/panel.js'));

    }

    public function routeShutdown(Zend_Controller_Request_Abstract $request) {

    	if (
    		in_array(System_String::StrToLower($request->getControllerName()), array('admin', 'panel'))
    		&& false == $request->isXmlHttpRequest()
    		&& false == $request->getParam('direct')
    	) {

    		$redirector = new Zend_Controller_Action_Helper_Redirector();
			$redirector->gotoUrlAndExit('#' . $this->_view->baseUrl() . $this->_view->currentUrl());
    	}

    }

    public function postDispatch(Zend_Controller_Request_Abstract $request) {

    	if ($this->_panelHTML) return;

    	if ('admin' != $request->getModuleName() && 'panel' != $request->getControllerName()) {

    		$this->_saveAndCleanCurrentBody();

    		$requestAdminPanel = new Zend_Controller_Request_Simple('index', 'panel', 'admin');
    		$ActionStack = Zend_Controller_Front::getInstance()->getPlugin('Zend_Controller_Plugin_ActionStack');
    		$ActionStack->forward($requestAdminPanel);

    	}
    	else if ('index' == $request->getActionName()) {

    		$this->_panelHTML = Zend_Controller_Front::getInstance()->getResponse()->getBody();
    		$this->_restoreBody();

    	}

    }

    public function dispatchLoopShutdown() {

    	$body = Zend_Controller_Front::getInstance()->getResponse()->getBody();
    	$body = preg_replace('/<body(.*)>(.*)<\/body>/isU', '<body$1><div id="zetta_wrapper" class="z_disable_editing">' . $this->_panelHTML . '<div id="zetta_content_wrapper">$2</div></div></body>', $body);

    	Zend_Controller_Front::getInstance()->getResponse()->setBody($body);

    }

    protected function _saveAndCleanCurrentBody() {
    	$this->_savedBody = Zend_Controller_Front::getInstance()->getResponse()->getBody();
		Zend_Controller_Front::getInstance()->getResponse()->clearBody();
    }

    protected function _restoreBody() {
		Zend_Controller_Front::getInstance()->getResponse()->setBody($this->_savedBody);
    }

}
