<?php

class Modules_Service_Model_Update
{
    const URL_UPDATE = 'http://updates.asdf.by/';
    
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
        
        $this->_updateDir = TEMP_PATH . DS . 'Updates';
        $this->_configDb = Zend_Registry::get('config')->Db;
        
        if (false == is_dir($this->_updateDir)) {
            mkdir($this->_updateDir);
        }
    }
    
    /**
     * Синглтон
     *
     * @return Modules_Service_Model_Update
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }

    public function update($skip_backup = false, $skip_zetta = false)
    {
        $client = new Zend_Http_Client(self::URL_UPDATE);
        $client->setParameterPost(array(
            'host' => HTTP_HOST,
            'version' => $this->currentVersion(),
            'key' => Zend_Registry::get('config')->Db->staticSalt
        ));
        
        $response = $client->request('POST');
        
        if (stristr($response->getHeader('Content-type'), 'application')) {	// доступно новое обновление
            
            preg_match('/filename=".+\.(.+)"/', $response->getHeader('Content-disposition'), $matches);
            $archiveType = $matches[1] == 'zip' ? 'zip' : 'tgz';
            
            // делаем резервную копию сайта
            if (false == $skip_backup) {
                Modules_Service_Model_Backup::getInstance()->backup($skip_zetta);
            }
            
            file_put_contents($this->_updateDir . DS . 'update.' . $archiveType, $response->getBody());
            
            // разархивируем данные
            exec(
                'cd ' . $this->_updateDir
                .
                    (
                        ($archiveType == 'zip')
                            ? ' && unzip -o update.zip -d .'
                            : ' && tar -xvf update.tgz .'
                    )
                . ' && rm update.' . $archiveType
            );
            
            if (is_file($this->_updateDir . DS . 'zetta.' . $archiveType) && false == $skip_zetta) {
                // обновляем библиотеку
                exec(
                    'cd ' . SYSTEM_PATH
                    . ' && rm -rf ./* '
                    .
                        (
                            ($archiveType == 'zip')
                                ? ' && unzip -o ' . $this->_updateDir . DS . 'zetta.zip  -d .'
                                : ' && tar -xvf ' . $this->_updateDir . DS . 'zetta.tgz .'
                        )
                    . ' && rm ' . $this->_updateDir . DS . 'zetta.' . $archiveType
                );
            } else {
                exec(
                    'rm ' . $this->_updateDir . DS . 'zetta.' . $archiveType
                );
            }
            
            if (is_file($this->_updateDir . DS . 'files.' . $archiveType)) {
                // обновляем данные по сайту
                exec(
                    'cd ' . FILE_PATH
                    .
                        (
                            ($archiveType == 'zip')
                            ?' && unzip -o ' . $this->_updateDir . DS . 'files.zip  -d .'
                            :' && tar -xvf ' . $this->_updateDir . DS . 'files.tgz .'
                        )
                    . ' && rm ' . $this->_updateDir . DS . 'files.' . $archiveType
                );
            }
            
            if (is_file($post_install = $this->_updateDir . DS . 'install.php')) {
                // запускаем  скрипт постобновления
                require_once $post_install;
                unlink($post_install);
            }
            
            $version = $this->currentVersion();
            Zend_Registry::get('Logger')->info($str = 'Автоматическое обновление завершено, текущая версия ZettaCMS: ' . $version);
            
            // пробуем обновиться на следующую версию
            $this->update(true, $skip_zetta);
        }
    }

    /**
     * Текущая версия ZettaCMS
     *
     * @return double
     */
    public function currentVersion()
    {
        $versionFile = $this->_updateDir . DS . 'current.version';
        
        if (is_file($versionFile)) {
            return doubleval(file_get_contents($versionFile));
        } else {
            return 0;
        }
    }
    
    /**
     * Доспутная версия для обновления
     *
     * @return double
     */
    public function avalibleVersion()
    {
        $client = new Zend_Http_Client(self::URL_UPDATE);
        $client->setParameterPost(array(
            'host' => HTTP_HOST,
            'version' => $this->currentVersion(),
            'key' => Zend_Registry::get('config')->Db->staticSalt,
            'getAvalibleVersion' => 1,
        ));
        
        $response = $client->request('POST');
        
        $return = $response->getBody();

        return $return ? $return : self::currentVersion();
    }
}
