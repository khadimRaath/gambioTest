<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id: viewdbtables.php 4042 2014-06-29 17:12:42Z derpapst $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

$_pageCSS = '
h4 {
	border-bottom: 1px solid #999;
}
';
$_MagnaSession['currentPlatform'] = '';

$_url = array(
	'module' => 'viewdbtables',
	'view' => isset($_GET['view']) ? $_GET['view'] : ''
);
if (!in_array($_url['view'], array('sql', 'list'))) {
	$_GET['view'] = $_url['view'] = 'list';
}


function getFieldsMeta($result) {
    $fields       = array();
    $num_fields   = (($___mysqli_tmp = mysqli_num_fields($result)) ? $___mysqli_tmp : false);
    for ($i = 0; $i < $num_fields; ++$i) {
        $fields[] = (array)(((($___mysqli_tmp = mysqli_fetch_field_direct($result, 0)) && is_object($___mysqli_tmp)) ? ( (!is_null($___mysqli_tmp->primary_key = ($___mysqli_tmp->flags & MYSQLI_PRI_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->multiple_key = ($___mysqli_tmp->flags & MYSQLI_MULTIPLE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->unique_key = ($___mysqli_tmp->flags & MYSQLI_UNIQUE_KEY_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->numeric = (int)(($___mysqli_tmp->type <= MYSQLI_TYPE_INT24) || ($___mysqli_tmp->type == MYSQLI_TYPE_YEAR) || ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? ($___mysqli_tmp->type == MYSQLI_TYPE_NEWDECIMAL) : 0)))) && (!is_null($___mysqli_tmp->blob = (int)in_array($___mysqli_tmp->type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) && (!is_null($___mysqli_tmp->unsigned = ($___mysqli_tmp->flags & MYSQLI_UNSIGNED_FLAG) ? 1 : 0)) && (!is_null($___mysqli_tmp->zerofill = ($___mysqli_tmp->flags & MYSQLI_ZEROFILL_FLAG) ? 1 : 0)) && (!is_null($___mysqli_type = $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = (($___mysqli_type == MYSQLI_TYPE_STRING) || ($___mysqli_type == MYSQLI_TYPE_VAR_STRING)) ? "type" : "")) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_TINY, MYSQLI_TYPE_SHORT, MYSQLI_TYPE_LONG, MYSQLI_TYPE_LONGLONG, MYSQLI_TYPE_INT24))) ? "int" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && in_array($___mysqli_type, array(MYSQLI_TYPE_FLOAT, MYSQLI_TYPE_DOUBLE, MYSQLI_TYPE_DECIMAL, ((defined("MYSQLI_TYPE_NEWDECIMAL")) ? constant("MYSQLI_TYPE_NEWDECIMAL") : -1)))) ? "real" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIMESTAMP) ? "timestamp" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_YEAR) ? "year" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (($___mysqli_type == MYSQLI_TYPE_DATE) || ($___mysqli_type == MYSQLI_TYPE_NEWDATE))) ? "date " : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_TIME) ? "time" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_SET) ? "set" : $___mysqli_tmp->type)) &&(!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_ENUM) ? "enum" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_GEOMETRY) ? "geometry" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_DATETIME) ? "datetime" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && (in_array($___mysqli_type, array(MYSQLI_TYPE_TINY_BLOB, MYSQLI_TYPE_BLOB, MYSQLI_TYPE_MEDIUM_BLOB, MYSQLI_TYPE_LONG_BLOB)))) ? "blob" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type && $___mysqli_type == MYSQLI_TYPE_NULL) ? "null" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->type = ("" == $___mysqli_tmp->type) ? "unknown" : $___mysqli_tmp->type)) && (!is_null($___mysqli_tmp->not_null = ($___mysqli_tmp->flags & MYSQLI_NOT_NULL_FLAG) ? 1 : 0)) ) : false ) ? $___mysqli_tmp : false);
    }
    return $fields;
}

