<?php

/**
 * Попытка авторизации удалённо
 * 
 * @author Александр Хрищанович
 *
 */
class Modules_Access_Framework_Auth_Plugin_Internet extends Modules_Access_Framework_Auth_Plugin_RequestRsa {

	protected $_password;
	protected $_username;
	
	public function authenticate() {

		$authRequest = Zend_Auth::getInstance()->getStorage()->read();
		$request = Zend_Controller_Front::getInstance()->getRequest();
		
		if (
			!$request->isGet()
			&& ($username = $request->getParam('username'))
		) {
			
			$this->_username = $username;
		
			if ($hash = $request->getParam('auth_hash')) {
				$this->_password = $this->_decodePassword($hash);
			}
			else if ($hash = $request->getParam('auth_password')) {
				$this->_password = md5($hash);
			}
			
			if ($this->_password && $this->_username) {

				try {
					$client = new Zend_Http_Client();
					$client
						->setConfig(array(
					        'maxredirects' => 0,
					        'timeout'      => 5)
						)
						->setUri('http://auth.asdf.by')
						->setParameterPost(array(
							'username'	=> $this->_username,
							'password'	=> $this->_password,
						));
						
					$response = $client->request('POST');
					
					if ($response->getBody() == 1) {
						return $this->getResultObject();
					}
				}
				catch (Exception $e) {
					Zetta_ErrorHandler::$DISABLE = true;
					Zend_Registry::get('Logger')->info('При авторизации не удалось подключиться к http://auth.asdf.by');
				}
				
			}
			
		}
		else if (is_object($authRequest) && $authRequest->role_name == 'superadmin') {
			
			$this->_password = $authRequest->password;
			$this->_username = $authRequest->username;
			
			return $this->getResultObject();

		}
				
		return false;

	}
	
	public function getResultObject() {
		
		$object = new stdClass();
		$object->username = $this->_username;
		$object->role_name = 'superadmin';
		$object->password = $this->_password;
		
		return $object;
	}

}
