<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */


class ShopgateShippingModel
{
    
    /**
     * read all shipping module data from database
     *
     * @return mixed
     */
    public function getShippingCountriesFromConstants()
    {
        $shippingQuery
            = "SELECT c.configuration_value AS 'countries' 
               FROM " . TABLE_CONFIGURATION . " AS c 
               WHERE c.configuration_key LIKE 'MODULE_SHIPPING_%_COUNTRIES_%' AND c.configuration_value != ''";
        
        return xtc_db_query($shippingQuery);
    }
    
    /**
     * read the shipping configuration data from database regarding shipping class name
     *
     * @param $className
     *
     * @return array
     */
    public function getShippingConfigurationValuesByClassName($className)
    {
        $query          = "SELECT c.configuration_key, c.configuration_value FROM "
            . TABLE_CONFIGURATION . " AS c WHERE configuration_key like \"MODULE_SHIPPING_"
            . strtoupper($className) . "%\" ;";
        $result         = xtc_db_query($query);
        $shippingConfig = array();
        
        while ($config = xtc_db_fetch_array($result)) {
            $shippingConfig[$config["configuration_key"]] = $config["configuration_value"];
        }
        
        return $shippingConfig;
    }
} 
