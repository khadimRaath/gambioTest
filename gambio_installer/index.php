<?php
/* --------------------------------------------------------------
   index.php 2016-06-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (index.php,v 1.18 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: index.php 1220 2005-09-16 15:53:13Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

require('includes/application.php');
require_once('includes/FTPManager.inc.php');

// include needed functions
require_once(DIR_FS_INC.'xtc_redirect.inc.php');
require_once(DIR_FS_INC.'xtc_href_link.inc.php');

if(isset($_SESSION['language']) && $_SESSION['language'] == 'english')
{
	include('language/english.php');
}
else
{
	include('language/german.php');
}

$iniFileData = parse_ini_file('config.ini', true);

require_once('includes/RequirementsTestingInstaller.inc.php');
$requirementsTesting   = new RequirementsTestingInstaller();
$testReqirementsResult = $requirementsTesting->textPHPAndMySQLVersion($iniFileData['PHP_VERSION']['minPHPVersion']);
$testReqirementsResultInfo = $requirementsTesting->getInfo();
$phpMysqlWarningMsg = '';
if($testReqirementsResult === false)
{
	$phpMysqlWarningMsg = REQUIREMENT_WARNING;

	$phpMysqlWarningTextArray = array(
			'###minPHPVersion###'    => $iniFileData['PHP_VERSION']['minPHPVersion'],
			'###yourPHPVersion###'   => $testReqirementsResultInfo['php'],
	);
	$phpMysqlWarningMsg       = str_replace(array_keys($phpMysqlWarningTextArray),
	                                        array_values($phpMysqlWarningTextArray), $phpMysqlWarningMsg);
}

if(isset($_GET['precheck']) && $_GET['precheck'] == '1')
{
	// check register_globals
	$t_register_globals = false;
	if(ini_get('register_globals') == '1' || ini_get('register_globals') == 'on' || ini_get('register_globals') == 'On')
	{
		$t_register_globals = true;
	}

	// check uploaded files
	$fp = fopen("txt/filelist.txt", "r");
	$t_missing_files_array = array();
	while($t_line = fgets($fp, 1024))
	{
		$t_dir = DIR_FS_CATALOG . $t_line;
		if(file_exists(trim($t_dir)) == false)
		{
			if(is_dir(DIR_FS_CATALOG . 'templates/EyeCandy/') == false && strstr($t_line, 'EyeCandy') !== false) continue;
			$t_missing_files_array[] = $t_line;
		}
	}
	fclose($fp);

	if($t_register_globals === false && empty($t_missing_files_array))
	{
		header('Location: index.php?language=' . rawurlencode($_GET['language']));
	}
}

if (!$script_filename = str_replace("\\", '/', getenv('PATH_TRANSLATED'))) {
	$script_filename = getenv('SCRIPT_FILENAME');
}
$script_filename = str_replace('//', '/', $script_filename);

if (!$request_uri = getenv('REQUEST_URI')) {
	if (!$request_uri = getenv('PATH_INFO')) {
		$request_uri = getenv('SCRIPT_NAME');
	}

	if (getenv('QUERY_STRING')) $request_uri .=  '?' . getenv('QUERY_STRING');
}

$dir_fs_www_root_array = explode('/', dirname($script_filename));
$dir_fs_www_root = array();
for ($i=0; $i<sizeof($dir_fs_www_root_array)-2; $i++) {
	$dir_fs_www_root[] = $dir_fs_www_root_array[$i];
}
$dir_fs_www_root = implode('/', $dir_fs_www_root);

$dir_ws_www_root_array = explode('/', dirname($request_uri));
$dir_ws_www_root = array();
for ($i=0; $i<sizeof($dir_ws_www_root_array)-1; $i++) {
	$dir_ws_www_root[] = $dir_ws_www_root_array[$i];
}
$dir_ws_www_root = implode('/', $dir_ws_www_root);

$coo_ftp_manager2 = new FTPManager(false, '', '', '', '');
$t_wrong_chmod_array = $coo_ftp_manager2->check_chmod();

for($i = 0; $i < count($t_wrong_chmod_array); $i++)
{
	$t_wrong_chmod_array[$i] = str_replace(DIR_FS_CATALOG, '', $t_wrong_chmod_array[$i]);
}
sort($t_wrong_chmod_array);

if(isset($_POST['FTP_HOST']) && !empty($t_wrong_chmod_array) && !isset($_GET['chmod']))
{
	$t_host = $_POST['FTP_HOST'];
	$t_user = $_POST['FTP_USER'];
	$t_password = $_POST['FTP_PASSWORD'];
	$t_pasv = false;
	if(!empty($_POST['FTP_PASV'])) $t_pasv = true;

	$coo_ftp_manager = new FTPManager(true, $t_host, $t_user, $t_password, $t_pasv);

	if($coo_ftp_manager->v_error == '')
	{
		if(isset($_POST['dir']))
		{
			$t_dir = $_POST['dir'];
		}
		else
		{
			$t_dir = $coo_ftp_manager->find_shop_dir('/');
		}

		$t_list_array = $coo_ftp_manager->get_directories($t_dir);

		$_SESSION['FTP_HOST'] = $_POST['FTP_HOST'];
		$_SESSION['FTP_USER'] = $_POST['FTP_USER'];
		$_SESSION['FTP_PASSWORD'] = $_POST['FTP_PASSWORD'];
		if(!empty($_POST['FTP_PASV']))
		{
			$_SESSION['FTP_PASV'] = $_POST['FTP_PASV'];
		}
	}
}

if(empty($t_wrong_chmod_array) 
	&& !isset($_GET['chmod'])
	&& isset($_GET['language'])
	&& (($t_memory_limit_ok === true
		&& $t_register_globals === false
		&& empty($t_missing_files_array))
		|| !isset($_GET['precheck'])))
{
	header('Location: index.php?chmod=ok&language=' . rawurlencode($_GET['language']));
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Installation Gambio GX3</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="css/stylesheet.css" />
		<!--[if gte IE 9]>
		  <style type="text/css">
			#main.gradient, #main .gradient.button, #main .gradient.button.red, #main .gradient.button.green {
			   filter: none;
			}
		  </style>
		<![endif]-->
		<script type="text/javascript" src="../gm/javascript/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="javascript/javascripts.js.php?language=<?php echo rawurlencode($_SESSION['language']); ?>"></script>
	</head>

	<body>

		<?php if($testReqirementsResult === false): ?>
			<div class="warning" style="margin: -30px 0 30px 0;">
				<?php echo $phpMysqlWarningMsg; ?>
			</div>
		<?php endif; ?>

		<h1>Installation</h1>
		<h2>Gambio GX3</h2>

		<div id="main" class="gradient">

			<div id="install_service">
				<p><strong><?php echo HEADING_INSTALLATION_SERVICE; ?></strong></p>
				<p>
					<?php echo TEXT_INSTALLATION_SERVICE; ?><br />
					<br />
					<a href="https://www.gambio.de/901fB" class="button gradient red" target="_blank"><?php echo BUTTON_GAMBIO_PORTAL; ?></a>
				</p>

				
			</div>

			<form name="install" id="install_form" action="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" method="post">
			<?php
			if($t_session_started === false)
			{
			?>
				<p><strong class="error"><?php echo sprintf(ERROR_SESSION_SAVE_PATH, $dir_ws_www_root . '/cache'); ?></strong></p>
			<?php
			}
			elseif(!isset($_GET['language']))
			{
			?>
				<p><strong><?php echo HEADING_INSTALLATION; ?></strong></p>
				<p>
					<?php echo TEXT_INSTALLATION; ?><br />
					<br />
					<a href="index.php?language=german&precheck=1" class="button gradient green"><?php echo BUTTON_GERMAN; ?></a>&nbsp;
					<a href="index.php?language=english&precheck=1" class="button gradient green"><?php echo BUTTON_ENGLISH; ?></a>
				</p>
			<?php
			}
			elseif(isset($_GET) && $_GET['precheck'] == '1')
			{
			?>
				<div class="precheck">
				<?php
				if($t_register_globals)
				{
				?>
					<strong><?php echo HEADING_REGISTER_GLOBALS; ?></strong>
					<br />
					<br />
					<?php echo TEXT_REGISTER_GLOBALS; ?>
					<br />
					<br />
					<br />
				<?php
				}
				if($t_memory_limit_ok === false)
				{
				?>
					<div class="error"><?php echo ERROR_MEMORY_LIMIT; ?></div>
					<br />
					<?php echo sprintf(ERROR_TEXT_MEMORY_LIMIT, $t_memory_limit); ?>
					<br />
					<br />
					<br />
				<?php
				}
				if(!empty($t_missing_files_array))
				{
				?>
					<div class="error"><?php echo ERROR_MISSING_FILES; ?></div>
					<br />
					<?php echo TEXT_MISSING_FILES; ?>
					<br />
					<br />
					<div class="error_field">
					<?php
						echo implode('<br />', $t_missing_files_array);
					?>
					</div>
					<br />
					<a href="index.php?precheck=1&language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_CHECK_MISSING_FILES; ?></a>
					<br />
					<br />
					<br />
				<?php
				}
				?>
					<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient green"><?php echo BUTTON_CONTINUE; ?></a>
				</div>
			<?php
			}
			elseif(!isset($_GET) || $_GET['ftp'] == 'done' || !isset($_GET['chmod']))
			{
			?>
				<div class="ftp_data">
				<?php
				if(isset($_GET) && $_GET['ftp'] == 'done')
				{
				?>

					<span class="error"><?php echo ERROR_SET_PERMISSIONS_FAILED; ?></span>
					<br />
					<br />
					<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_BACK; ?></a>&nbsp;
					<a href="index.php?chmod=ok&language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient green"><?php echo BUTTON_CONTINUE; ?></a>
					<br />
					<br />
					<br />
					<strong><?php echo HEADING_WRONG_PERMISSIONS; ?></strong>
					<br />
					<br />
					<div class="error_field">
					<?php
						echo implode('<br />', $t_wrong_chmod_array);
					?>
					</div>
					<br />
					<a href="index.php?ftp=donw&language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_CHECK_PERMISSIONS; ?></a>
					<br />
					<br />
				<?php
				}
				else
				{
				?>

					<strong><?php echo HEADING_WRONG_PERMISSIONS; ?></strong>
					<br />
					<br />
					<div class="error_field">
					<?php
						echo implode('<br />', $t_wrong_chmod_array);
					?>
					</div>
					<br />
					<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>" class="button gradient"><?php echo BUTTON_CHECK_PERMISSIONS; ?></a>
					<br />
					<br />
					<br />
					<?php echo TEXT_SET_PERMISSIONS; ?>
					<br />
					<br />
					<br />
				
					<table class="block_head" width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="4%"><img src="images/icon-path.png" alt="" /></td>
							<td width="96%"><strong><?php echo HEADING_FTP_DATA; ?></strong></td>
						</tr>
					</table>

					<table width="620" border="0" cellspacing="5" cellpadding="0">
						<tr>
							<td width="120"><?php echo LABEL_FTP_SERVER; ?></td>
							<td width="500"><input type="text" class="input_field" name="FTP_HOST" size="35" value="<?php echo $_POST['FTP_HOST']; ?>" autocomplete="off" /></td>
						</tr>
						<tr>
							<td width="120"><?php echo LABEL_FTP_USER; ?></td>
							<td width="500"><input type="text" class="input_field" name="FTP_USER" size="35" value="<?php echo $_POST['FTP_USER']; ?>" autocomplete="off" /></td>
						</tr>
						<tr>
							<td width="120"><?php echo LABEL_FTP_PASSWORD; ?></td>
							<td width="500"><input type="password" class="input_field" name="FTP_PASSWORD" size="35" value="<?php echo $_POST['FTP_PASSWORD']; ?>" autocomplete="off" /></td>
						</tr>
						<tr>
							<td><label for="pasv"><?php echo LABEL_FTP_PASV; ?></label></td>
							<td><input type="checkbox" id="pasv" name="FTP_PASV" value="true" style="margin-left: 0"<?php echo (isset($_POST['FTP_PASV']) || empty($_POST)) ? ' checked="checked"' : ''; ?> /></td>
						</tr>
					</table>

					<?php
					if(!isset($_POST['FTP_HOST']))
					{
					?>
					<br />
					<input type="submit" name="go" value="<?php echo BUTTON_CONNECT; ?>" class="button gradient green" />
					<?php
					}
					else
					{
					?>
					<br />
					<fieldset>
						<legend><?php echo HEADING_REMOTE_CONSOLE; ?></legend>
					<?php

					if($coo_ftp_manager->v_error != '')
					{
						echo '<div class="error">' . $coo_ftp_manager->v_error . '</div>';
					}
					else
					{
						if(is_object($coo_ftp_manager) && $coo_ftp_manager->is_shop($t_dir))
						{
							if(!isset($_POST['chmod_777']) || empty($_POST['chmod_777']))
							{
								echo '<input type="hidden" name="dir" value="' . $t_dir . '" />';
								echo '<input type="submit" name="chmod_777" value="' . BUTTON_SET_PERMISSIONS . '" class="button gradient green" /><br /><br />';
							}
							else
							{
								$coo_ftp_manager->chmod_777($t_dir);
								echo '<script type="text/javascript">
										<!--
										self.location.href="index.php?ftp=done&language=' . rawurlencode($_GET['language']) . '";
										//-->
										</script>';
							}
						}

						if(isset($_POST['FTP_HOST']) && (!isset($_POST['chmod_777']) || empty($_POST['chmod_777'])))
						{
							if(strrpos($t_dir, '/') !== false && $t_dir != '/')
							{
								if(strrpos($t_dir, '/') === 0)
								{
									echo '<input type="submit" class="dir" name="dir" value="/" /> ' . LABEL_DIR_UP . '<br /><br />';
								}
								else
								{
									echo '<input type="submit" class="dir" name="dir" value="' . substr($t_dir, 0, strrpos($t_dir, '/')) . '" /> ' . LABEL_DIR_UP . '<br /><br />';
								}
							}

							for($i = 0; $i < count($t_list_array); $i++)
							{
								echo '<input type="submit" class="dir" name="dir" value="' . $t_list_array[$i] . '" /><br />';
							}
						}
					}
					?>
					</fieldset>
					<?php
					}

					if($coo_ftp_manager->v_error != '' || (is_object($coo_ftp_manager)))
					{
					?>
					<br />
					<input type="submit" name="go" value="<?php echo BUTTON_CONNECT_NEW; ?>" class="button gradient" />
					<?php
					}
					?>
					<br />
					<br />
					<div class="warning">
						<?php echo TEXT_SKIP; ?>
						<br />
						<br />
						<a href="index.php?language=<?php echo rawurlencode($_GET['language']); ?>&chmod=ok" class="button gradient"><?php echo BUTTON_SKIP; ?></a>
					</div>

				<?php
					}
					?>
				</div>
			<?php
			}
			else
			{
			?>
				<table class="block_head server_data" width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="4%"><img src="images/icon-database.png" alt="" /></td>
						<td width="96%"><strong><?php echo HEADING_DATABASE; ?></strong></td>
					</tr>
				</table>

				<table class="server_data" width="750" border="0" cellspacing="5" cellpadding="0">
					<tr>
						<td width="16%"><?php echo LABEL_DB_SERVER; ?></td>
						<td width="22%"><input type="text" class="input_field_short" name="DB_SERVER" size="15" autocomplete="off" /></td>
						<td width="62%"><span class="input_error"><?php echo ERROR_INPUT_DB_CONNECTION; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_DB_USER; ?></td>
						<td colspan="2"><input type="text" class="input_field_short" name="DB_SERVER_USERNAME" size="15" autocomplete="off" /></td>
					</tr>
					<tr>
						<td><?php echo LABEL_DB_PASSWORD; ?></td>
						<td colspan="2"><input type="password" class="input_field_short" name="DB_SERVER_PASSWORD" size="15" autocomplete="off" /></td>
					</tr>
					<tr>
						<td><?php echo LABEL_DB_DATABASE; ?></td>
						<td><input type="text" class="input_field_short" name="DB_DATABASE" size="15" autocomplete="off" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_DB_DATABASE; ?></span></td>
					</tr>
				</table>
				<br class="server_data" />
				<br class="server_data" />
				<table class="block_head server_data" width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="4%"><img src="images/icon-path.png" alt="" /></td>
						<td width="96%"><strong><?php echo HEADING_SHOP_INFORMATION; ?></strong></td>
					</tr>
				</table>

				<table class="server_data" width="750" border="0" cellspacing="5" cellpadding="0">
					<tr>
						<td width="16%"><?php echo LABEL_HTTP_SERVER; ?></td>
						<td width="42%"><input type="text" class="input_field" name="HTTP_SERVER" size="35" value="<?php echo 'http://' . getenv('HTTP_HOST'); ?>" /></td>
						<td width="42%"><span class="input_error"><?php echo ERROR_INPUT_SERVER_URL; ?></span></td>
					</tr>
					<tr>
						<td><label for="ssl"><?php echo LABEL_SSL; ?></label></td>
						<td colspan="2"><input type="checkbox" id="ssl" name="ENABLE_SSL" value="true" style="margin-left: 0" /></td>
					</tr>
					<tr class="https_server" style="display:none">
						<td><?php echo LABEL_HTTPS_SERVER; ?></td>
						<td><input type="text" class="input_field" name="HTTPS_SERVER" size="35" value="<?php echo 'https://' . getenv('HTTP_HOST'); ?>" /></td>
						<td><span class="error  input_error"><?php echo ERROR_INPUT_SERVER_HTTPS; ?></span></td>
					</tr>
				</table>
				<br class="server_data" />
				<br class="server_data" />
				
				<table class="block_head shop_data" width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="4%"><img src="images/icon-path.png" alt="" /></td>
						<td width="96%"><strong><?php echo HEADING_ADMIN_DATA; ?></strong></td>
					</tr>
				</table>

				<table class="shop_data" width="750" border="0" cellspacing="5" cellpadding="0">
					<tr style="height: 32px">
						<td width="16%"><?php echo LABEL_GENDER; ?></td>
						<td width="42%"><input type="radio" value="m" name="GENDER" checked="checked" /> <?php echo LABEL_MALE; ?> <input type="radio" value="f" name="GENDER" /> <?php echo LABEL_FEMALE; ?></td>
						<td width="42%"></td>
					</tr>
					<tr>
						<td width="16%"><?php echo LABEL_FIRSTNAME; ?></td>
						<td width="42%"><input type="text" class="input_field" name="FIRST_NAME" size="35" value="" /></td>
						<td width="42%"><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_LASTNAME; ?></td>
						<td><input type="text" class="input_field" name="LAST_NAME" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_STREET; ?></td>
						<td><input type="text" class="input_field" name="STREET_ADRESS" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_5; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_STREET_NUMBER; ?></td>
						<td><input type="text" class="input_field" name="STREET_NUMBER" size="35" value="" /></td>
						<td><span class="input_error"></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_POSTCODE; ?></td>
						<td><input type="text" class="input_field" name="POST_CODE" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_4; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_CITY; ?></td>
						<td><input type="text" class="input_field" name="CITY" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_STATE; ?></td>
						<td id="states_container">
							<select name="STATE">
								<option value="81">Bremen</option>
							</select>
						</td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_COUNTRY; ?></td>
						<td id="countries_container">
							<select name="COUNTRY">
								<option value="81">Germany</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><?php echo LABEL_TELEPHONE; ?></td>
						<td><input type="text" class="input_field" name="TELEPHONE" size="35" value="" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
					</tr>
					<tr>
						<td><?php echo LABEL_EMAIL; ?></td>
						<td><input type="text" class="input_field" name="EMAIL_ADRESS" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_EMAIL; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_PASSWORD; ?></td>
						<td><input type="password" class="input_field" name="PASSWORD" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_5; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_CONFIRMATION; ?></td>
						<td><input type="password" class="input_field" name="PASSWORD_CONFIRMATION" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_PASSWORD_CONFIRMATION; ?></span></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td></td>
					</tr>
					<tr>
						<td><?php echo LABEL_SHOP_NAME; ?></td>
						<td><input type="text" class="input_field" name="STORE_NAME" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_3; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_COMPANY; ?></td>
						<td><input type="text" class="input_field" name="COMPANY" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_MIN_LENGTH_2; ?></span></td>
					</tr>
					<tr>
						<td><?php echo LABEL_EMAIL_FROM; ?></td>
						<td><input type="text" class="input_field" name="EMAIL_ADRESS_FROM" size="35" value="" /></td>
						<td><span class="input_error"><?php echo ERROR_INPUT_EMAIL; ?></span></td>
					</tr>
				</table>
				<br class="shop_data" />
				<br class="shop_data" />

				<br class="robots_data" />
				<table class="block_head robots_data" width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="4%"><img src="images/icon-path.png" alt="" /></td>
						<td width="96%"><strong><?php echo HEADLINE_ROBOTS; ?></strong></td>
					</tr>
				</table>

				<div class="progress">
					<strong><?php echo HEADING_PROGRESS; ?></strong>
					<p><?php echo TEXT_PROGRESS; ?></p>
					<br />
				</div>

				<div id="ajax"></div>

				<p class="robots_data">
					<?php echo TEXT_ROBOTS; ?>
					<br />
					<br />
					<a class="button gradient" id="download" href="get_robots.php?download=robot"><?php echo BUTTON_DOWNLOAD; ?></a>
					<br />
					<br />
				</p>
				<div class="finish">
					<br />
					<strong class="finish"><?php echo HEADING_SUCCESS; ?></strong>
					<p><?php echo TEXT_SUCCESS; ?></p>
					<br />
					<br />
					<a class="button gradient green" href="<?php echo $dir_ws_www_root . '/'; ?>"><?php echo BUTTON_OPEN_SHOP; ?></a>
				</div>
				
				<a class="gradient button green server_data" id="import_sql"><?php echo BUTTON_START; ?></a>
				<a class="gradient button green shop_data" id="run_config"><?php echo BUTTON_FINISH; ?></a>
				

				<?php
					echo xtc_draw_hidden_field_installer('install[]', 'database');
					echo xtc_draw_hidden_field_installer('install[]', 'configure');

					echo xtc_draw_hidden_field_installer('DIR_FS_DOCUMENT_ROOT', $dir_fs_www_root);
					echo xtc_draw_hidden_field_installer('DIR_FS_CATALOG', $local_install_path);
					echo xtc_draw_hidden_field_installer('DIR_FS_ADMIN', $local_install_path . 'admin/');
					echo xtc_draw_hidden_field_installer('DIR_WS_CATALOG', $dir_ws_www_root . '/');
					echo xtc_draw_hidden_field_installer('DIR_WS_ADMIN', $dir_ws_www_root . '/admin/');

					echo xtc_draw_hidden_field_installer('ZONE_SETUP', 'yes');

					echo xtc_draw_hidden_field_installer('STATUS_DISCOUNT', '0.00');
					echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT_FLAG', '0');
					echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT', '0.00');
					echo xtc_draw_hidden_field_installer('STATUS_GRADUATED_PRICE', '1');
					echo xtc_draw_hidden_field_installer('STATUS_SHOW_PRICE', '1');
					echo xtc_draw_hidden_field_installer('STATUS_SHOW_TAX', '1');
					echo xtc_draw_hidden_field_installer('STATUS_DISCOUNT2', '0.00');
					echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT_FLAG2', '0');
					echo xtc_draw_hidden_field_installer('STATUS_OT_DISCOUNT2', '0.00');
					echo xtc_draw_hidden_field_installer('STATUS_GRADUATED_PRICE2', '1');
					echo xtc_draw_hidden_field_installer('STATUS_SHOW_PRICE2', '1');
					echo xtc_draw_hidden_field_installer('STATUS_SHOW_TAX2', '1');
				?>
			<?php
			}
			?>

			</form>
			
			<img id="logo" src="images/gambio_logo.png" alt="" />
		</div>

		<div id="copyright">
			<strong><a href="https://www.gambio.de" target="_blank"><strong>Gambio.de</strong></a> - Installer &copy; 2016 Gambio GmbH</strong><br />
			Gambio GmbH provides no warranty. The Shopsoftware is <br />
			redistributable under the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU General Public License (Version 2)</a><br />
			based on: E-Commerce Engine Copyright &copy; 2006 <a href="http://www.xt-commerce.com" target="_blank">xt:Commerce</a>, <br />
			<a href="http://www.xt-commerce.com" target="_blank">xt:Commerce</a> provides no warranty.
		</div>

	</body>
</html>
<?php
if(is_object($coo_ftp_manager))
{
	$coo_ftp_manager->quit();
}
@((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
?>