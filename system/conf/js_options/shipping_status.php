<?php
/*
#   --------------------------------------------------------------
#   shipping_status.js 2012-05-22 tb@gambio
#   Gambio GmbH
#   http://www.gambio.de
#   Copyright (c) 2012 Gambio GmbH
#   Released under the GNU General Public License (Version 2)
#   [http://www.gnu.org/licenses/gpl-2.0.html]
#   --------------------------------------------------------------
*/

    $array["shipping_status"] = array();
    
    $t_sql = '
        SELECT shipping_status_id, shipping_status_name, shipping_status_image
        FROM
            shipping_status
    ';
    
    $result = xtc_db_query($t_sql);
    while($row = xtc_db_fetch_array($result)){
        $array["shipping_status"][$row['shipping_status_id']] = array();
        $array["shipping_status"][$row['shipping_status_id']]['shipping_status_name'] = $row['shipping_status_name'];
        $array["shipping_status"][$row['shipping_status_id']]['shipping_status_image'] = $row['shipping_status_image'];
        
    }