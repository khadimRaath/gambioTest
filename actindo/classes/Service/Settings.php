<?php

/**
* Class Actindo_Connector_Service_Settings
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
*/
class Actindo_Connector_Service_Settings
{
    /**
     * Method to build shop settings
     * @param string $vars an associative array with information from actindo that may be relevant for the connector
     * @return array
     */
    public function get($vars) {
        return settings_get(array('params'=>$vars));
    }
}