function dumpTable($table) {
	if (!MagnaDB::gi()->tableExists($table)) {
		echo 'Table '.$table.' does not exist.';
		return;
	}
	$result = MagnaDB::gi()->query('SELECT * FROM `'.$table.'`');
	$fields = getFieldsMeta($result);
	$result = MagnaDB::gi()->fetchArray($result);
	
	echo '
		<table class="datagrid autoOddEven hover autoWidth valigntop">
			<thead>
				<tr>';
	foreach ($fields as $field) {
		echo '
					<th>'.$field['name'].'</th>';
	}
	echo '
				</tr>
			</thead>
			<tbody>';
	if (!empty($result)) {
		foreach ($result as $item) {
			echo '
					<tr>';
			foreach ($item as $value) {
				echo '
						<td>'.print_m($value).'</td>';
			}
			echo '
					</tr>';
		}
	} else {
		echo '
			<tr><td colspan="'.count($fields).'">Empty</td></tr>';
	}
	echo '
			</tbody>
		</table>';	
}

if ($_url['view'] == 'list') {
	if (!isset($_GET['kind']) || ($_GET['kind'] != 'ajax')) {
		include_once(DIR_MAGNALISTER_INCLUDES.'admin_view_top.php');
		$tables = MagnaDB::gi()->getAvailableTables('/magnalister/');
		foreach ($tables as $table) {
			echo '
				<h4>'.$table.' <span id="'.$table.'" class="gfxbutton magnifier"></span></h4>
				<div id="container_'.$table.'"></div>
				<script type="text/javascript">/*<![CDATA[*/
					$(document).ready(function() {
						$(\'#'.$table.'\').click(function () {'; ?>
	
							jQuery.blockUI(blockUILoading);
							jQuery.ajax({
								type: 'GET',
								url: '<?php echo toURL($_url, array('kind' => 'ajax', 'table' => $table), true);?>',
								success: function(data) {
									$('#container_<?php echo $table; ?>').html(data);
									jQuery.unblockUI();
								},
								error: function() {
									jQuery.unblockUI();
								},
								dataType: 'html'
							});
	<?php
			echo '
						});
					});
				/*]]>*/</script>
			';
		}
		include_once(DIR_MAGNALISTER_INCLUDES.'admin_view_bottom.php');
	} else {
		dumpTable($_GET['table']);
	}
} else if ($_url['view'] == 'sql') {
	include_once(DIR_MAGNALISTER_INCLUDES.'admin_view_top.php');
	
	echo '
	<form action="#" method="post">
		<h2>SQL</h2>
		<p><b>Vorsicht:</b> SQL Anfragen werden ohne Sicherung ausgef&uuml;hrt. Es gibt kein r&uuml;ckg&auml;nig machen!</p>
		<textarea name="SQL" style="width: 99.9%; height: 250px; resize: vertical; font-family: monospace;">'.(
			isset($_POST['SQL']) ? $_POST['SQL'] : ''
		).'</textarea>
		<input type="submit">
	</form>';
	
	if (isset($_POST['SQL'])) {
		echo '<div id="sql_out" style="border:1px solid #999;margin: 10px 0px;width: 800px; height: 500px;overflow-x:auto;">';
		$r = MagnaDB::gi()->query($_POST['SQL']);
		if ($r === true) {
			$r = array (array ('Affected Rows' => MagnaDB::gi()->affectedRows()));
			if (MagnaDB::gi()->getLastInsertID() > 0) {
				$r[0]['Inserted Id'] = MagnaDB::gi()->getLastInsertID();
			}
		} else {
			$r = MagnaDB::gi()->fetchArray($r);
			if (!is_array($r)) {
				$r = array(array ('Type' => var_dump_pre($r, true)));
			} else if (empty($r)) {
				$r = array(array ('No' => 'Data'));
			}
		}
		renderDataGrid($r, array (
			'CSS.TableClass' => 'valigntop'
		));
		echo '</div>
			<script>
			$(window).load(function() {
				$(\'#sql_out\').css("width", $(\'#content\').css("width"));
			});
			</script>';
		
	}
	
	include_once(DIR_MAGNALISTER_INCLUDES.'admin_view_bottom.php');
}

include_once(DIR_WS_INCLUDES . 'application_bottom.php');
exit();
