<?php

use Leafo\ScssPhp;
use MatthiasMullie\Minify;

class Zetta_View_Helper_HeadLink extends Zend_View_Helper_HeadLink {

	const TEMP_DIR = '/public/.compiled';

	const DERIMITER = '__';


	public function createData(array $attributes) {

		$item = parent::createData($attributes);
		return $this->_preprocessCSS($item);

	}

	/**
	 * Minify css files and implode to one file
	 * 
	 * @return self
	 */
	public function minify() {

		$filesToMinify = array();
		$array = $this->getContainer();

		while (list($index, $item) = each($array)) {

            if ($item->rel == 'stylesheet' && false != $this->_getLocalPathFile($item->href)) {

            	$array->offsetUnset($index);
            	$filesToMinify[] = $item->href;
            	reset($array);

            }

        }

        $hash = crc32(implode(',', $filesToMinify));
        $cacheFileName = 'minify' . self::DERIMITER . $hash . '.css';
        $cacheFilePath = self::TEMP_DIR . DS . $cacheFileName;

        $this->appendStylesheet($cacheFilePath);

        if (!is_readable($savePath = FILE_PATH . $cacheFilePath)) {

        	$content = '';

        	foreach ($filesToMinify as $file) {
        		$content .= $this->_getFileContent($file);
        	}

			$minifier = new Minify\CSS($content);
			$minifiedData = $minifier->minify();

			$this->_clean($cacheFileName);
			file_put_contents($savePath, $minifiedData);

        }

		return $this;        

	}

	/**
	 * After output links clean storage
	 * 
	 * @param  stdClass $item
     * @return string
	 */
	public function toString($indent = null) {
		
		$return = parent::toString($indent);
		$this->getContainer()->exchangeArray(array());

		return $return;

	}

	/**
	 * Preprocess sass, scss and less files
	 * 
	 * @param stdClass $item
	 * @retrun stdClass
	 */
	protected function _preprocessCSS(stdClass $item) {

		if (($isLess = $this->_isLess($item->href)) || ($isScss = $this->_isScss($item->href))) {

			$compiledFileName = $this->_getCompiledFileName($item->href);
			$nonCompiledFileName = $item->href;
			$nonCompiledDirName = $this->_getFileDirectory($item->href);
			$item->href = self::TEMP_DIR . DS . $compiledFileName;

			if (!is_readable($savePath = FILE_PATH . $item->href)) {

				switch(true) {
					case $isLess:
							$compiler = new lessc();
							$compiler->setImportDir($nonCompiledDirName);
						break;

					case $isScss:
							$compiler = new ScssPhp\Compiler();
							$compiler->setLineNumberStyle(ScssPhp\Compiler::LINE_COMMENTS);
							$compiler->setImportPaths($nonCompiledDirName);
						break;
				}

				$compiled = $compiler->compile($this->_getFileContent($nonCompiledFileName));
				$this->_clean($compiledFileName);
				file_put_contents($savePath, $compiled);

			}

		}

		return $item;

	}

	/**
	 * Check file is less?
	 * 
	 * @param string $path
	 * @return bool
	 */
	protected function _isLess($path) {

		if (stristr($path, '.less')) {
			return true;
		}

	}

	/**
	 * Check file is scss?
	 * 
	 * @param string $path
	 * @return bool
	 */
	protected function _isScss($path) {

		if (stristr($path, '.scss') || stristr($path, '.sass')) {
			return true;
		}

	}

	/**
	 * Check file is locall?
	 * 
	 * @param string $path
	 * @return bool
	 */
	protected function _getLocalPathFile($path) {

		$parseUrl = parse_url(FILE_PATH . $path);
		return is_readable($parseUrl['path']) ? realpath($parseUrl['path']) : false;

	}

	/**
	 * Get directory local file
	 * 
	 * @param string $pathFile
	 * @return string
	 */
	protected function _getFileDirectory($pathFile) {

		if ($localPathFile = $this->_getLocalPathFile($pathFile)) {
			return dirname($localPathFile);
		}

	}

	/**
	 * Get content file
	 * 
	 * @param string $pathFile
	 * @return string
	 */
	protected function _getFileContent($pathFile) {

		$data = false;

		if ($localPathFile = $this->_getLocalPathFile($pathFile)) {
			$data = file_get_contents($localPathFile);
		}
		else {

			$url = preg_match('/https?:/', $pathFile) ? $pathFile : HTTP_HOST . $pathFile;

			$client = new Zend_Http_Client($url, array(
				'maxredirects' => 0,
				'timeout'      => 5));

			$response = $client->request();

			if (200 == $response->getStatus()) {
				$data = $response->getBody();
			}

		}

		return $data;

	}

	/**
	 * Generate new compiled file name
	 * 
	 * @param string $pathFile
	 * @return string
	 */
	protected function _getCompiledFileName($pathFile) {


		if ($localPathFile = $this->_getLocalPathFile($pathFile)) {

			$fileName = basename($localPathFile);
			$hash = filemtime($localPathFile);

		}
		else {
			
			$parseUrl = parse_url($pathFile);
			$fileName = basename($parseUrl['path']);
			$hash = crc32($pathFile);

		}

		return $fileName . self::DERIMITER . $hash . '.css';

	}

	/**
	 * Clean old compiled files
	 * 
	 * @param string $fileName
	 * @return string
	 */
	protected function _clean($fileName) {

		$files = glob(FILE_PATH . self::TEMP_DIR . DS . explode(self::DERIMITER, $fileName)[0] . '*');

		if (sizeof($files)) {
			
			foreach ($files as $file) {
				unlink($file);
			}

		}

	}

}