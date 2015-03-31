<?php

/**
 * Zend_Controller_Request_Http
 *
 * HTTP request object for use with Zend_Controller family.
 *
 * @uses Zend_Controller_Request_Abstract
 * @package Zend_Controller
 * @subpackage Request
 */
class Zetta_Controller_Request_Http extends Zend_Controller_Request_Http {
	
	/**
     * Retrieve the module name
     *
     * @return string
     */
	public function getModuleName() {
		return ucfirst(System_String::StrToLower(parent::getModuleName()));
	}

}