<?php

/**
 * Class Actindo_Connector_Service_Actindo
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Actindo_Connector_Service_Actindo
{
    /**
     * returns the connector Version, shop version and hardware capabilities
     * @param string $params
     * @return array containing several information blocks
     */
    public function get_connector_version($params) {
        return actindo_get_connector_version(array('params'=>$params));
    }
    /**
     * returns the server time based on different time frames
     * @param string $params
     * @return array
     */
    public function get_time($params) {
        return actindo_get_time(array('params'=>$params));
    }
    /**
     * method used for pinging the server
     * @param string $params
     * @return array
     */
    public function ping($params) {
        return actindo_ping(array('params'=>$params));
    }
}
