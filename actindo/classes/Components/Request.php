<?php
require_once('Zend/XmlRpc/Request/Http.php');
/**
 * Actindo Faktura/WWS Connector
 * class for handling xmlrpc requests
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Actindo_Connector_Components_Request extends Zend_XmlRpc_Request_Http{
    protected $compression = null;
    /**
     * Create a new XML-RPC request
     * @param string $method (optional)
     * @param array $params  (optional)
     * @param array $server (optional) $_SERVER array (used to detect content compression)
     */
    public function __construct($method = null, $params = null, $server = null) {
        if($server !== null) {
            if(isset($server['HTTP_CONTENT_ENCODING'])
                && strtolower($server['HTTP_CONTENT_ENCODING']) == 'gzip')
            {
                $this->compression = 'gzip';
            }
        }

        parent::__construct($method, $params);
    }
    /**
     * load XML data into internal object
     * extends the parent function to handle compressed xml documents
     * @param string $request
     * @uses parent::loadXML
     * @return bool
     */
    public function loadXML($request) {
        switch($this->compression) {
            case 'gzip':
                if(function_exists('gzinflate') && ($inflated = @gzinflate(substr($request, 10)))) {
                    $request = $inflated;
                }
                break;
        }

        return parent::loadXML($request);
    }
}
