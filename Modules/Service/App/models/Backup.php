<?php

class Modules_Service_Model_Backup
{

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

    protected function __construct()
    {
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
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Создание резервной копии всего сайта
     *
     */
    public function backup($skip_zetta = false, $skip_folders = false)
    {
        $backupTo = $this->_backupsDir . DS . date('Y-m-d_H-i-s');
        mkdir($backupTo);

        $this
            ->_backupDB($backupTo)
            ->_backupFiles($backupTo, $skip_folders);

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
    protected function _backupDB($backupTo)
    {
        exec(
            'mysqldump'
            . ' -u' . $this->_configDb->username
            . ' -h' . $this->_configDb->host
            . ' -p' . $this->_configDb->password
            . ' ' . $this->_configDb->dbname
            . ' | gzip > ' . $backupTo . DS . 'database.sql.gz'
        );

        return $this;
    }

    /**
     * Бекапим файловую систему сайта
     *
     * @param string $backupTo	Путь куда сохраниться бекап
     * @return Modules_Service_Model_Backup
     */
    protected function _backupFiles($backupTo, $skipFolders = false)
    {
        exec(
            'cd ' . FILE_PATH
            . ' && tar -cvpzf ' . $backupTo . DS . 'files.tgz .  --exclude="*/Backups/*" --exclude="\.git*"'
            . ($skipFolders ? ' --exclude="' . $skipFolders . '"' : '')
        );

        return $this;
    }

    /**
     * Бекапим библиотеку ZettaCMS
     *
     * @param string $backupTo	Путь куда сохраниться бекап
     * @return Modules_Service_Model_Backup
     */
    protected function _backupZetta($backupTo)
    {
        exec(
            'cd ' . SYSTEM_PATH
            . ' && tar -cvpzf ' . $backupTo . DS . 'zetta.tgz ./*'
        );

        return $this;
    }

    /**
     * Чистим старые бекапы (срок хранения 7 дней)
     *
     * @return Modules_Service_Model_Backup
     */
    protected function _cleanOldBackups()
    {
        $daySaveBaskups = isset(Zend_Registry::get('SiteConfig')->backups_days_life)
            ? Zend_Registry::get('SiteConfig')->backups_days_life
            : 7;

        $backups = glob($this->_backupsDir . '/*', GLOB_ONLYDIR);

        foreach ($backups as $dir) {
            if (time() - $daySaveBaskups * 24 * 3600 >= filemtime($dir)) {
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
    public function restore($dir)
    {
        $this->backup();

        $dirPath = $this->_backupsDir . DS . $dir;
        $dbFile = $dirPath . DS . 'database.sql.gz';
        $filesZip = $dirPath . DS . 'files.tgz';
        $zettaZip = $dirPath . DS . 'zetta.tgz';

        if (is_file($dbFile)) {

            // восстанавливаем БД
            exec(
                'gunzip < ' . $dbFile . ' | mysql '
                . ' -u' . $this->_configDb->username
                . ' -p' . $this->_configDb->password
                . ' -h' . $this->_configDb->host
                . ' ' . $this->_configDb->dbname
            );
        }

        if (is_file($filesZip)) {

            // восстанавливаем файлы
            exec(
                'cd ' . FILE_PATH
                . ' && mv ' . $this->_backupsDir . ' ..'
                . ' && rm -rf ./*'
                . ' && tar -xvf ../Backups/' . $dir . '/' . basename($filesZip) . ' .'
                . ' && mv ../Backups/* ' . $this->_backupsDir
            );
        }

        if (is_file($zettaZip)) {

            // восстанавливаем zettaCMS
            exec(
                'cd ' . SYSTEM_PATH
                . ' && rm -rf ./* && tar -xvf ' . $zettaZip . ' .'
            );
        }
    }
}
