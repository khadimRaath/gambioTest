<?php
/* --------------------------------------------------------------
   header.php 2016-07-18
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
   (c) 2002-2003 osCommerce(header.php,v 1.19 2002/04/13); www.oscommerce.com
   (c) 2003	 nextcommerce (header.php,v 1.17 2003/08/24); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: header.php 1025 2005-07-14 11:57:54Z gwinger $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

if($jsEngineLanguage === null)
{
	$jsEngineLanguage = []; // This variable is used in the "header.php" for the JS Engine Configuration object.
}

// Added default translations to the $jsEngineLanguage array.
$languageTextManager = MainFactory::create_object('LanguageTextManager', array('buttons', $_SESSION['languages_id']),
                                                  true);
$defaultTranslations = [
	'buttons'          => $languageTextManager->get_section_array('buttons'),
	'messages'         => $languageTextManager->get_section_array('messages'),
	'labels'           => $languageTextManager->get_section_array('labels'),
	'admin_labels'     => $languageTextManager->get_section_array('admin_labels'),
	'admin_general'    => $languageTextManager->get_section_array('admin_general'),
	'admin_info_boxes' => $languageTextManager->get_section_array('admin_info_boxes')
];
$jsEngineLanguage    = array_merge($jsEngineLanguage, $defaultTranslations);

// Determine if "dev" environment is enabled by checking whether the ".dev-environment" file is present
// in the "src" directory.
$devEnvironment = file_exists(DIR_FS_CATALOG . '.dev-environment');

if($devEnvironment)
{
	$GLOBALS['messageStack']->add($languageTextManager->get_text('TEXT_DEV_ENVIRONMENT_WARNING', 'admin_general'),
	                              'warning');
}

$installerDirectory = file_exists(DIR_FS_CATALOG . 'gambio_installer');

// Check if the 'gambio_installer' directory exists, and warn of its existence.
if($_SESSION['customers_status']['customers_status_id'] === '0' && $installerDirectory)
{
	$installerMessage = sprintf($languageTextManager->get_text('WARNING_INSTALL_DIRECTORY_EXISTS', 'general'),
	                            substr(DIR_WS_CATALOG, 0, -1));
	
	$GLOBALS['messageStack']->add($installerMessage, 'error');
}

// Add FontAwesome Fallback Script
include DIR_FS_ADMIN . 'html/compatibility/fontawesome_fallback.php';
?>

<!--
	PRE-SCRIPTS INITIALIZATION
-->

<script type="text/javascript">
	// Do not show loading bar on ajax requests.
	window.paceOptions = {
		ajax: false,
		elements: false,
		restartOnPushState: false,
		restartOnRequestAfter: false
	};
</script>

<!--
	 JAVASCRIPT & CSS ASSETS
-->

<?php
$suffix = $devEnvironment ? '' : '.min';

echo '
	<link rel="stylesheet" href="' . DIR_WS_ADMIN . 'html/assets/styles/compatibility' . $suffix . '.css" />
	<link rel="stylesheet" href="' . DIR_WS_ADMIN . 'html/assets/styles/vendor' . $suffix . '.css" />
	<link rel="stylesheet" href="' . DIR_WS_ADMIN . 'html/assets/styles/legacy/admin_info_box'. $suffix . '.css" />
	<link rel="stylesheet" href="' . DIR_WS_CATALOG . 'JSEngine/build/vendor' . $suffix . '.css" />
	
	<script src="' . DIR_WS_CATALOG . 'JSEngine/build/vendor' . $suffix . '.js"></script>
	<script src="' . DIR_WS_ADMIN . 'html/assets/javascript/vendor' . $suffix . '.js"></script>
	<script src="' . DIR_WS_CATALOG . 'JSEngine/build/jse' . $suffix . '.js"></script>
	<script src="' . DIR_WS_CATALOG . 'JSEngine/build/require' . $suffix . '.js"></script>
	<script type="text/javascript" src="' . DIR_WS_CATALOG . 'gm/javascript/jquery/plugins/jquery.tooltip.pack.js"></script>
	<script type="text/javascript" src="' . DIR_WS_ADMIN . 'includes/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="' . DIR_WS_ADMIN . 'html/assets/javascript/legacy/gm/admin_info_box'. $suffix . '.js"></script>
	<script type="text/javascript" src="' . DIR_WS_ADMIN . 'html/assets/javascript/legacy/gm/GMFavMaster'. $suffix . '.js"></script>
	<script type="text/javascript" src="' . DIR_WS_ADMIN . 'html/assets/javascript/legacy/gm/GMLeftBoxes'. $suffix . '.js"></script>
';

require_once('../gm/modules/gm_gprint_admin_header.php');
?>

<!--
	POST-SCRIPTS INITIALIZATION
-->

<script type="text/javascript">
	// Session ID is used for AJAX calls (legacy files).
	var session_id = <?php echo '"' . xtc_session_id() . '"'; ?>;
	
	// Set console support fallback flag (legacy files).
	var fb = (typeof console !== 'undefined');
	
	// JS Engine Configuration Object
	window.JSEngineConfiguration = {
		environment  : <?php echo $devEnvironment ? '"development"' : '"production"'; ?>,
		appUrl       : <?php echo json_encode(HTTP_SERVER . rtrim(DIR_WS_CATALOG, '/')) ?>,
		shopUrl      : <?php echo json_encode(HTTP_SERVER . rtrim(DIR_WS_CATALOG, '/')) ?>,
		appVersion   : <?php echo json_encode(gm_get_conf('INSTALLED_VERSION')) ?>,
		shopVersion  : <?php echo json_encode(gm_get_conf('INSTALLED_VERSION')) ?>,
		translations : <?php echo isset($jsEngineLanguage) ? json_encode($jsEngineLanguage) : '{}'; ?>,
		languageCode : <?php echo json_encode($_SESSION['language_code']); ?>,
		pageToken    : <?php echo isset($_SESSION['coo_page_token'])
		? json_encode($_SESSION['coo_page_token']->generate_token()) : 'null'; ?>,
		cacheToken   : <?php echo json_encode(MainFactory::create('CacheTokenHelper')->getCacheToken()); ?>,
		collections  : [
			{name: 'controllers', attribute: 'controller'},
			{name: 'extensions', attribute: 'extension'},
			{name: 'widgets', attribute: 'widget'},
			{name: 'compatibility', attribute: 'compatibility'}
		]
	};
	
	// Legacy JS Options object. 
	<?php
	$coo_js_options_control = MainFactory::create_object('JSOptionsControl', [false]);
	$t_js_options_array =  $coo_js_options_control->get_options_array($_GET);
	?>
	
	var js_options = <?php echo json_encode($t_js_options_array) ?>;
	var gmFavMaster = new GMFavMaster();
	var gmLeftBoxes = new GMLeftBoxes();
	
	$(document).ready(function() {
		$('.template_warning').tooltip({
			delay: 0,
			showURL: false,
			track: true,
			positionLeft: true,
			top: 20,
			bodyHandler: function() {
				return '<?php echo TEMPLATE_ADVICE; ?>';
			}
		});
	});
</script>

<!--
	ADMIN HEADER ELEMENTS

	Defines a page wrapper that is closed in the footer.php file and the main admin header.

	The "page_loading" module will display the main <div> once the page is ready.
-->

<div class="hidden" style="width:100%; background-color:#2c2c2c;" align="center"
     data-gx-namespace="<?php echo HTTP_SERVER . DIR_WS_ADMIN . 'html/assets/javascript/engine'; ?>"
     data-jse-namespace="<?php echo HTTP_SERVER . DIR_WS_CATALOG. 'JSEngine/build'; ?>"
     data-gx-compatibility="admin_favicon_fix init_class_fixes page_loading init_html_fixes page_nav_tabs resize_page shortcuts scroll_top"
     data-gx-extension="dynamic_shop_messages"
     data-admin_favicon_fix-status="<?php echo (gm_get_conf('GM_LOGO_FAVICON_USE')) ? 'enabled' : 'disabled'; ?>">

	<div class="main-top-header content_width gx-container" style="width:<?php echo $_SESSION['screen_width']; ?>px;background-color:white; margin-top:30px;" align="left">
		<?php require DIR_FS_ADMIN . 'html/compatibility/main_top_header.php'; ?>
	</div>

	<div class="main-page-content content_width" style="background-color:white; margin-bottom:0px;" align="left">

		<div class="message_stack_container hidden">
			<?php
			if($GLOBALS['messageStack']->size > 0)
			{
				echo $GLOBALS['messageStack']->output();
			}
			?>
		</div>
