<?php

/**
 * Попытка авторизации с сессий
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Access_Framework_Auth_Plugin_Session extends Modules_Access_Framework_Auth_Plugin_Abstract {

	public function authenticate() {

		$storage = new Zend_Auth_Storage_Session();
				
		if (false == $storage->isEmpty()) {

			Zend_Auth::getInstance()->setStorage($storage);
			$authRequest = Zend_Auth::getInstance()->getStorage()->read();
			
			if (is_object($authRequest)) {
			
				$this
					->setUserName($authRequest->username)
					->setHashPassword($authRequest->password);
	
				return parent::authenticate();
				
			}
			else {
				Zend_Auth::getInstance()->logOut();
			}

		}

		return false;
		
	}
	
	protected function _getTreatment() {
		return "? AND active = 1";
	}

}