<?php
/* --------------------------------------------------------------
   gm_emails.php 2016-02-04
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
   (c) 2002-2003 osCommerce(configuration.php,v 1.40 2002/12/29); www.oscommerce.com
   (c) 2003	 nextcommerce (configuration.php,v 1.16 2003/08/19); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: configuration.php 1125 2005-07-28 09:59:44Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application_top.php');

AdminMenuControl::connect_with_page('admin.php?do=Emails');

include_once(DIR_FS_CATALOG . 'gm/inc/gm_save_template_file.inc.php');

/**
 * @var MailTemplateManager $mailTemplateManager
 */
$mailTemplateManager = MainFactory::create_object('MailTemplateManager', array(
	MainFactory::create_object('MailTemplatesCacheBuilder')
));

$languageId = (int)$_SESSION['languages_id'];
if(isset($_GET['lang']))
{
	$languageId	= (int)$_GET['lang'];
}

$c_name = '';
if(isset($_GET['name']))
{
	$c_name	= basename($_GET['name']);
}

$contentType = '';

if(isset($_GET['gm_type']))
{
	$contentType = ($_GET['gm_type'] === 'txt') ? 'txt' : 'html';

	if(isset($_POST['go']))
	{
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
		
		switch($_POST['backup_action']) {
			case 'save':
				$mailTemplateManager->saveContent($c_name, $languageId, $contentType, gm_correct_config_tag(xtc_db_prepare_input($_POST['gm_emails_content'])));
				break;

			case 'save_backup':
				$mailTemplateManager->saveBackup($c_name, $languageId, $contentType, gm_correct_config_tag(xtc_db_prepare_input($_POST['gm_emails_content'])));
				break;

			case 'restore_backup':
				$mailTemplateManager->restoreBackup($c_name, $languageId, $contentType);
				break;

			case 'restore_original':
				$mailTemplateManager->restoreOriginal($c_name, $languageId, $contentType);
				break;
		}

		// clears the page and data cache
		$languageTextManager = MainFactory::create('LanguageTextManager');
		$getCacheText = function ($phraseName) use ($languageTextManager)
		{
			return $languageTextManager->get_text($phraseName, 'clear_cache', $_SESSION['language_id']);
		};

		$cacheControl = MainFactory::create_object('CacheControl');
		$cacheControl->reset_cache('modules');

		$messageStack->add($getCacheText('CLEAR_OUTPUT_CACHE_SUCCESS') . ' '
		                   . $getCacheText('CLEAR_DATA_CACHE_SUCCESS'), 'success');
	}
}

if($c_name !== '')
{
	$htmlContent = $mailTemplateManager->findContent($c_name, $languageId, 'html');
	$txtContent  = $mailTemplateManager->findContent($c_name, $languageId, 'txt');

	if($contentType === 'txt' && $txtContent === null && $htmlContent !== null)
	{
		$contentType = 'html';
	}
	elseif($contentType === 'html' && $htmlContent === null && $txtContent !== null)
	{
		$contentType = 'txt';
	}
	elseif($contentType === '' && $htmlContent !== null)
	{
		$contentType = 'html';
	}
	elseif($contentType === '' && $txtContent !== 'null')
	{
		$contentType = 'txt';
	}
}

