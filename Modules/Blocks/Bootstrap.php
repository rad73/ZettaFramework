<?php

/**
 * Bootstrap для модуля Modules_Blocks
 *
 * @author Александр Хрищанович
 *
 */
class Modules_Blocks_Bootstrap extends Zetta_BootstrapModules
{
    public function bootstrap()
    {
        parent::bootstrap();

        if (!System_Functions::tableExist('blocks')) {
            $routerBootstrap = new Modules_Router_Bootstrap();
            $routerBootstrap->bootstrap();
            
            $_migrationManager = new Modules_Dbmigrations_Framework_Manager();
            $_migrationManager->upTo('Modules_Blocks_Migrations_CreateBlocksTable');
        }

        $this->_registerPlugin();
    }

    protected function _registerPlugin()
    {
        Zend_Controller_Front::getInstance()
                ->registerPlugin(new Modules_Blocks_Plugin_Widget());
    }
}
