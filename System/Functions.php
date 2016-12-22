<?php

/**
 * Класс для статических функций
 *
 */
abstract class System_Functions {

	private final function __construct() {}

	/**
	 * Проебразование файла в имя класса или интерфейса
	 *
	 *
	 * @param string $file
	 * @return string
	 */
	public static function File2Class($file) {

		if (file_exists($file)) {
			$intrest = array(T_CLASS, T_INTERFACE);
			$tokens = token_get_all(file_get_contents($file));

			for($i = 0, $count = sizeof($tokens); $i < $count; $i++) {
				if(in_array($tokens[$i][0], $intrest)) {
					$i = $i+2;
					return  $tokens[$i][1];
				}
			}
		}

		return false;

	}

	/**
	 * Проебразование класса в путь к файлу
	 *
	 * @param string $class
	 * @return string
	 */
	public static function Class2File($class) {
		return str_replace('_', DS, $class) . '.php';
	}

	/**
	 * Преобразуем массив в объект
	 *
	 * @param array $array
	 * @return object
	 */
	public static function toObject($array) {
		return json_decode(json_encode($array), false);
	}

	/**
	 * Преобразуем массив в дерево
	 *
	 * @param array $rows
	 * @param string $idName	Имя ключа ID
	 * @param string $pidName	Имя ключа ParentID
	 * @return array
	 */
	public static function toForest($rows, $idName, $pidName) {

		$children = array(); // children of each ID
		$ids = array();
		foreach ($rows as $i=>$r) {
			$row = &$rows[$i];
			$id = $row[$idName];
			$pid = $row[$pidName];
			$children[$pid][$id] = &$row;
			if (!isset($children[$id])) $children[$id] = array();
			$row['childs'] = &$children[$id];
			$ids[$row[$idName]] = true;
		}

		// Root elements are elements with non-found PIDs.
		$forest = array();
		foreach ($rows as $i=>$r) {
			$row = &$rows[$i];
			if (!isset($ids[$row[$pidName]])) {
			    $forest[$row[$idName]] = &$row;
			}
		}

		return $forest;
    }

    /**
     * Проверяем есть ли таблица в БД
     *
     * @param string $tableName
     * @return bool
     */
    public static function tableExist($tableName, $connectionName = false) {

		if (
			$connectionName !== false
			&& Zend_Registry::isRegistered('dbs')
			&& isset(Zend_Registry::get('dbs')[$connectionName])
		) {
			$connection = Zend_Registry::get('dbs')[$connectionName];
		}
		else {
			$connection = Zend_Registry::get('db');
		}

		$databaseTables = $connection->listTables();
    	return in_array($tableName, $databaseTables);

    }

	public static function getFolderFilesRecursive($path, $ext = array('*')) {

		$Directory = new RecursiveDirectoryIterator($path);
		$Iterator = new RecursiveIteratorIterator($Directory, RecursiveIteratorIterator::CHILD_FIRST);
		return new RegexIterator($Iterator, '/^.+(\.' . implode('|\.', $ext) . ')$/i', RecursiveRegexIterator::GET_MATCH);

	}

	/**
	 * Генерация путей к thumbs
	 *
	 * @param string $path
	 * @param array(w, h) $sizes
	 * @return string
	 */
	public static function getThumbUrl($path, $sizes = array(200, 100), $watermarked = false, $prefix = '') {

		$name = basename($path);
		$temp = explode('.', $name);
		$ext = end($temp);
		$dir = dirname($path);
		return $dir . DS . 'thumbs' . DS . $prefix . str_replace('.' . $ext, '', $name) . '_' . $sizes[0] .'x' . $sizes[1] . ($watermarked ? '_w' : '') . '.' . $ext;

	}

