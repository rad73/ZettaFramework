<?php

/**
 * Генерация путей к thumbs
 *
 * @param string $path
 * @param array(w, h) $sizes
 * @param bool $watermarked
 * @return string
 */
class Zetta_View_Helper_ThumbUrl extends Zend_View_Helper_Abstract
{
    public function thumbUrl($path, $sizes = array(200, 100), $watermarked = false)
    {
        return System_Functions::getThumbUrl($path, $sizes, $watermarked);
    }
}
