<?php

require_once 'Zend/Application/Bootstrap/Bootstrap.php';

class BootstrapQuick extends Zend_Application_Bootstrap_Bootstrap {

	/**
	 * Устанавливаем кодировку UTF-8 для функций mb_*
	 *
	 */
	protected function _initEncoding() {

		if (phpversion() < 5.6) {
			mb_internal_encoding('utf-8');
			iconv_set_encoding('internal_encoding', 'utf-8');
		}
	}

	/**
	 * Инициализируем переменные с коммандной строки
	 *
	 */
	protected function _initOptParams() {

		$arrayInput = getopt(false, array('url:'));
		if ($arrayInput && array_key_exists('url', $arrayInput)) {
			$_SERVER['REQUEST_URI'] = $arrayInput['url'];
		}

	}

	/**
	 * Устанавливаем путь к системным папкам
	 * таким как Zend Fremawork library и библиотеки CMS
	 */
	protected function _initIncludePath() {

		set_include_path(implode(PATH_SEPARATOR, array(
			FILE_PATH,
			MODULES_PATH,
			HEAP_PATH,
			get_include_path()))
		);
		$this->bootstrap('Autoloader');

	}

	/**
	 * Создаём реестр для config
	 */
	protected function _initConfigRegistry() {
		Zend_Registry::set('config', new stdClass());
	}

	/**
	 * Сохраняем LOG в Zend_Registry::get('Logger');
	 *
	 */
	protected function _initRegisterLogger() {

		$options = $this->getPluginResource('log')->getOptions();
		$options = $options['stream']['writerParams'];

		$logFile = $options['stream'];
		if (!is_file($logFile)) {
			touch($logFile);
			chmod($logFile, octdec($options['file_perm']));
		}

		$this->bootstrap('Log');
		$logger = $this->getResource('Log');

		if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
			$logger->setEventItem('remote_addr', $_SERVER['REMOTE_ADDR']);
		}
		if (array_key_exists('REQUEST_URI', $_SERVER)) {
			$logger->setEventItem('request_url', $_SERVER['REQUEST_URI']);
		}

		Zend_Registry::set('Logger', $logger);

	}

}