<?php

require_once 'Modules/Dbmigrations/App/models/History.php';

class Modules_Dbmigrations_Framework_Manager
{

    /**
     * Получаем список классов - миграций
     *
     * @return array
     */
    public function getMigrationClasses()
    {
        $array = array();
        foreach (Zend_Registry::get('config')->Dbmigrations->search_pattern as $folder) {
            $files = glob($folder, GLOB_NOSORT);
            $array = array_merge($array, $files);
        }

        return $this->_filesToClass($array);
    }

    /**
     * Выборка ветки текущего состояния БД
     *
     * @return array
     */
    public function getCurrentBranch()
    {
        $table = new Modules_Dbmigrations_Model_History();

        return $table->fetchAll()->toArray();
    }

    /**
     * Выборка мастер-ветки
     *
     * @return array
     */
    public function getMasterBranch()
    {
        $fName = Zend_Registry::get('config')->Dbmigrations->master;
        if (file_exists($fName)) {
            return unserialize(file_get_contents($fName));
        } else {
            return false;
        }
    }

    /**
     * Поднимаем миграцию
     *
     * @param string $migrationClass
     */
    public function upTo($migrationClass, $params = false, $saveHistory = true)
    {
        if (is_string($migrationClass) && class_exists($migrationClass)) {
            $migration = new $migrationClass;
        } elseif ($migrationClass instanceof Modules_Dbmigrations_Framework_Abstract) {
            $migration = $migrationClass;
        } else {
            return false;
        }

        $migration->up($params);

        if ($saveHistory) {
            $table = new Modules_Dbmigrations_Model_History();

            try {
                $table->info();
            } catch (Exception $e) {
                // таблицы истории миграций нету
                // создаём таблицу истории
                $this->upTo('Modules_Dbmigrations_Migrations_CreateTableHistory');
            }

            $table->insert(array(
                'date' => date('Y-m-d H:i:s'),
                'class_name' => get_class($migration),
                'comment' => $migration->getComment(),
            ));
        }
    }

    /**
     * Откатываем миграцию
     *
     * @param string $migrationClass
     */
    public function downTo($migrationClass, $params = false)
    {
        if (false == class_exists($migrationClass)) {
            $file = System_Init::classToDirName($migrationClass);

            if (false == class_exists($migrationClass)) {
                return false;
            }
        }

        $table = new Modules_Dbmigrations_Model_History();
        $table->delete($table->getAdapter()->quoteInto('class_name=?', $migrationClass));

        $migration = new $migrationClass;
        $migration->down($params);
    }

    /**
     * Текущую ветку делаем мастер-веткой
     *
     */
    public function setCurrentToMaster()
    {
        $fname = Zend_Registry::get('config')->Dbmigrations->master;

        $current = $this->getCurrentBranch();
        file_put_contents($fname, serialize($current));
    }

    /**
     * Разрываем цепочку миграций в текущей ветке
     *
     * @param Modules_Dbmigrations_Abstract $startClassName
     */
    public function chainBreak(Migrations_Abstract $startClassName)
    {
        $table = new Modules_Dbmigrations_Model_Migrations();
        $id = $table->fetchRow($table->getAdapter()->quoteInto('class_name = ?', $startClassName))->id;

        $chain = $table->fetchAll($table->select()->where('id >= ?', $id)->order('id DESC'));
        foreach ($chain as $item) {
            $this->downTo($item->class_name);
        }
    }


    protected function _filesToClass($files)
    {
        $resultArray = array();

        foreach ($files as $file) {
            $className = $this->_dirToNameClass($file);

            if ($className && class_exists($className)) {
                array_push($resultArray, $className);
            }
        }

        return $resultArray;
    }

    protected function _dirToNameClass($file)
    {
        $directory = realpath($file);
        if (true == empty($directory)) {
            return false;
        }

        return $this->_getClassName($directory);
    }

    protected function _getClassName($file)
    {
        $intrest = array(T_CLASS, T_INTERFACE);
        $tokens = token_get_all(file_get_contents($file));
        $namespace = '';
        $classes = array();

        for ($i = 0, $count = sizeof($tokens); $i < $count; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j = $i + 1; $j < $count; ++$j) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= ($namespace ? '\\' : '') . $tokens[$j][1];
                    } elseif ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j = $i + 1;$j < $count;++$j) {
                    if ($tokens[$j] === '{') {
                        $classes[] = ($namespace ? $namespace . '\\' : '') . $tokens[$i + 2][1];
                    }
                }
            }
        }

        $classes = array_values($classes);

        return sizeof($classes) ? $classes[0] : false;
    }
}
