<?php
/* --------------------------------------------------------------
   withdrawals.php 2015-09-28 gm
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

// Include JS Language Vars
if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$jsEngineLanguage['admin_general'] = $languageTextManager->get_section_array('admin_general');

$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('withdrawal', $_SESSION['languages_id']));

$template = 'overview';
$coo_withdrawal_control = MainFactory::create_object( 'WithdrawalControl' );
$t_headline = $coo_language_text_manager->get_text('headline');

// delete withdrawal
if(isset($_POST['delete']) && isset($_POST['id']) && $_SESSION['coo_page_token']->is_valid($_POST['page_token']))
{
	$coo_withdrawal = MainFactory::create_object('WithdrawalModel', array((int)$_POST['id']));
	$coo_withdrawal->delete();
}

if(isset($_GET['id']) && (int)$_GET['id'] > 0)
{
	$coo_withdrawal = MainFactory::create_object('WithdrawalModel');
	$coo_withdrawal->load($_GET['id']);
	$coo_withdrawal_control->set_withdrawal_id((int)$_GET['id']);

	if(isset($_GET['action']))
	{
		$coo_withdrawal_control->set_action($_GET['action']);

		if($_GET['action'] == 'edit')
		{
			$template = 'details';
			$t_headline = $coo_language_text_manager->get_text('withdrawal_nr') . ' <span id="withdrawal_id">' . $coo_withdrawal->get_withdrawal_id() . '</span> - ' . $coo_withdrawal->get_withdrawal_date_formatted();
		}
	}
}

$t_page = 1;

if(isset($_GET['page']) && (int)$_GET['page'] > 1)
{
	$t_page = (int)$_GET['page'];
}

$coo_withdrawal_control->set_page($t_page);

$t_limit = $coo_withdrawal_control->get_limit();
$t_offset = ($t_page - 1) * $t_limit;
$coo_withdrawal_control->set_offset($t_offset);

if(isset($_GET['order_id']) && (int)$_GET['order_id'] > 0)
{
	$coo_withdrawal_control->set_order_id((int)$_GET['order_id']);
}

$t_template = $coo_withdrawal_control->get_template($template);

$t_redirect_url = $coo_withdrawal_control->get_redirect_url();
if(empty($t_redirect_url) === false && isset($_GET['search_result_page']) )
{
	xtc_redirect($t_redirect_url);
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">   
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/buttons.css">
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/withdrawals.css">
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/lightbox.css">
</head>
<body class="gx-compatibility" marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php');
?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%">
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)">
				<?php
					echo $t_headline;
				?>
			</div>
		</td>
      </tr>
      <tr>
        <td>
			<div id="withdrawals_wrapper">
				<?php
					echo $t_template;
				?>
			</div>
		</td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
