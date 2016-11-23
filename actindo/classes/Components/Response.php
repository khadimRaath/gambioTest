<?php
require_once('Zend/XmlRpc/Response.php');
/**
 * Actindo Faktura/WWS Connector
 * import product
 * Imports Products from Actindo ERP to Shop
 * Extends the Product Export
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */

class Actindo_Connector_Components_Response extends Zend_XmlRpc_Response{
    /**
     * Override __toString() to send HTTP Content Type Header
     */
    public function __toString(){
        /**
         * removed according to customer notes about double headers with fcgi
         *if (!headers_sent()) {
         *   header('Content-Type: text/xml; charset=' . strtolower($this->getEncoding()));
         * }
         */
        return parent::__toString();
    }
}
