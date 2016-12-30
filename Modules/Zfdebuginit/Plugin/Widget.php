<?php

/**
 * Плагин для размещения иконки в панели виджетов
 * 
 */
class Modules_Zfdebuginit_Plugin_Widget extends Zend_Controller_Plugin_Abstract {

	protected $_view = null;
	
	protected $_isEnable = false;
	
	public function __construct() {
		
		$this->_view = Zend_Registry::get('view');
		
		if (array_key_exists('z_zfdebuginit_enabled', $_COOKIE) && true == $_COOKIE['z_zfdebuginit_enabled']) {
			$this->_isEnable = true;
		}
				
	}
	
	public function routeStartup(Zend_Controller_Request_Abstract $request) {

		$this->_registerPanel();

		if (
			Zetta_Acl::getInstance()->isAllowed('admin_module_zfdebuginit')
			&& Zetta_Acl::getInstance()->isAllowed('admin')
		) {
						
			$this->_view->renderWidget(MODULES_PATH . DS . 'Zfdebuginit/App/views', 'admin/widget.phtml', array(
				'enabled'	=> $this->_isEnable
			));
			
			$this->_view->headScript()
					->appendFile($this->_view->libUrl('/Zfdebuginit/public/js/admin.js'));	
			
		}
		

    }
    
    protected function _registerPanel() {
    	
		set_include_path(get_include_path() . PATH_SEPARATOR . LIBRARY_PATH . '/ZFDebug/library/');
		
		$_config = Zend_Registry::get('config')->Zfdebuginit;

		if ($this->_isEnable || isset($_REQUEST[$_config->getvar])) {
			
			$options = $_config->toArray();
			
			/**
			 * База данных
			 */
			$dbKey = array_search('Database', $options['plugins']);
		
			if (Zend_Registry::isRegistered('db') && $db = Zend_Registry::get('db') && isset($dbKey)) {
				$options['plugins']['Database'] = array('adapter' => array('standard' => Zend_Registry::get('db'))); 
			}
			
			unset($options['plugins'][$dbKey]);
			
			/**
			 * Кэш
			 */
			$cacheKey = array_search('Cache', $options['plugins']);
			
			
			if (Zend_Registry::isRegistered('cache') && $db = Zend_Registry::get('cache') && isset($cacheKey)) {
				$options['plugins']['Cache'] = array('backend' => Zend_Registry::get('cache')->getBackend()); 
			}
		
			unset($options['plugins'][$cacheKey]);
			
			/**
			 * Авторизация
			 */
			$authKey = array_search('ZFDebug_Controller_Plugin_Debug_Plugin_Auth', $options['plugins']);
		
			if (isset($authKey)) {
				$options['plugins']['ZFDebug_Controller_Plugin_Debug_Plugin_Auth'] = array('user' => 'username', 'role' => 'role_name'); 
			}
		
			unset($options['plugins'][$authKey]);
			
			/**
			 * Регистрация плагина самым последним, чтобы он запускался в самом конце
			 */
			$debug = new ZFDebug_Controller_Plugin_Debug($options);
			
			Zend_Controller_Front::getInstance()
				->registerPlugin($debug, 100000);
		
		}
		
    }

}
