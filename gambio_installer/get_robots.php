<?php
/* --------------------------------------------------------------
   get_robots.php 2013-04-02 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2013 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

require_once('../admin/includes/configure.php');
require_once('../gm/inc/get_robots.php');

if($_GET['download'] == 'robot') {
	$t_content_links = "\nDisallow: {PATH}shop_content.php?coID=2\nDisallow: {PATH}info/privacy-notice.html\nDisallow: {PATH}info/privatsphaere-und-datenschutz.html\nDisallow: {PATH}shop_content.php?coID=3\nDisallow: {PATH}info/conditions-of-use.html\nDisallow: {PATH}info/allgemeine-geschaeftsbedingungen.html\nDisallow: {PATH}shop_content.php?coID=4\nDisallow: {PATH}info/imprint.html\nDisallow: {PATH}info/impressum.html\nDisallow: {PATH}shop_content.php?coID=9\nDisallow: {PATH}info/widerrufsrecht.html\nDisallow: {PATH}info/withdrawal.html";	
	get_robots(DIR_WS_CATALOG, $t_content_links);
}
