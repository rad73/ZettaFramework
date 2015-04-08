<?php

class Modules_Service_Model_Backup  {

	/**
	 * Путь к директории где лежат бекапы
	 *
	 * @var string
	 */
	protected $_backupsDir;

	/**
	 * Настройки mysql сайта
	 *
	 * @var Zend_Config
	 */
	protected $_configDb;


	protected static $_instance = null;

	protected function __construct() {

		ini_set('max_execution_time', 3 * 60 * 60);
		ini_set('memory_limit', '256M');

		$this->_backupsDir = TEMP_PATH . DS . 'Backups';
		$this->_configDb = Zend_Registry::get('config')->Db;

		if (false == is_dir($this->_backupsDir)) {
			mkdir($this->_backupsDir);
		}

	}

	/**
	 * Синглтон
	 *
	 * @return Modules_Service_Model_Backup
	 */
	public static function getInstance() {

		if (null === self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;

	}

	/**
	 * Создание резервной копии всего сайта
	 *
	 */
	public function backup($skip_zetta = false) {

		$backupTo = $this->_backupsDir . DS . date('Y-m-d_H-i-s');
		mkdir($backupTo);

		$this
			->_backupDB($backupTo)
			->_backupFiles($backupTo);

		if (false == $skip_zetta) {
			$this->_backupZetta($backupTo);
		}

		$this->_cleanOldBackups();

	}

	/**
	 * Бекапим БД
	 *
	 * @param string $backupTo	Путь куда сохраниться бекап
	 * @return Modules_Service_Model_Backup
	 */
	protected function _backupDB($backupTo) {

		exec('mysqldump'
			. ' -u' . $this->_configDb->params['username']
			. ' -h' . $this->_configDb->params['host']
			. ' -p' . $this->_configDb->params['password']
			. ' ' . $this->_configDb->params['dbname']
			. ' > ' . $backupTo . DS . 'database.sql'
		);

		return $this;

	}

	/**
	 * Бекапим файловую систему сайта
	 *
	 * @param string $backupTo	Путь куда сохраниться бекап
	 * @return Modules_Service_Model_Backup
	 */
	protected function _backupFiles($backupTo) {

		exec('cd ' . FILE_PATH
			. ' && tar -cvpzf ' . $backupTo . DS . 'files.tgz .  --exclude="*/Backups/*" --exclude="\.git*"'
		);

		return $this;

	}

	/**
	 * Бекапим библиотеку ZettaCMS
	 *
	 * @param string $backupTo	Путь куда сохраниться бекап
	 * @return Modules_Service_Model_Backup
	 */
	protected function _backupZetta($backupTo) {

		exec('cd ' . SYSTEM_PATH
			. ' && tar -cvpzf ' . $backupTo . DS . 'zetta.tgz ./* --exclude="\.git*"'
		);

		return $this;

	}

	/**
	 * Чистим старые бекапы (срок хранения 15 дней)
	 *
	 * @return Modules_Service_Model_Backup
	 */
	protected function _cleanOldBackups() {

		$backups = glob($this->_backupsDir . '/*', GLOB_ONLYDIR);

		foreach ($backups as $dir) {

			if (time() - 15 * 24 * 3600 >= filemtime($dir)) {
				System_Functions::unlinkDir($dir);
			}

		}

		return $this;

	}

	/**
	 * Восстанавливаем сайт из архива
	 *
	 * @param string $dir	Имя папки с архивом
	 */
	public function restore($dir) {

		$this->backup();

		$dirPath = $this->_backupsDir . DS . $dir;
		$dbFile = $dirPath . DS . 'database.sql';
		$filesZip = $dirPath . DS . 'files.tgz';
		$zettaZip = $dirPath . DS . 'zetta.tgz';

		if (is_file($dbFile)) {

			// восстанавливаем БД
			exec('mysql '
				. ' -u' . $this->_configDb->params['username']
				. ' -p' . $this->_configDb->params['password']
				. ' -h' . $this->_configDb->params['host']
				. ' ' . $this->_configDb->params['dbname'] . ' < ' . $dbFile
			);

		}

		if (is_file($filesZip)) {

			// восстанавливаем файлы
			exec('cd ' . FILE_PATH
				. ' && mv ' . $this->_backupsDir . ' ..'
				. ' && rm -rf ./*'
				. ' && tar -xvf ../Backups/' . $dir . '/' . basename($filesZip) . ' .'
				. ' && mv ../Backups/* ' . $this->_backupsDir
			);

		}

		if (is_file($zettaZip)) {

			// восстанавливаем zettaCMS
			exec('cd ' . SYSTEM_PATH
				. ' && rm -rf ./* && tar -xvf ' . $zettaZip . ' .'
			);

		}

	}

}