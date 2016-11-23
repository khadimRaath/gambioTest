<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty htmlentities_wrapper modifier plugin
 *
 * Type:     modifier<br>
 * Name:     htmlentities_wrapper<br>
 * @param string
 */

require_once( '../inc/htmlentities_wrapper.inc.php' );

function smarty_modifier_htmlentities_wrapper( $string )
{
    return htmlentities_wrapper( $string );
}