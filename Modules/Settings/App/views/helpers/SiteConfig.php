<?php

/**
 * Доступ к SiteConfig из view
 *
 */
class Zetta_View_Helper_SiteConfig extends Zend_View_Helper_Abstract
{
    public function siteConfig($key)
    {
        return Zend_Registry::get('SiteConfig')->$key;
    }
}
