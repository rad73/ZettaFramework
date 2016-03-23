<?php

use Leafo\ScssPhp;
use MatthiasMullie\Minify;

class Zetta_View_Helper_HeadLink extends Zend_View_Helper_HeadLink {

	use Zetta_View_Helper_Trait_Head;

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
        $cacheFileName = 'jsmin' . self::$DERIMITER . $hash . '.css';
        $cacheFilePath = self::$TEMP_DIR . DS . $cacheFileName;

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
			$item->href = self::$TEMP_DIR . DS . $compiledFileName;

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

}