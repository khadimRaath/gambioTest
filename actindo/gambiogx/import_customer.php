<?php

/**
 * import customer cid
 *
 * actindo Faktura/WWS connector
 *
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author Christopher Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright (c) 2007, Patrick Prasse (Schneebeerenweg 26, D-85551 Kirchheim, GERMANY, pprasse@actindo.de)
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
*/
function import_customer_set_deb_kred_id( $params )
{
      if( !parse_args($params,$ret) )
      {
            return $ret;
      }
      $customer_id = $params['userId'];
      $deb_kred_id = $params['customerNumber'];
      if( !$customer_id || !$deb_kred_id )
      {
        return resp(array( 'ok'=>FALSE, 'errno'=>EINVAL ));
      }
      if( act_shop_is(SHOP_TYPE_OSCOMMERCE) )
      {
          return resp(array('ok' => FALSE, 'errno' => ENOSYS));
      }
      $res = act_db_query( "UPDATE ".TABLE_CUSTOMERS." SET `customers_cid`=".(int)$deb_kred_id." WHERE `customers_id`=".(int)$customer_id );
      if( !$res )
      {
        return resp(array( 'ok'=>FALSE, 'errno'=>EIO ));
      }
      return resp(array( 'ok'=>TRUE ));
}

