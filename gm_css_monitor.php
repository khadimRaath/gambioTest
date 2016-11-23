<?php
/* --------------------------------------------------------------
   gm_css_monitor.php 2014-04-24 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php

	include ('includes/application_top.php');
	if($_SESSION['customers_status']['customers_status_id'] == 0) {
	require_once (DIR_FS_CATALOG . 'gm/classes/GMCSSMonitor.php');

	$monitor = new GMCSSMonitor(CURRENT_TEMPLATE);

	/*
	* -> SHOW CHILD
	*/
	if(!empty($_GET['gm_selector'])) {

		$nodes = $monitor->get_child($_GET['gm_selector']);
		echo $nodes;

	/*
	* -> SHOW STYLES BY ID
	*/
	} else if(!empty($_GET['gm_child_selector'])) {

		$style = $monitor->get_style_by_id($_GET['gm_child_selector']);
		echo  $style;
		echo  '<br /><input id="gm_save" type="submit" value="speichern" onclick="save_styles()">';

	/*
	* -> SHOW STYLES BY NAME
	*/
	} else if(!empty($_GET['gm_child_selector_name'])) {
		if(empty($_GET['gm_searcher'])) {
			$style = $monitor->get_style_by_name($_GET['gm_child_selector_name']);
			echo  '<br /><input id="gm_save" type="submit" value="speichern" onclick="save_styles()">';
		} else {
			$style = $monitor->get_style_by_name($_GET['gm_child_selector_name']);
			echo  $style;
		}

	/*
	* -> SAVE STYLES
	*/
	} else if(!empty($_GET['css_input'])) {

		$importer = new GmImportCSS($_GET['css_input'], CURRENT_TEMPLATE);

		if(empty($_GET['insert'])) {
			$importer->run_away();
		} else {
			$importer->run();
		}

		@unlink(DIR_FS_CATALOG . 'cache/__dynamics.css');

	/*
	* -> Browse
	*/
	} else if(!empty($_GET['parents'])) {
		echo $nodes = $monitor->get_parent();

	/*
	* -> new style
	*/
	} else if(!empty($_GET['new_style'])) {
		echo xtc_draw_textarea_field("css_input", '', '', 20);
		echo  '<br /><input id="gm_save" type="submit" value="speichern" onclick="insert_styles()">';

	/*
	* -> SHOW STYLES BY NAME
	*/
	} else {

		$nodes = $monitor->get_parent();
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
		<head>
			<title>Gambio CSS Monitor</title>
			<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/jquery.min.js"></script>
			<script type="text/javascript" src="<?php echo DIR_WS_CATALOG; ?>gm/javascript/jquery/jquery-migrate.min.js"></script>
			<script type="text/javascript">

				$(document).ready(function() {

					$("select option").css({
							"padding-left":"20px",
							"font-weight":"normal"
					});

					$("select option:selected").css({
							"padding-left":"0px",
							"font-weight":"bold"
					});

					$("#new_style").click(function() {
						$("#gm_form").load("gm_css_monitor.php?new_style=1");
					});

					$("#gm_selector").change(function() {
						$("#gm_search_result").fadeOut("fast");
						$("#gm_update_styles").fadeOut("fast");
						$(this).fadeTo("fast", 0.5);
						//
						$("#gm_selector").load("gm_css_monitor.php" + "?gm_selector=" + encodeURIComponent($("#gm_selector").val()));
						$("#gm_selector").attr('id', 'gm_child_selector');
						$("#gm_child_selector").attr('onchange', 'load_styles()');
						$(this).fadeTo("fast", 1);
					});

					$("#gm_search").change(function() {
						$("#gm_result").fadeOut("fast");
						$("#css_input").fadeOut("fast");
						$("#css_input").val("");
						$("#gm_search_result").load("gm_css_monitor.php" + "?gm_child_selector_name=" + encodeURIComponent($("#gm_search").val()) + "&gm_searcher=1");
						$("#gm_search_result").fadeIn('fast');
						$("#gm_search").val("");
					});
				});

				function id_switcher() {
						$("#gm_child_selector").attr('id', 'gm_selector');
						$("#gm_selector").attr('onchange', 'gm_clear()');
				}

				function load_styles() {
					$("#gm_search_result").fadeOut("fast");
					$("#gm_update_styles").fadeOut("fast");
					if($("#gm_child_selector").val() == 'root') {
						$("#gm_child_selector").load("gm_css_monitor.php?parents=1",  '', function() {id_switcher()});
						$("#css_input").val("");

					} else {

						$("#css_input").val("loading...");
						$("#gm_result").load("gm_css_monitor.php" + "?gm_child_selector=" + encodeURIComponent($("#gm_child_selector").val()));
						$("#gm_result").fadeIn('fast');
					}
				}

				function load_searched_styles() {
					$("#gm_update_styles").fadeOut("fast");
					$("#css_input").val("loading...");
					$("#gm_result").load("gm_css_monitor.php" + "?gm_child_selector=" + encodeURIComponent($("#gm_searcher").val()));
					$("#gm_result").fadeIn('fast');
				}

				function gm_clear() {
					$("#css_input").val("");
				}

				function save_styles() {
					$("#gm_update_styles").html('<img src="images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');
					$("#gm_update_styles").load("gm_css_monitor.php" + "?css_input=" + encodeURIComponent($("#css_input").val()));
					$("#gm_update_styles").fadeIn("fast");
				}

				function insert_styles() {
					var css_input = $("#css_input").val();
					if(css_input == '') {
						css_input = 'empty';
					}

					$("#gm_form").load("gm_css_monitor.php" + "?css_input=" + encodeURIComponent(css_input) + "&insert=1");
					$("#gm_form").html('<img src="images/loading.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="loading">');
					$("#gm_form").fadeIn("fast");
				}

				function gm_close() {
					$("#gm_search_result").fadeOut("fast");
				}

			</script>
			<style type="text/css">
			<!--
				body{
					margin:0px;
					padding:0px;
					font-family:Arial, Helvetica, sans-serif;
					color:#0E4686;
					font-size:10pt;
					line-height:12pt;
					text-align:center;
				}

				a:link, a:hover, a:active, a:visited {
					font-family:Arial, Helvetica, sans-serif;
					color:#0E4686;
					font-size:11px;
					font-style:normal;
					font-weight:bold;
					text-decoration: none;
					text-transform:none;
				}

				a:hover {
					text-decoration: underline;
				}

				h1{
					margin:0px;
					padding:0px;
					margin-bottom:5px;
					float:left;
					font-family:Arial, Helvetica, sans-serif;
					color:#0E4686;
					font-size:12pt;
					line-height:12pt;
				}

				h4 {
					font-family:Arial, Helvetica, sans-serif;
					color:#0E4686;
					font-size:10pt;
					line-height:12pt;
					margin-top:12pt;
					margin-bottom:12pt;
				}


				pre, .error, input, select, textarea, #gm_update_styles, #gm_result, #gm_close, #gm_search_result, .gm_new {
					font-family:Arial, Helvetica, sans-serif;
					color:#0E4686;
					font-size:13px;
					font-style:normal;
					font-weight:normal;
					text-decoration: none;
					text-transform:none;
				}

				.error {
					font-weight:bold;
				}
				input, select, textarea, #gm_update {
					width:450px;
					margin-top:5px;
				}

				select {
					font-weight:bold;
				}

				select option {
					padding-left: 20px;
					font-weight:normal;
				}

				select option:selected {
					padding-left: 0px;
					font-weight:bold;
				}

				#gm_update {
					height:20px;
				}

				h1, input, select, textarea {
					width:600px;
				}

				#search_form {
					margin: 20px;
				}

				#gm_save {
					width:100px;
				}

				#gm_result {
					display:none;
				}

				#gm_close{
					height:20px;
					width:20px;
					font-weight:bold;
					font-size:12px;
					cursor:pointer
				}

				#gm_heading {
					float:left;
					text-align:left;
				}

				.gm_new, #gm_new span {
					font-weight:bold;
					text-align:right;
					font-size: 11px;
				}

				.gm_new span:hover {
					font-size: 11px;
					cursor:pointer;
					font-weight:bold;
					text-decoration:underline;
					text-align:right;
				}
			//-->
		</style>
	</head>
	<body>
		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td colspan="3" style="width:800px; height:154px">&nbsp;</td>
			</tr>

			<tr>
				<td>&nbsp;</td>
				<td style="width:700px; background-color:#FFFFFF">

					<div id="search_form">

						<h1><div id="gm_heading">Gambio CSS Monitor</div><div class="gm_new"><a href="gm_css_monitor.php">update Styles</a> | <span id="new_style">insert Styles</span> | <a href="<?php echo HTTP_SERVER . DIR_WS_CATALOG; ?>">Shop</a></div></h1>
						<br />
						<div id="gm_form">
							<input id="gm_search" type="text">
							<div id="gm_selector_box">
								<select id="gm_selector" onchange="gm_clear()">
									<?php echo $nodes; ?>
								</select>
							</div>
							<div id="gm_monitor"></div>
							<div id="gm_search_result"></div>
							<div id="gm_result"></div>

							<div id="gm_update">
								<div id="gm_update_styles"></div>
							</div>
						</div>
					</div>
				</td>
				<td style="width:50px">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3" style="width:800px; height:50px">&nbsp;</td>
			</tr>
		</table>
	</body>
</html>
<?php } ?>
<?php } else { echo "Sie sind nicht als Admin eingeloggt"; }?>