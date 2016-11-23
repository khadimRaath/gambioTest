<?php
/* --------------------------------------------------------------
   gm_backup_files_zip.php 2015-09-28 gm
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
require_once('includes/classes/pclzip.lib.php');

function bytestostring($size, $precision = 0) {
	$sizes = array('GB', 'MB', 'kB', 'B');
	$total = count($sizes);
	while($total-- && $size > 1024) $size /= 1024;
	return round($size, $precision).$sizes[$total];
}

function create_filelist_array($p_start_dir, $p_exclude_array = array(), $p_filelist_array = array()) {
	$t_filelist_array =& $p_filelist_array;

	if(is_readable($p_start_dir)) {
		$t_handle = opendir($p_start_dir);

		while($t_file = readdir($t_handle)) {
			$t_file_path = str_replace(DIR_FS_CATALOG, '', $p_start_dir . $t_file . '/');

			if($t_file != '.' && $t_file != '..' && !in_array($t_file_path, $p_exclude_array)) {
				if(is_dir($p_start_dir . $t_file)) {
					$t_filelist_array = create_filelist_array($p_start_dir . $t_file . '/', $p_exclude_array, $t_filelist_array);
				} else {
					$t_filelist_array[] = $p_start_dir . $t_file;
				}
			}
		}
		closedir($t_handle);
	}

	return $t_filelist_array;
}

$t_exclude_array = array();

if(file_exists(DIR_FS_ADMIN . 'includes/backup_blacklist.txt')
   && is_readable(DIR_FS_ADMIN . 'includes/backup_blacklist.txt')) {
	$t_handle = fopen(DIR_FS_ADMIN . 'includes/backup_blacklist.txt', 'r');
	while(!feof($t_handle))	{
		$t_blacklist = fgets($t_handle);
		$t_blacklist = trim($t_blacklist);
		$t_exclude_array[] = $t_blacklist;
	}
	fclose($t_handle);
}

if ($_GET['action']) {
	// Validate page token.
	$_SESSION['coo_page_token']->is_valid($_REQUEST['page_token']);
	
	switch ($_GET['action']) {
		case 'backup':
			$start_time = time();
			$t_filelist_array = create_filelist_array(DIR_FS_CATALOG, $t_exclude_array);

			if (!empty($t_filelist_array)) {
				$ziplist = implode(',', $t_filelist_array);

				$zip_filename = DIR_FS_BACKUP.date("Ymd_His").'.zip';
				$zip = new PclZip($zip_filename);
				$result=$zip->add($ziplist,PCLZIP_OPT_REMOVE_PATH,DIR_FS_DOCUMENT_ROOT);
				
				if ($result==0) {
					$messageStack->add_session($zip->errorInfo(), 'error');
				} else {
					$messageStack->add_session(SUCCESS_BACKUP_CREATED.' '.$zip_filename.GM_BACKUP_FILES_ZIP_FILES_CREATED.' '.GM_BACKUP_FILES_DURATION.date("i:s",time()-$start_time).' Min', 'success');
				}
					
				xtc_redirect(xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP));
			} else {
				$messageStack->add_session(GM_BACKUP_FILES_ZIP_FILELIST_ERROR, 'error');
				xtc_redirect(xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP));
			}
			
			break;

		case 'download':
			$file=$_GET['file'];
			$extension = substr($file, -3);
			
			if ($extension == 'zip') {
				header('Content-type: application/x-octet-stream');
				header('Content-disposition: attachment; filename=' . $file);
				readfile(DIR_FS_BACKUP . $file);
			} else {
				$messageStack->add(ERROR_DOWNLOAD_LINK_NOT_ACCEPTABLE, 'error');
			}
			
			break;

		case 'deleteconfirm':
			if (strstr($_GET['file'], '..')) {
				xtc_redirect(xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP));
			} 
				
			xtc_remove(DIR_FS_BACKUP . '/' . $_GET['file']);
			
			if (!$xtc_remove_error) {
				$messageStack->add_session(SUCCESS_BACKUP_DELETED, 'success');
			} else {
				$messageStack->add_session(ERROR_BACKUP_DELETED, 'success');
			}
				
			break;

		case 'images':
			$start_time = time();
			$zip_filename = DIR_FS_BACKUP.'images_'.date("Ymd_His").'.zip';
			$zip = new PclZip($zip_filename);
			$result=$zip->create(DIR_FS_DOCUMENT_ROOT.'images',PCLZIP_OPT_REMOVE_PATH,DIR_FS_DOCUMENT_ROOT);
			
			if ($result==0) {
				$messageStack->add_session($zip->errorInfo(), 'error');
			} else {
				$messageStack->add_session(SUCCESS_BACKUP_CREATED.' '.$zip_filename.GM_BACKUP_FILES_ZIP_FILES_CREATED.' '.GM_BACKUP_FILES_DURATION .date("i:s",time()-$start_time).' Min', 'success');
			}
			
			xtc_redirect(xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP));
			
			break;
	}
}

// Check if the backup directory exists. 
$dir_ok = false;
if (is_dir(DIR_FS_BACKUP)) {
	$dir_ok = true;
	if (!is_writeable(DIR_FS_BACKUP)) {
		$messageStack->add(ERROR_BACKUP_DIRECTORY_NOT_WRITEABLE, 'error');
	}
} else {
	$messageStack->add(ERROR_BACKUP_DIRECTORY_DOES_NOT_EXIST, 'error');
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
	<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
	<!-- header_eof //-->

	<!-- body //-->
	<table border="0" width="100%" cellspacing="2" cellpadding="2">
		<tr>
			<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
				<!-- left_navigation //-->
					<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
				<!-- left_navigation_eof //-->
				</table>
			</td>
			<!-- body_text //-->
			<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
		<tr>
			<td width="100%">
				<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)"><?php echo HEADING_TITLE; ?></div>
			</td>
		</tr>
		<tr>
			<td>
				<table border="0" width="100%" cellspacing="0" cellpadding="0">
					<tr>
						<td valign="top">
							<table class="gx-modules-table left-table" border="0" width="100%" cellspacing="0" cellpadding="0">
								<tr class="dataTableHeadingRow">
									<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_TITLE; ?></td>
									<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_FILE_DATE; ?></td>
									<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_FILE_SIZE; ?></td>
									<td class="dataTableHeadingContent" align="right"></td>
								</tr>
<?php
if ($dir_ok) {
	$dir = dir(DIR_FS_BACKUP);
	$contents = array();
	$exts = array("zip");
	
	while ($file = $dir->read()) {
		if (!is_dir(DIR_FS_BACKUP . $file)) {
			foreach ($exts as $value) {
				if (xtc_CheckExt($file, $value)) {
					$contents[] = $file;
				}
			}
		}
	}

	sort($contents);

	for ($files = 0, $count = sizeof($contents); $files < $count; $files++) {
		$entry = $contents[$files];
		$check = 0;

		if (((!$_GET['file']) || ($_GET['file'] == $entry)) && (!$buInfo) && ($_GET['action'] != 'backup') && ($_GET['action'] != 'restorelocal')) {
			$file_array['file'] = $entry;
			$file_array['date'] = date(PHP_DATE_TIME_FORMAT, filemtime(DIR_FS_BACKUP . $entry));
			$file_array['size'] = bytestostring(filesize(DIR_FS_BACKUP . $entry),1);
			switch (substr($entry, -3)) {
				case 'zip': $file_array['compression'] = 'ZIP'; break;
				case '.gz': $file_array['compression'] = 'GZIP'; break;
				default: $file_array['compression'] = TEXT_NO_EXTENSION; break;
			}

			$buInfo = new objectInfo($file_array);
		}

		if (is_object($buInfo) && ($entry == $buInfo->file)) {
			echo '<tr class="dataTableRow active">';
			$onclick_link = 'file=' . $buInfo->file . '&action=download&page_token=' . $_SESSION['coo_page_token']->generate_token();
		} else {
			echo '<tr class="dataTableRow">';
			$onclick_link = 'file=' . $entry;
		}
		?>
		
		<td class="dataTableContent" onclick="document.location.href='<?php echo xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, $onclick_link); ?>'"><?php echo '<a href="' . xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, 'action=download&file=' . $entry) . '">' . xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/icons/file_download.gif', ICON_FILE_DOWNLOAD) . '</a>&nbsp;' . $entry; ?></td>
		<td class="dataTableContent" align="center" onclick="document.location.href='<?php echo xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, $onclick_link); ?>'"><?php echo date(PHP_DATE_TIME_FORMAT, filemtime(DIR_FS_BACKUP . $entry)); ?></td>
		<td class="dataTableContent" align="right" onclick="document.location.href='<?php echo xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, $onclick_link); ?>'"><?php echo bytestostring(filesize(DIR_FS_BACKUP . $entry),1); ?></td>
		<td class="dataTableContent" align="right"></td>
</tr>
<?php
	}
	$dir->close();
}
?>
</table>
<table class="grid left-table paginator add-margin-bottom-24">
	<tr>
	<td class="span6">
		<?php echo TEXT_BACKUP_DIRECTORY . ' ' . DIR_FS_BACKUP; ?>
	</td>
		<td class="span6 pagination-control">
			<?php
			echo '<a class="control-element btn remove-margin" style="float:right" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, 'action=images&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '">' . BUTTON_BACKUP_IMAGES . '</a>';
			echo '<a class="control-element btn" style="float:right" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, 'action=backup&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '">' . BUTTON_BACKUP . '</a>';
			?>
		</td>
	</tr>
</table>
</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<div class="main left-table">
										<?php echo sprintf(GM_BACKUP_FILES_DESCRIPTION, implode('<br />', $t_exclude_array)); ?>
										<?php echo GM_BACKUP_FILES_RESTORE_INFO; ?>
									</div>
								</td>
							</tr>
						</table>
					</td>
				<!-- body_text_eof //-->
				</tr>
			</table>
			<!-- body_eof //-->

			<!-- footer //-->
				<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
			<!-- footer_eof //-->
		<div class="hidden">
			<?php
			$heading = array();
			$contents = array();
			$buttons = '';
			$formIsEditable = false;
			$formAction = '';
			$formMethod = 'post';
			$formAttributes = array();

			switch ($_GET['action']) {
				case 'delete':

					$heading[] = array('text' => '<b>' . $buInfo->date . '</b>');

					$formAction = xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, 'file=' . $buInfo->file . '&action=deleteconfirm');
					$contents[] = array('text' => TEXT_DELETE_INTRO);
					$contents[] = array('text' => '<br /><b>' . $buInfo->file . '</b>');
					$buttons = '<a class="btn btn-primary" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, 'file=' . $buInfo->file) . '">' . BUTTON_CANCEL . '</a>';
					$buttons .= '<input type="submit" class="btn" onClick="this.blur();" value="' . BUTTON_DELETE . '"/>';
					break;
				default:
					if (is_object($buInfo)) {
						$heading[] = array('text' => '<b>' . $buInfo->date . '</b>');
						$contents[] = array('text' => '<br />' . TEXT_INFO_DATE . ' ' . $buInfo->date);
						$contents[] = array('text' => TEXT_INFO_SIZE . ' ' . $buInfo->size);
						$contents[] = array('text' => '<br />' . TEXT_INFO_COMPRESSION . ' ' . $buInfo->compression);
						$buttons = '<a class="btn btn-primary" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, 'file=' . $buInfo->file . '&action=download&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '">' . BUTTON_DOWNLOAD . '</a>';
						$buttons .= '<a class="btn" onClick="this.blur();" href="' . xtc_href_link(FILENAME_GM_BACKUP_FILES_ZIP, 'file=' . $buInfo->file . '&action=delete&page_token=' . $_SESSION['coo_page_token']->generate_token()) . '">' . BUTTON_DELETE . '</a>';
					}
					break;
			}
			
			$configurationBoxContentView = MainFactory::create_object('ConfigurationBoxContentView');
			$configurationBoxContentView->setOldSchoolHeading($heading);
			$configurationBoxContentView->setOldSchoolContents($contents);
			$configurationBoxContentView->set_content_data('buttons', $buttons);
			$configurationBoxContentView->setFormEditable($formIsEditable);
			$configurationBoxContentView->setFormAction($formAction);
			$configurationBoxContentView->setFormAttributes($formAttributes);
			if (is_object($buInfo))
			{
				echo $configurationBoxContentView->get_html();
			}
			?>
		</div>
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>