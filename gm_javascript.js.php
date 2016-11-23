<?php
/* --------------------------------------------------------------
   gm_javascript.js.php 2016-06-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once('includes/application_top.php');

if(isset($_SESSION['language_charset']))
{
	header('Content-Type: text/javascript; charset=' . $_SESSION['language_charset']);
}
else
{
	header('Content-Type: text/javascript; charset=utf-8');
}

if(gm_get_env_info('TEMPLATE_VERSION') >= 3 && (!isset($_GET['globals']) || $_GET['globals'] !== 'off'))
{
	$httpCaching = MainFactory::create_object('HTTPCaching');
	$httpCaching->start_output_buffer();
	
	$isDebugMode = file_exists(DIR_FS_CATALOG . '.dev-environment');
	
	$pageToken  = $_SESSION['coo_page_token']->generate_token();
	$cacheToken = MainFactory::create('CacheTokenHelper')->getCacheToken();
	
	$jsEngineConfig = MainFactory::create('JSEngineConfiguration',
	                                      new NonEmptyStringType(GM_HTTP_SERVER . DIR_WS_CATALOG),
	                                      new NonEmptyStringType('templates/' . CURRENT_TEMPLATE . '/'),
	                                      new LanguageCode(new StringType($_SESSION['language_code'])),
	                                      MainFactory::create_object('LanguageTextManager', array(), true),
	                                      new EditableKeyValueCollection(array(
		                                                                     'buttons'  => 'buttons',
		                                                                     'general'  => 'general',
		                                                                     'labels'   => 'labels',
		                                                                     'messages' => 'messages'
	                                                                     )), new BoolType($isDebugMode),
	                                      new StringType($pageToken), new StringType($cacheToken));
	
	echo $jsEngineConfig->getJavaScript();
	
	$suffix = '.min';
	if($isDebugMode)
	{
		$suffix = '';
	}
	
	echo file_get_contents(DIR_FS_CATALOG . 'JSEngine/build/vendor' . $suffix . '.js') . "\n";
	echo file_get_contents(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/assets/javascript/vendor' . $suffix
	                       . '.js') . "\n";
	echo file_get_contents(DIR_FS_CATALOG . 'JSEngine/build/jse' . $suffix . '.js') . "\n";
	echo file_get_contents(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/assets/javascript/initialize_template'
	                       . $suffix . '.js') . "\n";
	echo file_get_contents(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/assets/javascript/template_helpers'
	                       . $suffix . '.js') . "\n";
	
	$page = (isset($_GET['page'])) ? (string)$_GET['page'] : 'Global';
	
	$usermodJsMaster = MainFactory::create('UsermodJSMaster', $page);
	$files           = $usermodJsMaster->get_files();
	
	foreach($files as $file)
	{
		// print new line avoiding conflicts with comments
		echo "\n";
		
		if(file_exists($file))
		{
			include_once($file);
		}
	}
	
	// print new line avoiding conflicts with comments
	echo "\n";
	
	// include RequireJS after usermods avoiding conflicts
	echo file_get_contents(DIR_FS_CATALOG . 'JSEngine/build/require' . $suffix . '.js') . "\n";
	
	$jsContent = $httpCaching->stop_output_buffer();
	
	$httpCaching->send_header($jsContent, false, false, 'public', '', '');
	$httpCaching->check_cache($jsContent);
	
	echo $jsContent;
	
	xtc_db_close();
	exit;
}


if(MOBILE_ACTIVE == 'true' && (isset($_GET['section']) == false || trim($_GET['section']) != 'MobileCandy'))
{
	xtc_db_close();
	exit;
}


/////////////////// OLD SCRIPTS ///////////////////////

$_SESSION['lightbox']->set_actual('false');

$httpCaching = MainFactory::create_object('HTTPCaching');
$httpCaching->start_output_buffer();

if(!isset($_GET['globals']) || $_GET['globals'] != 'off')
{
// Session-ID as GET-param is deprecated and missing. Let set it manually. 	
if(!isset($_GET['XTCsid']))
{
	$_GET['XTCsid'] = xtc_session_id();
}
$_GET['XTCsid_name'] = xtc_session_name();

$jsOptionsControl = MainFactory::create_object('JSOptionsControl');
$jsOptions = $jsOptionsControl->get_options_array($_GET);
?>

var js_options = <?php echo json_encode($jsOptions) ?>;

var t_php_helper = '';
<?php

if(is_object($GLOBALS['coo_debugger']) && $GLOBALS['coo_debugger']->is_enabled('js') == true
   && gm_get_env_info('TEMPLATE_VERSION') >= FIRST_GX2_TEMPLATE_VERSION
)
{
	include_once(DIR_FS_CATALOG . 'templates/' . CURRENT_TEMPLATE . '/javascript/StopWatch.js');
}

//JQuery
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/jquery.min.js'));
//JQuery Migrate
include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/jquery-migrate.min.js'));
/* BOF StyleEdit */
if($_SESSION['style_edit_mode'] == 'edit' || $_SESSION['style_edit_mode'] == 'sos')
{
	echo 'var style_edit_sectoken = "' . xtc_session_id() . '";';
	
	include_once(get_usermod(DIR_FS_CATALOG . 'gm/javascript/jquery/ui/jquery-ui.js'));
	
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/jquery/plugins/fancybox/jquery.mousewheel-3.0.2.pack.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/jquery/plugins/fancybox/jquery.fancybox-1.3.0.pack.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/jquery/plugins/ajaxupload.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/config_StyleEdit.js');
	
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMColorizer.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMStyleMonitor.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMStyleEditToolBox.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMStyleEditHandler.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMStyleEditSelector.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMStyleEditControl.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMBoxesPageMenu.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMBoxesMaster.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/GMUploader.js');
	include_once(DIR_FS_CATALOG . 'StyleEdit/javascript/style_edit.js.php');
}
/* EOF StyleEdit */

$globalExtenderComponent = MainFactory::create_object('JSGlobalExtenderComponent');
$globalExtenderComponent->set_data('GET', $_GET);
$globalExtenderComponent->proceed();
}

$section = $_GET['section'];
if(preg_match('/[\W]+/', $section))
{
	trigger_error('gm_javascript: $_GET["section"] contains unexpected characters', E_USER_ERROR);
}

$c_page = '';
if(isset($_GET['page']) && is_string($_GET['page']))
{
	$c_page = trim((string)$_GET['page']);
}

if($c_page !== '')
{
	$classNameSuffix = 'ExtenderComponent';
	
	$requestRouter = MainFactory::create_object('RequestRouter', array($classNameSuffix));
	$requestRouter->set_data('GET', $_GET);
	$className     = 'JS' . $c_page;
	$proceedStatus = $requestRouter->proceed($className);
	if($proceedStatus != true)
	{
		trigger_error('could not proceed module [' . htmlentities_wrapper($className) . ']', E_USER_ERROR);
	}
}

((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);

$jsContent = $httpCaching->stop_output_buffer();

$httpCaching->send_header($jsContent, false, false, 'public', '', '');
$httpCaching->check_cache($jsContent);

echo $jsContent;
