<?php
/* --------------------------------------------------------------
   start.php 2016-08-30
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
   (c) 2000-2001 The Exchange Project
   (c) 2002-2003 osCommerce coding standards (a typical file) www.oscommerce.com
   (c) 2003      nextcommerce (start.php,1.5 2004/03/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: start.php 1235 2005-09-21 19:11:43Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require ('includes/application_top.php');

include(DIR_WS_MODULES.FILENAME_SECURITY_CHECK);

	require(DIR_FS_ADMIN . 'includes/gm/classes/GMStart.php');

	$gmStart = new GMStart();

	$gm_listing	= $gmStart->getTopListing();

if(!isset($jsEngineLanguage))
{
	$jsEngineLanguage = array();
}
$languageTextManager = MainFactory::create_object('LanguageTextManager', array(), true);
$jsEngineLanguage['start'] = $languageTextManager->get_section_array('start');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="x-ua-compatible" content="IE=edge">
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_ADMIN; ?>html/assets/styles/legacy/stylesheet.css">
		<script type="text/javascript" src="html/assets/javascript/legacy/gm/LoadUrl.js"></script>
	</head>
	<body topmargin="0" leftmargin="0" bgcolor="#FFFFFF">

		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<script type="text/javascript">

			$(document).ready(function(){
				var coo_load_url = new LoadUrl();
				coo_load_url.load_url('LoadNews');
			});

		</script>

		<table border="0" cellspacing="2" cellpadding="0">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top" height="100%">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="0" class="columnLeft">
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
					</table>
				</td>
				<td class="boxCenter no-wrap" width="100%" valign="top" height="100%">
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<!--
							COMPATIBILITY DASHBOARD
						-->
						<tr style="background-color: #F4F4F4">
							<td>
								<div class="dashboard-title"><?php echo HEADING_DASHBOARD; ?></div>
								<div class="dashboard-content breakpoint-large box-center-wrapper no-wrap" style="margin-bottom: 0; margin-top: 0;"></div>
							</td>
						</tr>
						
						<?php if ($_SESSION['customers_status']['customers_status_id'] == '0'  && ($admin_access['gm_counter'] == '1')) { ?>
						<tr style="background-color: #F4F4F4">
							<td>
								<div class="breakpoint-large box-center-wrapper no-wrap charts" style="margin-top: 0">
								    <?php require DIR_FS_ADMIN . 'html/compatibility/dashboard.php' ?>
								</div>
							</td>
						</tr>
						<?php } ?>
						<tr style="background-color: #FFF">
							<td colspan="2" style="border-top: 1px solid #E4E4E4;">
								<div class="breakpoint-large box-center-wrapper no-wrap news-wrapper">
									<!-- NEWS -->

									<div class="dashboard-title">
										<?php echo TITLE_NEWS; ?>
									</div>

									<div id="content_loader">
										<div id="url_loader">
											<img id="loading" src="../images/loading.gif" />
											<?php echo TEXT_NEWS_LOADING; ?>
										</div>
										<div class="load_url">
											<?php 
												include DIR_FS_CATALOG . 'release_info.php';
												echo base64_encode('http://news.gambio-support.de/news.php?category=gx2-5&get_news_for_version=' . rawurlencode($gx_version)); 
											?>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/ui/datepicker/jquery-ui-datepicker.js"></script>
		<script type="text/javascript" src="<?php echo DIR_WS_ADMIN; ?>html/assets/javascript/legacy/gm/gm_counter.js"></script>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
