<?php

use MatthiasMullie\Minify;

class Zetta_View_Helper_HeadScript extends Zend_View_Helper_HeadScript
{
    use Zetta_View_Helper_Trait_Head;

    /**
     * Minify css files and implode to one file
     *
     * @return self
     */
    public function minify($suffix = '')
    {
        $filesToMinify = array();
        $array = $this->getContainer();

        while (list($index, $item) = each($array)) {
            if (isset($item->attributes['src']) && false != $this->_getLocalPathFile($item->attributes['src'])) {
                $array->offsetUnset($index);
                $filesToMinify[] = $item->attributes['src'];
                reset($array);
            }
        }

        $hash = crc32(implode(',', $filesToMinify));
        $cacheFileName = 'jsmin' . $suffix . self::$DERIMITER . $hash . '.js';
        $cacheFilePath = self::$TEMP_DIR . DS . $cacheFileName;

        $this->appendFile($cacheFilePath, 'text/javascript', array('async' => 'async'));

        if (!is_readable($savePath = FILE_PATH . $cacheFilePath)) {
            $content = '';

            foreach ($filesToMinify as $file) {
                $content .= $this->_getFileContent($file);
            }

            $minifier = new Minify\JS($content);
            $minifiedData = $minifier->minify();

            $this->_clean($cacheFileName);
            file_put_contents($savePath, $minifiedData);
        }

        return $this;
    }
}
