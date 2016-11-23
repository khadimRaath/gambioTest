<?php
/* --------------------------------------------------------------
   findologic_config.php 2015-09-28 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------
*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('admin.php?do=ModuleCenter');

defined('GM_HTTP_SERVER') or define('GM_HTTP_SERVER', HTTP_SERVER);
define('PAGE_URL', GM_HTTP_SERVER.DIR_WS_ADMIN.basename(__FILE__));

function replaceTextPlaceholders($content) {
	$txt = new LanguageTextManager('findologic', $_SESSION['languages_id']);
	while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
		$replacement = $txt->get_text($matches[1]);
		if(empty($replacement)) {
			$replacement = $matches[1];
		}
		$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
	}
	return $content;
}

function getCustomersGroups() {
	$query = "SELECT cs.* FROM `customers_status` cs join languages l on l.languages_id = cs.language_id and l.directory = '".xtc_db_input($_SESSION['language'])."'";
	$groups = array();
	$result = xtc_db_query($query);
	while($row = xtc_db_fetch_array($result)) {
		$groups[] = $row;
	}
	return $groups;
}

function getLanguageCodes()
{
	$query = 'SELECT `code` FROM `languages` WHERE `status` = 1';
	$result = xtc_db_query($query);
	$lcodes = array();
	while($row = xtc_db_fetch_array($result))
	{
		$lcodes[] = $row['code'];
	}
	return $lcodes;
}

$cfg = array(
	'fl_use_search' => '0',
	'fl_shop_id' => '',
	'fl_shop_url' => HTTP_SERVER.DIR_WS_CATALOG,
	'fl_service_url' => '',
	'fl_net_price' => '0',
	'fl_export_filename' => 'findologic.csv',
	'fl_customer_group' => 1, // Gaeste
);

$t_langcodes = getLanguageCodes();
foreach($t_langcodes as $lc)
{
	$cfg['fl_shop_id_'.$lc] = '';
}

foreach($cfg as $key => $value) {
	$cfg_value = gm_get_conf(strtoupper($key));
	if(!empty($cfg_value)) {
		$cfg[$key] = $cfg_value;
	}
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	foreach($cfg as $key => $value) {
		$confkey = strtoupper($key);
		switch($key) {
			case 'fl_use_search':
			case 'fl_net_price':
				gm_set_conf($confkey, isset($_POST[$key]) ? '1' : '0');
				break;
			default:
				if(isset($_POST[$key])) {
					gm_set_conf($confkey, trim(xtc_db_input($_POST[$key])));
				}
		}
	}
	xtc_redirect(PAGE_URL);
}

ob_start();
?>
<!DOCTYPE html>
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
		<style>
			td.boxCenter {font: 0.8em sans-serif; padding: 1em; }
			td.boxCenter h1 {color: #0264BB; margin: 1ex 0; }
			dl.form {overflow: auto; position: relative; }
			dl.form > dt, dl.form > dd {margin: 2px 0; }
			dl.form > dt {float: left; clear: left; width: 25% }
			dl.form > dd {float: left; width: 75%; margin: 0; }
			dl.form > dt label {display: inline-block; width: 200px }
			dl.form > dt label:after {content: ':'; }
			dl.form input[type="text"] {width: 25em; }
			dl.form input[type="checkbox"] { vertical-align: middle; }

			form.bluegray {font-size: 0.9em; }
			form.bluegray fieldset {border: none; padding: 0; margin: 1ex 0 0 0; }
			form.bluegray legend {font-weight: bolder; font-size: 1.4em; background: #585858; color: #FFFFFF; padding: .2ex 0.5%; width: 99%; }
			form.bluegray dl.adminform {margin: 0; }
			form.bluegray dl.adminform dt, form.bluegray dl.adminform dd {line-height: 20px; padding: 3px 0; margin: 0; }
			form.bluegray dl.adminform dt {width: 18%; float: left; font-weight: bold; padding: 2px;}
			form.bluegray dl.adminform dd {border-bottom: 1px dotted rgb(90, 90, 90); width: 80%; float: none; padding-left: 20%; background-color: #F7F7F7; }
			form.bluegray dl.adminform dd:nth-child(4n) {background: #D6E6F3; }

		</style>
	</head>
	<body>
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					<!-- left_navigation_eof //-->
				</td>

				<!-- body_text //-->

				<td class="boxCenter" width="100%" valign="top">
					<h1>Findologic Konfiguration</h1>
					<form class="adminform bluegray" action="<?php echo PAGE_URL ?>" method="POST">
						<dl class="form adminform">
							<dt><label for="use_search">##use_search</label></dt>
							<dd>
								<input id="use_search" name="fl_use_search" type="checkbox" <?php echo $cfg['fl_use_search'] ? 'checked="checked"' : '' ?>>
								<label for="use_search">##use_search</label>
							</dd>
							<?php foreach($t_langcodes as $lc): ?>
								<dt><label for="shop_id">##shop_id <?php echo strtoupper($lc) ?></label></dt>
								<dd>
									<input id="shop_id" name="fl_shop_id_<?php echo $lc ?>" type="text" value="<?php echo $cfg['fl_shop_id_'.$lc] ?>">
								</dd>
							<?php endforeach ?>
							<dt><label for="shop_url">##shop_url</label></dt>
							<dd>
								<input id="shop_url" name="fl_shop_url" type="text" value="<?php echo $cfg['fl_shop_url'] ?>">
							</dd>
							<dt><label for="service_url">##service_url</label></dt>
							<dd>
								<input id="service_url" name="fl_service_url" type="text" value="<?php echo $cfg['fl_service_url'] ?>">
							</dd>
							<dt><label for="export_filename">##export_filename</label></dt>
							<dd>
								<input id="export_filename" name="fl_export_filename" type="text" value="<?php echo $cfg['fl_export_filename'] ?>">
							</dd>
							<dt><label for="net_price">##net_price</label></dt>
							<dd>
								<input id="net_price" name="fl_net_price" type="checkbox" <?php echo $cfg['fl_net_price'] ? 'checked="checked"' : '' ?>>
								<label for="net_price">##net_price</label>
							</dd>
							<dt><label for="customer_group">##customer_group</label></dt>
							<dd>
								<select name="fl_customer_group">
									<?php foreach(getCustomersGroups() as $cgroup): ?>
										<option value="<?php echo $cgroup['customers_status_id'] ?>" <?php echo $cgroup['customers_status_id'] == $cfg['fl_customer_group'] ? 'selected="selected"' : '' ?>>
											<?php echo $cgroup['customers_status_name'] ?>
										</option>
									<?php endforeach ?>
								</select>
							</dd>

						</dl>
						<input type="submit" value="##save" class="button">
					</form>
				</td>
			</tr>
		</table><!-- body layout table -->
		
		<script>
		// test
		</script>
		
		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php
echo replaceTextPlaceholders(ob_get_clean());
require(DIR_WS_INCLUDES . 'application_bottom.php');
