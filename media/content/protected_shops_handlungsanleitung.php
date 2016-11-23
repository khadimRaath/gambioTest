<?php
/* --------------------------------------------------------------
	protected_shops_handlungsanleitung.php 2014-02-28_1207 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------


	based on:
	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
	(c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
	(C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
	(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

	Released under the GNU General Public License
	---------------------------------------------------------------------------------------*/

$t_document_file = dirname(__FILE__).'/ps_handlungsanleitung.html';

if(defined('DIR_FS_CATALOG'))
{
	include $t_document_file;
}
else
{
?>
<!DOCTYPE html>
<html>
<head>
	<title>Protected Shops</title>
	<style>
	body { font: 0.8em sans-serif; }
	</style>
</head>
<body>
	<?php include $t_document_file; ?>
</body>
</html>
<?php
}
