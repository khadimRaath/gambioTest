<?php
/* -----------------------------------------------------------------------------------------
   $Id: xtc_sqlSafeString.inc.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   by Mario Zanier for neXTCommerce
   
   based on:
   (c) 2003	 nextcommerce (xtc_sqlSafeString.inc.php,v 1.4 2003/08/13); www.nextcommerce.org

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

  function xtc_sqlSafeString($param) {
    // Hier wird wg. der grossen Verbreitung auf MySQL eingegangen
    return (NULL === $param ? "NULL" : '"' . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $param) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . '"');
  }
?>