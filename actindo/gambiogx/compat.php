<?php

/**
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
function act_get_attributes_model( $product_id, $attribute_name, $options_name, $language='' )
{
  if( act_shop_is(SHOP_TYPE_XTCOMMERCE) || act_shop_is(SHOP_TYPE_GAMBIOGX) )
  {
    require_once (DIR_FS_INC.'xtc_get_attributes_model.inc.php');
    return xtc_get_attributes_model( $product_id, $attribute_name, $options_name, $language );
  }
  else
  {
    // now comes the tricky part...
    $options_value_id_query=act_db_query("SELECT
pa.products_attributes_id
FROM
".TABLE_PRODUCTS_ATTRIBUTES." pa
Inner Join ".TABLE_PRODUCTS_OPTIONS." po ON po.products_options_id = pa.options_id
Inner Join ".TABLE_PRODUCTS_OPTIONS_VALUES." pov ON pa.options_values_id = pov.products_options_values_id
WHERE
po.language_id = pov.language_id AND
po.products_options_name = '".$options_name."' AND
pov.products_options_values_name = '".$attribute_name."'");


    $options_attr_data = act_db_fetch_array($options_value_id_query);
    return '-'.$options_attr_data['products_attributes_id'];
  }
}


function act_get_geo_zone_code( $country_id )
{
  $geo_zone_query = act_db_query("select geo_zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where zone_country_id = '" . (int)$country_id . "'");
  $geo_zone = act_db_fetch_array($geo_zone_query);
  return $geo_zone['geo_zone_id'];
}
