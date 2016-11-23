<?php
/* --------------------------------------------------------------
   show_log.php 2016-07-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License
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
   --------------------------------------------------------------
*/

/*
 * needed functions
 */
require_once('includes/application_top.php');
/*
 * class to show logs
 */
require_once('includes/gm/classes/ShowLogs.php');

$page_array[0]['id'] = 1;
$page_array[0]['text'] = 1;
$no_logs_message = '';
$error_message = '';

$coo_show_logs = new ShowLogs();

// get a list of logs
$file_array = $coo_show_logs->scan_dir();

if(count($file_array) > 0) {	
if((isset($_GET['file']) || isset($_GET['hidden_file'])) && (!empty($_GET['file']) || !empty($_GET['hidden_file']))) {
	$file = $_GET['file'];
    if(!empty($_GET['hidden_file'])) {
		$file = $_GET['hidden_file'];
	}
}
else
{
	$latestLogFile = $file_array[0];
	
	foreach($file_array as $entry)
	{
		if(strpos($entry['id'], '.gz') !== false)
		{
			continue;
		}
		
		$currentModifiedTime = $coo_show_logs->get_file_date($latestLogFile['id']);
		$entryModifiedTime   = $coo_show_logs->get_file_date($entry['id']);
		
		if($entryModifiedTime > $currentModifiedTime)
		{
			$latestLogFile = $entry;
		}
	}
	
	$file = $latestLogFile['id'];
}

$checked_filename = $coo_show_logs->check_file_name($file, $file_array);
if($checked_filename) {
    // get page numbers for the page select
	if(strstr($file, '.html') == false)
	{
		$page_array = $coo_show_logs->get_page_number($file);
	}
	else
	{
		//TODO: Deaktivierung der Paginierung eleganter lösen
		$page_array = $coo_show_logs->get_page_number($file, 150, true);
	}

    // set pagenumber
    $page = 1;
    if(isset($_GET['page']) && !empty($_GET['page'])) {
        $page = (int)$_GET['page'];
    }

    // read log
    if(!empty($file_array[0]['id'])) {
		if(strstr($file, '.html') == false)
		{
			$log = $coo_show_logs->get_log($file, $page);
		}
		else
		{
			//TODO: Deaktivierung der Paginierung eleganter lösen
			$log = $coo_show_logs->get_log($file, $page, 150, true);
		}
    }
} else {
    $error_message = TEXT_ERROR_MESSAGE.' ('.$file.')';
}
} else {
	$no_logs_message = TEXT_INFO_NO_FILES;
}

