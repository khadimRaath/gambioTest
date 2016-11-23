<?php
/* --------------------------------------------------------------
   accounting.php 2016-07-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE.
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommercecoding standards www.oscommerce.com
   (c) 2003	 nextcommerce (accounting.php,v 1.27 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: accounting.php 1167 2005-08-22 00:43:01Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

// Add info message about "admin.php" which is required to be enabled for every user.
$languageTextManager = MainFactory::create('LanguageTextManager', 'accounting', $_SESSION['language_id']);
$GLOBALS['messageStack']->add($languageTextManager->get_text('HTTP_SERVICE_INFORMATION_MESSAGE'), 'info');

if ($_GET['action'])
{
	switch ($_GET['action'])
	{
		case 'save':
			if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
			{
				// reset values before writing
				$admin_access_query = xtc_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
				$admin_access = xtc_db_fetch_array($admin_access_query);

				$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
				$columnsQuery = $db->query('SHOW COLUMNS FROM admin_access');
				$fields       = $columnsQuery->result_array();
				$columns      = $columnsQuery->num_rows();

				for ($i = 0; $i < $columns; $i++)
				{
					$field = $fields[$i]['Field'];
					if ($field!='customers_id')
					{
						xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS ." SET " . $field . "=0 where customers_id='" . (int)$_GET['cID'] . "'");
					}
				}

				$access_ids='';
				if(isset($_POST['access']))
				{
					foreach($_POST['access'] as $key)
					{
						xtc_db_query("UPDATE " . TABLE_ADMIN_ACCESS . " SET " . $key . "=1 where customers_id='" . (int)$_GET['cID'] . "'");
					}
				}
				xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
			}
		break;
	}
}
if ($_GET['cID'] != '')
{
	if ($_GET['cID'] == 1)
	{
		xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
	}
	else
	{
		$allow_edit_query = xtc_db_query("select customers_status, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$_GET['cID'] . "'");
		$allow_edit = xtc_db_fetch_array($allow_edit_query);
		if ($allow_edit['customers_status'] != 0 || $allow_edit == '')
		{
			xtc_redirect(xtc_href_link(FILENAME_CUSTOMERS, 'cID=' . (int)$_GET['cID'], 'NONSSL'));
		}
	}
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="x-ua-compatible" content="IE=edge">
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
	<title><?php echo TITLE; ?></title>
	<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
	<tr>
		<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
				<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			</table>
		</td>
    	<td class="boxCenter" width="100%" valign="top">
			<!-- Page Heading -->
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/kunden.png)">
				<?php echo TEXT_ACCOUNTING.' '.$allow_edit['customers_lastname'].' '.$allow_edit['customers_firstname']; ?>
			</div>

			<!-- Content -->
			<div class="breakpoint-small" data-gx-controller="accounting/accounting_controller" data-gx-widget="checkbox">

				<?php
					echo xtc_draw_form('accounting', FILENAME_ACCOUNTING, 'cID=' . (int)$_GET['cID']  . '&action=save', 'post', 'enctype="multipart/form-data"');
					echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token());
				?>

				<table border="0" cellpadding="0" cellspacing="0" id="accounting_table" class="gx-compatibility-table" style="width: 100%;">
					<!-- Table Header -->
					<thead>
						<tr class="dataTableHeadingRow">
							<th class="dataTableHeadingContent" style="padding-left: 24px;">
								<?php echo TEXT_ACCESS; ?>
							</th>
							<th class="dataTableHeadingContent">
								<input data-single_checkbox="true" type="checkbox" name="check_all" value="" id="check_all" />
								<label for="check_all" class="cursor-pointer"><strong><?php echo TEXT_ALLOWED; ?></strong></label>
							</th>
						</tr>
					</thead>

					<!-- Table Body -->
					<tbody>
						<?php
							$required_fields = [
								'admin', 
							    'start', 
							    'request_port', 
							    'admin_javascript'
							]; 
						
							$admin_access		= '';
							$customers_id 		= xtc_db_prepare_input($_GET['cID']);
							$admin_access_query = xtc_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
							$admin_access 		= xtc_db_fetch_array($admin_access_query);

							$group_query = xtc_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = 'groups'");

							if ($admin_access == '') {
								xtc_db_query("INSERT INTO " . TABLE_ADMIN_ACCESS . " (customers_id) VALUES ('" . (int)$_GET['cID'] . "')");
								$admin_access_query = xtc_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = '" . (int)$_GET['cID'] . "'");
								$group_query 		= xtc_db_query("select * from " . TABLE_ADMIN_ACCESS . " where customers_id = 'groups'");
								$admin_access		= xtc_db_fetch_array($admin_access_query);
							}

							$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
							$columnsQuery = $db->query('SHOW COLUMNS FROM admin_access');
							$fields       = $columnsQuery->result_array();
							$columns      = $columnsQuery->num_rows();

							for ($i = 0; $i < $columns; $i++) {
								$field = $fields[$i]['Field'];

								if ($field != 'customers_id') {
									$checked = '';

									if ($admin_access[$field] == '1') $checked='checked';
									
									if (in_array($field, $required_fields)) {
										$checked = 'checked'; 
										$displayedField = '<strong>' . $field . ' *</strong>';
									} else {
										$displayedField = $field;
									}

									echo '
										<tr class="dataTableRow">
											<td width="50%" class="dataTableContent configuration-label">
												'.$displayedField.'
											</td>
											<td width="50%" class="dataTableContent">
												<input type="checkbox" name="access[]" value="'.$field.'"'.$checked.'>
											</td>
										</tr>
									';
								}
							}

						?>
					</tbody>
				</table>
				<br>
				<div class="pull-right">
					<input type="submit" class="button" value="<?php echo BUTTON_SAVE; ?>">
				</div>

				</form>

			</div>
		</td>
	</tr>
</table>

<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>

</body>
</html>

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
