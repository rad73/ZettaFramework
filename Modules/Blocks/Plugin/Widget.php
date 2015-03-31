<?php

/**
 * Плагин для включения inine редактирования блоков
 *
 */
class Modules_Blocks_Plugin_Widget extends Zend_Controller_Plugin_Abstract {

	protected $_view = null;
	
	protected $_isEnable = false;
	protected $_isAccess = false;
	
	
	public function __construct() {
		
		$this->_view = Zend_Registry::get('view');
		
		if (array_key_exists('z_blocks_enabled', $_COOKIE) && true == $_COOKIE['z_blocks_enabled']) {
			$this->_isEnable = true;
		}
		
		if (Zetta_Acl::getInstance()->isAllowed('admin_module_blocks')) {
			$this->_isAccess = true;
		}
		
	}
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) {

		$this->_view->addBasePath(MODULES_PATH . DS . 'Blocks/App/views');

		$this->_view->headLink()
				->appendStylesheet($this->_view->libUrl('/Blocks/public/css/admin.css'));

		if ($this->_isAccess) {

			$this->_view->blocks_enabled = $this->_isEnable;
			$this->_view->renderToPlaceholder('admin/widget.phtml', 'z_panel_modules');
			unset($this->_view->blocks_enabled);
			
			$this->_view->headScript()
				->appendFile($this->_view->libUrl('/Blocks/public/js/admin.js'));	
			
		}

    }
    
     public function dispatchLoopShutdown() {

     	if ($this->_isAccess) {
     	
     		$body = Zend_Controller_Front::getInstance()->getResponse()->getBody();
    		$body = preg_replace('/<body(.*)>(.*)<\/body>/isU', '<body$1><div id="z_blocks_wrapper" class="' .  ($this->_isEnable && $this->_isAccess ? 'z_blocks_enabled' : 'z_blocks_disabled'). '">$2</div></body>', $body);
    	
    		Zend_Controller_Front::getInstance()->getResponse()->setBody($body);
    		
     	}

     }

}