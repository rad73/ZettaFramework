<?php

/**
 * Плагин для глобальной защиты от csrf атак
 *
 *
 */
class Modules_Default_Plugin_Csrf extends Zend_Controller_Plugin_Abstract {

	protected $_securitySession = null;
	protected $_csrf_hash = null;
	protected $_view = null;

	public function __construct() {
		$this->_view = Zend_Registry::get('view');
	}

	public function routeStartup(Zend_Controller_Request_Abstract $request) {

		$this->_securitySession = new Zend_Session_Namespace('Zetta_Security');

		if (
			$request->isPost()
			&& (!$request->getParam('csrf_hash') || $request->getParam('csrf_hash') != $this->_securitySession->csrf_hash)
		) {
			throw new Exception('Access Denied (csrf attack detected)', 401);
		}

		$this->_csrf_hash = md5(rand());

		if (!$this->_securitySession->csrf_hash) {
			$this->_securitySession->csrf_hash = $this->_csrf_hash;
		}

		$this->_view->headScript()
			->prependScript('
				var _csrf_hash = "' . $this->_securitySession->csrf_hash . '";'
			);

		$this->_view->csrf_hash = $this->_securitySession->csrf_hash;

		Zend_Controller_Front::getInstance()
			->unregisterPlugin($this)
			->registerPlugin($this, 1000000);	// перерегистрируем плагин чтобы dispatchLoopShutdown запустился последним

	}

	public function dispatchLoopShutdown() {

		$body = $this->getResponse()->getBody();

		//  записываем тоукен в каждую форму
		$field = $this->_view->formHidden('csrf_hash', $this->_securitySession->csrf_hash);
		$body = str_ireplace('</form>', $field . '</form>', $body);

		$this->getResponse()->setBody($body);

	}

}