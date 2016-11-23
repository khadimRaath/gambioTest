<?php
/* --------------------------------------------------------------
   gm_sql.php 2016-06-20
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
require_once(DIR_FS_CATALOG . 'gm/inc/gm_split_sql_queries.inc.php');

$GLOBALS['messageStack']->add(GM_SQL_ADVICE, 'info');
$GLOBALS['messageStack']->add_additional_class('breakpoint-small');

if(isset($_POST['query']))
{
	if($_SESSION['coo_page_token']->is_valid($_POST['page_token']))
	{
		$gm_queries = gm_prepare_string($_POST['query'], true);

		$gm_query = array();
		$gm_query = gm_split_sql_queries($gm_queries);

		$gm_query_result_output = '';

		for($i = 0; $i < count($gm_query); $i++)
		{
			$gm_query_result = mysqli_query($GLOBALS["___mysqli_ston"], $gm_query[$i]); // xtc_db_query hier nicht verwenden!
			if(!$gm_query_result) {
				$gm_query_result_output .= GM_SQL_ERROR . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '<br />';
			}
		}

		if($gm_query_result_output == '')
		{
			$GLOBALS['messageStack']->add(GM_SQL_SUCCESS, 'success');
			$gm_query_result_output = GM_SQL_SUCCESS;
		}
		else
		{
			$GLOBALS['messageStack']->add($gm_query_result_output, 'error');
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
<script language="JavaScript" type="text/javascript">
function set_color(id){
	document.getElementById('result_row_'+id).style.backgroundColor = '#E8E8E8';
}
</script>
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
		<div class="main breakpoint-small">
		
			<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr class="dataTableHeadingRow">
				 	<td class="dataTableHeadingContentText" style="border-right: 0px"><?php echo HEADING_TITLE; ?></td>
				</tr>
			</table>

			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="150" align="center" class="dataTableHeadingContent">
						<?php echo HEADING_TITLE; ?>
					</td>
					<td width="150" align="center" class="dataTableHeadingContent">
						<a href="<?php echo xtc_href_link('minisql.php'); ?>"><?php echo GM_SQL_MINI_LINK; ?></a>
					</td>
				</tr>
			</table>
			
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr class="dataTableRow">
					<td style="font-size: 12px; padding: 0px 10px 11px 10px; text-align: justify">
						<br />
						<?php echo GM_SQL_DESCRIPTION; ?>
						<br />
						<br />
						<form name="gm_sql_form" action="<?php echo xtc_href_link('gm_sql.php'); ?>" method="post">
							<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
							<textarea name="query" cols="92" rows="10"><?php echo htmlspecialchars_wrapper(stripslashes($_POST['query'])); ?></textarea>
							<br />
							<br />
							<input style="margin-left:1px" class="button pull-right" type="submit" name="go" value="<?php echo BUTTON_EXECUTE; ?>" />
							
						</form>
					</td>
				</tr>
			</table>
		
		</div>	
		
		<div class="breakpoint-large">
			<?php
			if(isset($gm_query_result) && $gm_query_result_output == GM_SQL_SUCCESS && count($gm_query) == 1 && strpos_wrapper(strtolower_wrapper($gm_query[0]), 'select') === 0 ){
				?>
				<br />
				<br />
				<div id="gm_sql_output" style="overflow:auto; height:500px;">
					<table class="gx-modules-table gx-container" border="0" style="border-collapse: collapse">
						<tr class="dataTableHeadingRow remove-border">
							<?php
							$fieldCount = mysqli_num_fields($gm_query_result);
							for($i = 0; $i < $fieldCount; $i++)
							{
								$field = mysqli_fetch_field_direct($gm_query_result, $i);
								echo '<td class="dataTableHeadingContent">';
								echo '<nobr><strong>'. $field->name .'</strong></nobr>';
								echo '</td>';
							}
							?>
						</tr>
						<?php
						$count = 0;
						while($resultData = mysqli_fetch_array($gm_query_result))
						{
							echo '<tr id="result_row_' . $count . '" class="dataTableRow" onclick="set_color(' . $count . ')">';
							foreach($resultData as $column)
							{
								$value = htmlentities($column);
								echo '<td valign="top" class="dataTableContent"><nobr>' . $value . '</nobr></td>';
							}
							echo '</tr>';
							flush();
							
							$count++;
						}
						?>
					</table>
				</div>
				<?php
			}
			?>
		</div>

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