if(isset($_GET['action']) && $_GET['action'] == 'mark_as_read')
{
	$coo_show_logs->mark_as_read($_SESSION['customer_id'], $_GET['file']);
	$t_success_message = LOG_MARKED_AS_READ;
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="x-ua-compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
<script type="text/javascript" src="html/assets/javascript/legacy/gm/ShowLog.js"></script>
</head>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<script type="text/javascript">
    $(document).ready(function(){
        var coo_show_log = new ShowLog();
		var t_last_key_pressed = 0;

		$('#log_content').show();

		$(document).keydown(function(e) {

			var t_key_pressed = (e.keyCode ? e.keyCode : (e.which ? e.which : e.charCode));
			
			// r = reload
			if (t_key_pressed == 82) {
				location.reload();
			}
			// f = download log file
			if (t_key_pressed == 70) {
				coo_show_log.download_log();
			}
			// e = Log neu laden
			if (t_key_pressed == 69) {
				coo_show_log.do_request();
				$('#log_message').html('<?php echo JS_LOG_LOADED; ?>');
				$("#log_message").fadeIn("fast").delay(4000).fadeOut("fast");
			}
			// d = delete selected log
			if (t_key_pressed == 68) {
				if(confirm('<?php echo JS_CONFIRM_DELETE; ?>')) {
					coo_show_log.delete_log();
				}
			}
			// c = clear selected log
			if (t_key_pressed == 67 && t_last_key_pressed != 17) {
				
				if(confirm('<?php echo JS_CONFIRM_CLEAR; ?>')) {
		            coo_show_log.clear_log();
					coo_show_log.do_request();
				}
			}
			// a = autoload start/stop
			if (t_key_pressed == 65) {
				if($('input[name="autoload"]').is(':checked')) {
					$('input[name="autoload"]').prop('checked', false);
				} else {
					$('input[name="autoload"]').prop('checked', true);
				}
	            coo_show_log.start_stop('<?php echo TEXT_MIN_TIME; ?>');
			}

			t_last_key_pressed = (e.keyCode ? e.keyCode : (e.which ? e.which : e.charCode));
		});

        $('input[name="autoload"]').change(function(){
            coo_show_log.start_stop('<?php echo TEXT_MIN_TIME; ?>');
        });
		
		$('input').keydown(function (e)
		{
			e.stopPropagation();
		});
     });
</script>

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
           <span class="main breakpoint-large" style="display: inline-block; overflow: hidden;">
				<?php
				// if no logfiles exists, show message
				if(!empty($no_logs_message)) {
					?>
					<div class="pageHeading" style="float:left; background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)">
						<?php echo HEADING_TITLE; ?>
					</div>
					<table style="margin-bottom:5px; clear: left" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr class="dataTableHeadingRow">
							<td class="dataTableHeadingContentText" style="border-right: 0px;">
								<?php echo HEADING_TITLE; ?>
							</td>
						</tr>
					</table>
					<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr class="dataTableHeadingRow">
							<td class="dataTableHeadingContentText" style="border-right: 0px; background-color: #585858; color: #000;"><?php echo $no_logs_message; ?></td>
						</tr>
					</table>
					<?php
				} else {
					?>
               <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                        <td width="100%">
                            <div class="pageHeading" style="float:left; background-image:url(html/assets/images/legacy/gm_icons/hilfsprogr1.png)">
                                <?php echo HEADING_TITLE; ?>
                            </div>
                            
                            <table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr class="dataTableHeadingRow">
                                    <td class="dataTableHeadingContentText" style="border-right: 0px;">
                                        <?php echo HEADING_TITLE; ?>
										<span style="display: none;" id="counter">
											<?php
											echo ' - '.TEXT_RELOAD.'&nbsp;';
											echo '<span id="timer" style="width: 40px; font-weight: bold;"></span>';
											?>
										</span>
										<span style="display: none;" id="log_message">
										</span>
                                    </td>
                                </tr>
                            </table>
                            <?php
                            // show errormessage
                            if(!empty($error_message)) {
                                ?>
	                            <div class="message_stack_container breakpoint-large">
		                            <div class="alert alert-danger" ><?php echo $error_message; ?></div>
	                            </div>
                                <?php
                            }
							 // show success message
                            if(!empty($t_success_message)) {
                                ?>
		                            <div class="message_stack_container breakpoint-large">
                                        <div class="alert alert-success" ><?php echo $t_success_message; ?></div>
		                            </div>
                                <?php
                            }
                            ?>
                            <pre id="log_content" class="breakpoint-large" style="box-sizing: border-box; margin: 0; padding: 2px; display: none; height: 500px; border: 1px solid #DDDDDD; overflow:auto; font-size: 12px; background-color: #F7F7F7;"><?php echo $log; ?></pre>
                        </td>
                    </tr>
                </table>
				<table class="showLogMenu" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td class="add-padding-10">
							<span class="gx-container">
								<span data-gx-widget="checkbox">
								<?php
								echo '<label for="autoload">'.TEXT_AUTO_LOAD.'</label>&nbsp;';
								echo xtc_draw_input_field('autoload', '1', 'id="autoload" data-single_checkbox', false, 'checkbox')
								?>
								</span>
							</span>
							<span>
								<?php
								echo '<label for="interval">'.TEXT_AUTO_LOAD_INTERVAL.'</label>&nbsp;';
								echo xtc_draw_input_field('auto_interval', '3', 'style="width: 40px;" id="interval"');
								?>
							</span>
							<span>
								<?php
								echo xtc_draw_form('file_search', FILENAME_SHOW_LOGS, '', 'get');
								echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
								echo HEADING_LOG_FILE . '&nbsp;' .
								     xtc_draw_pull_down_menu('file',
								                             $file_array,
								                             $file,
								                             'onChange="this.form.submit();" style="max-width: 580px;"');
								?>
								</form>
							</span>
							<span>
								<?php
								echo xtc_draw_form('file_search1', FILENAME_SHOW_LOGS, '', 'get');
								echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
								echo xtc_draw_hidden_field('hidden_file', $file);
								echo HEADING_PAGE_NUMBER . '&nbsp;';
								echo xtc_draw_pull_down_menu(
									'page',
									$page_array,
									$page,
									'onChange="this.form.submit();"');
								?>
								</form>
							</span>
						</td>
					</tr>
				</table>
			   <div style="float:left">
				   <ul style="list-style-type: none; padding-left: 10px;">
					<li><?php echo TEXT_RELOAD_PAGE; ?></li>
					<li><?php echo TEXT_AUTORELOAD_LOG; ?></li>
					<li><?php echo TEXT_RELOAD_LOG; ?></li>
					<li><?php echo TEXT_DOWNLOAD_LOG; ?></li>
					<li><?php echo TEXT_CLEAR_LOG . ' (' . TEXT_ATTENTION . ')'; ?></li>
					<li><?php echo TEXT_DELETE_LOG . ' (' . TEXT_ATTENTION . ')'; ?></li>
				</ul>
			   </div>
			   <div style="width:100%; text-align: right; margin-top: 20px">
			   <?php
			   if($coo_show_logs->check_for_change($_SESSION['customer_id'], $_GET['file']) == true)
			   {
				   echo '<a style="width: auto; display: inline-block" class="button" href="' . xtc_href_link(FILENAME_SHOW_LOGS, xtc_get_all_get_params() . 'action=mark_as_read') . '">' . BUTTON_MARK_AS_READ . '</a>';
			   }
			   ?>
			   </div>
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
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
