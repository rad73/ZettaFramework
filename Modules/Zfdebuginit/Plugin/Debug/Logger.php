<?php

class Modules_Zfdebuginit_Plugin_Debug_Logger extends ZFDebug_Controller_Plugin_Debug_Plugin implements ZFDebug_Controller_Plugin_Debug_Plugin_Interface
{
    /**
     * Contains plugin identifier name
     *
     * @var string
     */
    protected $_identifier = 'debug';

    /**
     * Create ZFDebug_Controller_Plugin_Debug_Plugin_Html
     *
     * @param string $tab
     * @param string $panel
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Gets identifier for this plugin
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->_identifier;
    }

    /**
     * Returns the base64 encoded icon
     *
     * @return string
     **/
    public function getIconData()
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAALbSURBVDjLfZHrM5RhGMb9A33uq7ERq7XsOry7y2qlPqRFDsVk0c4wZZgRckjJeWMQRhkju5HN6V1nERa7atJMw0zMNLUow1SOUby7mNmrdzWJWn245nnumee+7vt3PRYALI6SZy8fnt08kenu0eoW4E666v9+c6gQDQgYB2thJwGPNrfOmBJfK0GTSxT/qfP3/xqcNk3s4SX9rt1VbgZBs+tq9N1zSv98vp5fwzWG3BAUHGkg7CLWPToIw97KJLHBb3QBT+kMXq0zMrQJ0M63IbUoAuIozk2zBjSnyL3FFcImYt2HPAvVlBx97+pRMpoH1n1bRPT6oXmsEk7Fp+BYYA+HPCY9tYPYoDn32WlOo6eSh8bxUuQ+lyK9MwTJnZEQVhJgFdhBWn8Z3v42uv0NaM4dmhP8Bpc6oZJYuqTyh/JNMTJ7wpGo8oPkiRfyO4IxOXId1cOFcMixgyDUuu0QAq/e+RVRywUh54KcqEBGdxgSSF9IakUIb/DD24FIrOpaoO6PBSuDCWaazaZdsnXcoQyIR1xDaFMAigbjEN8sRpjCC0F1F9A3EIdlOofdzWlMtgfDN5sN28QTxpPxDNjEWv0J0O0BZ+uaSoqyoRRIHnsjUOGDqu4ETLRehGG5G4bPJVib6YHioRDiVPvjph5GtOXtfQN+uYuMU8RCdk8KguRiFHelobVBjJX3JAzz2dDe42JnlcSE/IxxvFoUaPYbuTK2hpFkiZqRClSRUnxUp2N7qQ7U9FVoZU7Qz6VgffYZBkuJxddlxLF/DExySGdqOLfsMag4j290cPpPSdj6EPJLOgmNUoo5TTnac9mlZg1MypJxx+a0Jdj+Wrk3fUt3hUbg7J3UbAyoLx3Q5rAWNVn2TLMG9HoL1MoMttfUMCzRGSy1HJAKuz+msDBWj6F0mxazBi8LOSsvZI7UaB6boidRA5lM9GfYYfiOLUU3Ueo0a0qdwqAGk61GfwIga508Gu46TQAAAABJRU5ErkJggg==';
    }

    /**
     * Gets menu tab for the Debugbar
     *
     * @return string
     */
    public function getTab()
    {
        return 'Debug (' . sizeof(Zetta_Log_Writers_Memory::getEvents()) . ')';
    }

    /**
     * Gets content panel for the Debugbar
     *
     * @return string
     */
    public function getPanel()
    {
        $events = Zetta_Log_Writers_Memory::getEvents();

        if (sizeof($events)) {
            $return = '<ol>';

            foreach ($events as $i => $event) {
                $return .= '<li>'
                    . ($i + 1) . '. '
                    . $this->_getIcon($event['priority']) . '&nbsp;'
                    . $event['message']
                    . (isset($event['file']) ? ' (in ' . $event['file'] . ':' .  $event['line'] . ')' : '')
                . '</li>' ;
            }

            return $return . '</ol>';
        }
    }

    protected function _getIcon($priority)
    {
        switch ($priority) {
            case Zend_Log::EMERG:
                $icoName = 'fa-fire';

                break;
            case Zend_Log::ALERT:
            case Zend_Log::WARN:
                $icoName = 'fa-exclamation-triangle';
                // no break
            case Zend_Log::CRIT:
            case Zend_Log::ERR:
                $icoName = 'fa-exclamation-circle';

                break;
            case Zend_Log::NOTICE:
            case Zend_Log::INFO:
                $icoName = 'fa-info-circle';

                break;
            case Zend_Log::DEBUG:
                $icoName = 'fa-bug';

                break;
        }


        return '<i class="fa ' . $icoName . '"></i>';
    }
}
