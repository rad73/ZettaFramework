<?php

/**
 * Bootstrap для модуля Modules_Accessusers
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Accessusers_Bootstrap extends Zetta_BootstrapModules
{
    public function bootstrap()
    {
        parent::bootstrap();

        Zend_Controller_Front::getInstance()
            ->registerPlugin(new Modules_Accessusers_Plugin_Widget(), 1000);
    }
}