$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<?php
if(preg_match('/MSIE [\d]{2}\./i', $_SERVER['HTTP_USER_AGENT']))
{
?>
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" />
<?php
}
?>
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<?php
if(!empty($c_name)){
?>
<script type="text/javascript" language="JavaScript">
	var t_gm_preview = false;

	function gm_emails_preview(gm_type){
		window.open('', 'gm_emails_preview', 'toolbar=0, width=800, height=600, scrollbars=yes');
		if(gm_type == 'txt') document.gm_emails_form.action = '<?php echo xtc_href_link('gm_emails_preview.php', 'name='.$c_name.'&type=txt'); ?>';
		else document.gm_emails_form.action = '<?php echo xtc_href_link('gm_emails_preview.php', 'name='.$c_name.'&type=html'); ?>';
		document.gm_emails_form.target = 'gm_emails_preview';
		document.gm_emails_form.submit();
		t_gm_preview = true;
	}

	function gm_emails_submit(){
		if((t_gm_preview && document.getElementById('backup_action').checked) || document.getElementById('backup_action').checked == false)
		{
			document.gm_emails_form.action = '<?php echo xtc_href_link('gm_emails.php', 'name='.$c_name.'&lang='.$languageId.'&gm_type='.$contentType); ?>';
			document.gm_emails_form.target = '';
			return true;
		}
		else
		{
			alert('<?php echo str_replace("'", "\'", GM_EMAILS_PREVIEW); ?>');
			return false;
		}
	}

</script>
<?php
}
?>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
			<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
			<!-- left_navigation //-->
			<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
			<!-- left_navigation_eof //-->
    	</table>
		</td>
		<!-- body_text //-->
    <td class="boxCenter" width="100%" valign="top">

			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/gambio.png)"><?php echo HEADING_TITLE; ?></div>
			<br />

		    <table>
			    <tr>
				    <td class="dataTableHeadingContent">
					    <a href="admin.php?do=Emails">
						    <?php echo $adminMenuLang->get_text('emails', 'emails'); ?>
					    </a>
				    </td>
				    <td class="dataTableHeadingContent">
					    <a href="configuration.php?gID=12">
						    <?php echo $adminMenuLang->get_text('BOX_CONFIGURATION_12'); ?>
					    </a>
				    </td>
				    <td class="dataTableHeadingContent">
					    <?php echo $adminMenuLang->get_text('BOX_GM_EMAILS'); ?>
				    </td>
			    </tr>
		    </table>

			<span class="main">
			<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr class="dataTableHeadingRow">
				 	<td class="dataTableHeadingContentText" style="border-right:0px"><?php echo GM_EMAILS_TITLE; ?></td>
				</tr>
			</table>

			<table style="border: 1px solid #dddddd" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr class="dataTableRow">
					<td style="font-size: 12px; padding: 0px 10px 11px 10px; text-align: justify">
						<br />

						<ul type="square">
						<?php
						//LOAD template list from database
						$templateNames = $mailTemplateManager->getAllTemplateNamesByLanguageId($languageId,
						                                                                       $coo_lang_file_master);

						foreach($templateNames as $templateName => $templateNameText)
						{
							echo '<li><a href="' . xtc_href_link('gm_emails.php', 'name=' . $templateName . '&lang=' . $languageId) . '">' . $templateNameText . '</a><br /></li>';
						}

						?>
						</ul>

					</td>
				</tr>
			</table>

			<?php
			if($c_name !== '' && $contentType !== '')
			{
			?>
				<br />
				<br />

				<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%" class="exclude-page-nav">
					<tr class="dataTableHeadingRow">
						<?php
						if($htmlContent !== null)
						{
							echo '<td class="dataTableHeadingContentText"
							    style="width:1%; padding-right:20px; white-space: nowrap"><a href="'
							     . $_SERVER['PHP_SELF'] . '?name=' . $c_name . '&lang=' . $languageId
							     . '&gm_type=html">HTML</a> </td>';
						}

						if($txtContent !== null)
						{
							echo '<td class="dataTableHeadingContentText"
							    style="width:1%; padding-right:20px; white-space: nowrap"><a href="'
							     . xtc_href_link('gm_emails.php',
							                     'name=' . $c_name . '&lang=' . $languageId . '&gm_type=txt')
							     . '">TEXT</a> </td>';
						}
						?>
						<td class="dataTableHeadingContentText" style="border-right:0px; text-align: right; padding-top: 6px; white-space: nowrap">
							<?php
							$gm_get_languages = xtc_db_query("SELECT DISTINCT 
																	l.languages_id, 
																	l.name, 
																	l.image, 
																	l.directory
																FROM 
																	languages l, 
																	email_templates_cache e
																WHERE 
																	e.name = '" . xtc_db_input($c_name) . "' AND
																	e.language_id = l.languages_id
																ORDER BY l.sort_order");
							while($row = xtc_db_fetch_array($gm_get_languages)){
								echo '&nbsp;&nbsp;<a style="a:hover { font-weight:bold; color:green; text-decoration:none; }" href="' . $_SERVER['PHP_SELF'] . '?name=' . $c_name . '&lang=' . $row['languages_id'] . '&gm_type=' . $contentType . '"><img src="'.DIR_WS_LANGUAGES.$row['directory'].'/admin/images/'.$row['image'].'" border="0" alt="' . $row['name'] . '" title="' . $row['name'] . '" /></a>';
								if($row['languages_id'] == $languageId) $lang_headline = $row['name'];
							}
							?>
						</td>
					</tr>
				</table>

				<table style="border: 1px solid #dddddd" border="0" cellpadding="0" cellspacing="0" width="100%">
					<tr class="dataTableRow">
						<td style="font-size: 12px; padding: 0px 10px 11px 10px; text-align: justify">

							<form action="<?php echo xtc_href_link('gm_emails.php', 'name='.$c_name.'&lang='.$languageId.'&gm_type='.$contentType); ?>" name="gm_emails_form" method="post">
								<?php
								echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token());
								
								if($contentType == 'html'){
								?>
								<br />
								<strong>HTML (<?php echo $coo_lang_file_master->get_text($c_name, 'gm_emails', $languageId) . ' - ' . $lang_headline; ?>)</strong>
								<br /><br />
								<div
									<?php
									if(USE_WYSIWYG == 'true')
									{
										echo 'data-gx-widget="ckeditor" data-ckeditor-height="400px"';
									}
									?>>
									<textarea id="gm_emails_content"
									          name="gm_emails_content"
									          class="wysiwyg"
									          style="width:100%; height:320px">
										<?php
										echo htmlspecialchars_wrapper($htmlContent);
										?>
									</textarea>
								</div>
								<br />
								<br />
								<?php } else{
								?>
								<br />
								<strong>TEXT (<?php echo $coo_lang_file_master->get_text($c_name, 'gm_emails', $languageId) . ' - ' . $lang_headline; ?>)</strong>
								<br /><br />
								<textarea id="gm_emails_content" name="gm_emails_content" wrap="" style="width:100%; height:320px"><?php
								echo htmlspecialchars_wrapper($txtContent);
								?></textarea>
								<br />
								<br />
								<?php } ?>

								<input type="radio" id="backup_action" name="backup_action" value="save" checked="checked"> 			<?php echo GM_EMAILS_SAVE ?><br>
								<input type="radio" name="backup_action" value="save_backup"> 			<?php echo GM_EMAILS_SAVE_BACKUP ?><br>
								<input type="radio" name="backup_action" value="restore_backup"> 		<?php echo GM_EMAILS_RESTORE_BACKUP ?><br>
								<input type="radio" name="backup_action" value="restore_original"> 	<?php echo GM_EMAILS_RESTORE_ORIGINAL ?><br />
								<br />
								<div class="gx-container">
									<input type="submit" class="button pull-right" name="go" value="OK" onclick="return gm_emails_submit()" />
									<input type="button" class="button pull-right" name="html" value="<?php echo GM_PREVIEW; ?>" onclick="gm_emails_preview('<?php echo $contentType; ?>')" />
								</div>
							</form>

						</td>
					</tr>
				</table>

			<?php
			}
			?>

			</span>

		</td>
<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