	/**
	 * Генерация превью
	 *
	 * @param string $imageSrc		путь к оригиналу
	 * @param string $imageDest		путь куда сохранить превью
	 * @param int $width			ширина
	 * @param int $height			высота
	 * @param int $quality			качество сохраняемой картинки
	 * @return bool
	 */
	public static function createThumb($imageSrc, $imageDest, $width, $height, $quality = 85) {

		$thumb = new \PHPThumb\GD($imageSrc);
		$dimensions = $thumb->getCurrentDimensions();

		if ($dimensions['width']) {

			$thumb->setOptions(array('jpegQuality' => $quality));
			$thumb->setFormat(strtoupper(pathinfo($imageDest, PATHINFO_EXTENSION)));
			$thumb->resizeToSquare($width, $height);
			$thumb->save($imageDest);

			return $imageDest;

		}

		return false;

	}

	/**
	 * Генерация превью с водяным знаком
	 *
	 * @param string $imageSrc		путь к оригиналу
	 * @param string $imageDest		путь куда сохранить превью
	 * @param int $width			ширина
	 * @param int $height			высота
	 * @param int $quality			качество сохраняемой картинки
	 * @return bool
	 */
	public static function createThumbWatermark($imageSrc, $imageDest, $width, $height, $quality = 85) {
		
		$thumb = new \PHPThumb\GD($imageSrc, array(), array(
			new PHPThumb\Plugins\Watermark(0, 0, USER_FILES_PATH . DS . 'watermark.png')
		));
		$dimensions = $thumb->getCurrentDimensions();

		if ($dimensions['width']) {

			$thumb->setOptions(array('jpegQuality' => $quality));
			$thumb->setFormat(strtoupper(pathinfo($imageDest, PATHINFO_EXTENSION)));
			$thumb->resizeToSquare($width, $height);
			$thumb->save($imageDest);

			return $imageDest;

		}

		return false;

	}

	/**
	 * Получаем все классы из файла
	 *
	 * @param string $php_code
	 * @return array
	 */
	public static function get_php_classes($php_code) {

		$classes = array();
		$namespace = 0;
		$tokens = token_get_all($php_code);
		$count = count($tokens);
		$dlm = false;

		for ($i = 2; $i < $count; $i++) {
			if ((isset($tokens[$i - 2][1]) && ($tokens[$i - 2][1] == "phpnamespace" || $tokens[$i - 2][1] == "namespace")) ||
				($dlm && $tokens[$i - 1][0] == T_NS_SEPARATOR && $tokens[$i][0] == T_STRING)) {
				if (!$dlm) $namespace = 0;
				if (isset($tokens[$i][1])) {
					$namespace = $namespace ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
					$dlm = true;
				}
			}
			elseif ($dlm && ($tokens[$i][0] != T_NS_SEPARATOR) && ($tokens[$i][0] != T_STRING)) {
				$dlm = false;
			}
			if (($tokens[$i - 2][0] == T_CLASS || (isset($tokens[$i - 2][1]) && $tokens[$i - 2][1] == "phpclass"))
					&& $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
				$class_name = $tokens[$i][1];
				if (!isset($classes[$namespace])) $classes[$namespace] = array();
				$classes[$namespace][] = $class_name;
			}
		}
		return $classes;

	}

	/**
	 * Рекурсивное удаление дректории
	 *
	 * @param string $dirname
	 */
	public static function unlinkDir($dirname) {

		$files = glob($dirname . '/*', GLOB_NOSORT);

		foreach ($files as $file) {

			if (is_dir($file)) {
				unlinkDir($file);
			}
			else {
				unlink($file);
			}

		}

		rmdir($dirname);

	}

	/**
	 * Копирование папки
	 *
	 * @param string $path
	 * @param string $dest
	 * @return bool
	 */
	public static function Copy($path, $dest) {

		if (is_dir($path)) {

			@mkdir($dest, 0777, true);
			$objects = scandir($path);

			if (sizeof($objects) > 0) {

				foreach ($objects as $file) {

					if ($file == "." || $file == "..")	continue;

					if (is_dir($path . DS . $file )){
						self::Copy($path . DS . $file, $dest . DS . $file);
					}
					else {
						copy($path . DS . $file, $dest . DS . $file);
					}

				}

			}

			return true;

		}
		elseif (is_file($path)) {

			$copyToDir = dirname($dest);

			if (!is_dir($copyToDir)) {
				@mkdir($copyToDir, 0777, true);
			}

			return copy($path, $dest);

		}
		else {
			return false;
		}
	}

}
