<?php
require_once 'Zend/Config/Xml.php';

class System_Mime_Type {

	/**
	 * Объект с типами mime
	 *
	 * @var Zend_Config_Xml
	 */
	private $mimeData;

	/**
	 * Реализуем прттерн синглтон
	 *
	 * @var self
	 */
	private static $_instance;
	
	protected function __construct() {

		$this->mimeData = new Zend_Config_Xml(dirname(__FILE__) . '/Type/MimeTypes.xml');
		
	}

	/**
	 * Получение MIME типа файла
	 *
	 * @param string $fileName
	 * @return string
	 */
	protected function mimeType($filePath) {

		$fileName = basename( $filePath );
		$fileExplodeName = explode( '.', $fileName );
		$fileExtensionName = $fileExplodeName[count($fileExplodeName)-1];   
		
		if ( isset( $this->mimeData->MimeTypes->$fileExtensionName ) ) {
			return $this->mimeData->MimeTypes->$fileExtensionName;
		}
		else {
			return $this->mimeData->DefaultMimeTypes->value;
		}

	}

	/**
	 * Статический метод для получения MIME типа файла
	 *
	 * @param string $filePath
	 * @return string
	 */
	public static function mime($filePath) {

		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		
		return self::$_instance->mimeType($filePath);

	}

}