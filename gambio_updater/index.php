<?php
/* --------------------------------------------------------------
   index.php 2016-09-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once('includes/application.inc.php');

$t_content = 'language';
if(isset($_GET['content']))
{
    $t_content = $_GET['content'];
}

$t_language = 'german';
if(isset($_GET['language']) && file_exists('lang/' . basename($_GET['language']) . '.inc.php'))
{
    $t_language = basename($_GET['language']);
}

function filter_password($p_string)
{
    $t_string = $p_string;

    if(ini_get('magic_quotes_gpc') == 1 || ini_get('magic_quotes_gpc') == 'On' || ini_get('magic_quotes_gpc') == 'on')
    {
        $t_string = stripslashes($p_string);
    }
    elseif(preg_match('/(^"|[^\\\\]{1}")/', $p_string) != 1 && preg_match('/(^\'|[^\\\\]{1}\')/', $p_string) != 1)
    {
        $t_string = stripslashes($p_string);
    }

    return $t_string;
}

require_once('lang/' . $t_language . '.inc.php');
require_once '../release_info.php';

$iniFileData = parse_ini_file('config.ini', true);

require_once('classes/RequirementsTesting.inc.php');
$requirementsTesting       = new RequirementsTesting();
$testReqirementsResult     = $requirementsTesting->textPHPAndMySQLVersion($iniFileData['PHP_VERSION']['minPHPVersion'],
                                                                          $iniFileData['MySQL_VERSION']['minMySQLVersion']);
$testReqirementsResultInfo = $requirementsTesting->getInfo();

if($testReqirementsResult === false)
{
    $phpMysqlWarningMsg = REQUIREMENT_WARNING;

    $phpMysqlWarningTextArray = array(
        '###minPHPVersion###'    => $iniFileData['PHP_VERSION']['minPHPVersion'],
        '###yourPHPVersion###'   => $testReqirementsResultInfo['php'],
        '###minMySQLVersion###'  => $iniFileData['MySQL_VERSION']['minMySQLVersion'],
        '###yourMySQLVersion###' => $testReqirementsResultInfo['mySQL']
    );
    $phpMysqlWarningMsg       = str_replace(array_keys($phpMysqlWarningTextArray),
                                            array_values($phpMysqlWarningTextArray), $phpMysqlWarningMsg);
}

require_once('classes/GambioUpdateControl.inc.php');

require_once('classes/DatabaseModel.inc.php');
$coo_db = new DatabaseModel(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

$t_sql = "CREATE TABLE IF NOT EXISTS `version_history` (
  `history_id` INT(11) NOT NULL AUTO_INCREMENT,
  `version` VARCHAR(255) CHARACTER SET `utf8` NOT NULL,
  `name` VARCHAR(255) CHARACTER SET `utf8` NOT NULL,
  `type` ENUM('master_update', 'service_pack', 'update') NOT NULL DEFAULT 'update',
  `revision` INT(11) NOT NULL,
  `is_full_version` TINYINT(1) NOT NULL DEFAULT '0',
  `installation_date` DATETIME NOT NULL,
  `php_version` VARCHAR(255) CHARACTER SET `utf8` NOT NULL,
  `mysql_version` VARCHAR(255) CHARACTER SET `utf8` NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1";
$coo_db->query($t_sql);

// version_history fix for 2.1.4.0 full version
if(file_exists('../version_info/2_1_4_0.php'))
{
    $t_check = $coo_db->query('SELECT * FROM `version_history` WHERE `version` = "2.1.3.0" AND `is_full_version` = "1"',
                              true);
    if($t_check->num_rows > 0)
    {
        $coo_db->query('UPDATE `version_history` SET `version` = "2.1.4.0", `name` = "2.1.4.0" WHERE `version` = "2.1.3.0" AND `is_full_version` = "1"');
    }
}

$t_check = $coo_db->query("SHOW tables LIKE 'gm_lang_files'", true);
if($t_check->num_rows > 0)
{
    $t_check = $coo_db->query("SHOW tables LIKE 'language_sections'", true);
    if($t_check->num_rows == 0)
    {
        $t_sql = "RENAME TABLE `gm_lang_files` TO `language_sections`";
        $coo_db->query($t_sql);

        $t_sql = "ALTER TABLE `language_sections` CHANGE `lang_files_id` `language_section_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
        $coo_db->query($t_sql);

        $t_sql = "ALTER TABLE `language_sections` CHANGE `file_path` `section_name` VARCHAR( 255 ) NOT NULL";
        $coo_db->query($t_sql);
    }
}

$t_check = $coo_db->query("SHOW tables LIKE 'gm_lang_files_content'", true);
if($t_check->num_rows > 0)
{
    $t_check = $coo_db->query("SHOW tables LIKE 'language_section_phrases'", true);
    if($t_check->num_rows == 0)
    {
        $t_sql = "RENAME TABLE `gm_lang_files_content` TO `language_section_phrases`";
        $coo_db->query($t_sql);

        $t_sql = "ALTER TABLE `language_section_phrases` CHANGE `lang_files_content_id` `language_section_phrase_id` INT( 11 ) NOT NULL AUTO_INCREMENT";
        $coo_db->query($t_sql);

        $t_sql = "ALTER TABLE `language_section_phrases` CHANGE `lang_files_id` `language_section_id` INT( 11 ) NOT NULL";
        $coo_db->query($t_sql);

        $t_sql = "ALTER TABLE `language_section_phrases` CHANGE `constant_name` `phrase_name` VARCHAR( 255 ) NOT NULL";
        $coo_db->query($t_sql);

        $t_sql = "ALTER TABLE `language_section_phrases` CHANGE `constant_value` `phrase_value` TEXT NOT NULL";
        $coo_db->query($t_sql);
    }
}

$t_check = $coo_db->query('SHOW TABLES LIKE "language_sections"', true);
if($t_check->num_rows > 0)
{
    $t_sql   = 'SELECT' . ' main.`language_section_phrase_id`' . ' FROM' . ' (SELECT' . ' `language_section_phrase_id`,'
               . ' `language_section_id`,' . ' `phrase_name`,' . ' COUNT(*) AS variants' . ' FROM'
               . ' `language_section_phrases`' . ' GROUP BY' . ' `language_section_id`,' . ' `phrase_name`' . ' HAVING'
               . ' variants > 1' . ' ORDER BY' . ' NULL) AS sub,' . ' `language_section_phrases` AS main' . ' WHERE'
               . ' main.`language_section_phrase_id` != sub.`language_section_phrase_id` AND'
               . ' main.`language_section_id` = sub.`language_section_id` AND'
               . ' main.`phrase_name` LIKE sub.`phrase_name`';
    $t_check = $coo_db->query($t_sql);
    if(count($t_check) > 0)
    {
        $t_language_section_phrase_id_array = array();
        foreach($t_check as $t_row)
        {
            $t_language_section_phrase_id_array[] = $t_row['language_section_phrase_id'];
        }

        $t_sql = 'DELETE FROM `language_section_phrases` WHERE `language_section_phrase_id` IN (' . implode(',',
                                                                                                            $t_language_section_phrase_id_array)
                 . ')';
        $coo_db->query($t_sql);
    }

    $t_check = $coo_db->query('
	SHOW INDEX
	  FROM
		language_section_phrases
	  WHERE
		((Key_name = "language_section_phrase"
		AND Column_name = "language_section_id"
		AND Seq_in_index = 1)
		OR (Key_name = "language_section_phrase"
		AND Column_name = "phrase_name"
		AND Seq_in_index = 2))
		AND Non_unique = 0', true);
    if($t_check->num_rows < 2)
    {
        $coo_db->set_index('language_section_phrases', 'UNIQUE', array('language_section_id', 'phrase_name'),
                           'language_section_phrase');
    }
}

$t_check = $coo_db->query("DESCRIBE `version_history` 'installed'", true);
if($t_check->num_rows == 0)
{
    $t_sql = 'ALTER TABLE `version_history` ADD `installed` TINYINT NOT NULL DEFAULT "1" COMMENT "Signalisiert, ob ein Versionseintrag wirklich installiert wurde oder durch die Versionsauswahl angelegt wurde."';
    $coo_db->query($t_sql);
}

$coo_update_control = new GambioUpdateControl(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

if((isset($_GET['action']) && $_GET['action'] == 'login')
   || isset($_GET['content'])
      && in_array($_GET['content'], array(
        'setup',
        'configure',
        'move',
        'chmod',
        'delete_files',
        'finish'
    ))
)
{
    if(isset($_POST['email']) && isset($_POST['password'])
       && $coo_update_control->login($_POST['email'], $_POST['password'])
    )
    {
        if(isset($_POST['no_error_output']))
        {
            $coo_update_control->set_no_error_output((boolean)(int)$_POST['no_error_output']);
        }

        if($_GET['action'] == 'login')
        {
            $t_content = 'setup';
        }

        $t_email = '';
        if(isset($_POST['email']))
        {
            $t_email = $_POST['email'];
        }

        $t_password = '';
        if(isset($_POST['password']))
        {
            $t_password = filter_password($_POST['password']);
        }

        if(isset($_POST['force_version_selection']))
        {
            $t_content = 'setup';
        }
    }
    else
    {
        $t_content = 'login';
        if(isset($_POST['email']) && isset($_POST['password']))
        {
            $t_login_notification = TEXT_LOGIN_ERROR;
        }
    }
}

header("Cache-Control: no-cache, must-revalidate");
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <title>Update Gambio GX3</title>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta http-equiv="Cache-Control" content="no-cache, must-revalidate" />
            <meta http-equiv="Pragma" content="no-cache" />
            <meta http-equiv="Expires" content="0" />
            <link type="text/css" rel="stylesheet" href="css/stylesheet.css" />
            <!--[if gte IE 9]>
            <style type="text/css">
                #main.gradient, #main .gradient.button, #main .gradient.button.red, #main .gradient.button.green {
                    filter: none;
                }
            </style><![endif]-->
            <script type="text/javascript" src="../gm/javascript/jquery/jquery.min.js"></script>
            <script type="text/javascript" src="../gm/javascript/jquery/jquery-migrate.min.js"></script>
            <script type="text/javascript"
                    src="javascript/main.js.php?language=<?php echo $t_language . '&rand=' . time(); ?>"></script>
        </head>

        <body>

            <?php if($testReqirementsResult === false): ?>
                <div class="warning" style="margin: -30px 0 30px 0;">
                    <?php echo $phpMysqlWarningMsg; ?>
                </div>
            <?php endif; ?>

            <h1>Updater</h1>
            <h2>Gambio GX3</h2>

            <div id="main" class="gradient">


                <!-- CONTENT START -->

                <div id="install_service">
                    <p><strong><?php echo HEADING_INSTALLATION_SERVICE; ?></strong></p>
                    <p>
                        <?php echo TEXT_INSTALLATION_SERVICE; ?><br /> <br /> <a href="https://www.gambio.de/km5bI"
                                                                                 class="button gradient red"
                                                                                 target="_blank"><?php echo BUTTON_GAMBIO_PORTAL; ?></a>
                    </p>


                </div>

                <?php

                switch($t_content)
                {
                    case 'move':
                        debug_notice('index.php: content \'move\' called');

                        $t_update_dir_array = unserialize(file_get_contents(DIR_FS_CATALOG
                                                                            . 'cache/update_dir_array.pdc'));
                        $coo_update_control->rebuild_gambio_update_array($t_update_dir_array);

                        $coo_update_control->get_move_form();

                        $t_delete_array = $coo_update_control->get_delete_list();
                        $t_move_array   = $coo_update_control->get_move_array();
                        $t_chmod_array  = $coo_update_control->get_chmod_array();

                        // WORKAROUND: WINDOWS SYSTEM
                        $t_move_form = $coo_update_control->get_move_form();

                        if(empty($t_move_array) && empty($t_delete_array) && empty($t_chmod_array))
                        {
                            $t_move_form = '<form id="form_move" action="index.php?content=finish&language='
                                           . $t_language . '" method="post" autocomplete="off">' . $t_move_form;
                        }
                        elseif(empty($t_move_array) && empty($t_delete_array) === false)
                        {
                            $t_move_form = '<form id="form_move" action="index.php?content=delete_files&language='
                                           . $t_language . '" method="post" autocomplete="off">' . $t_move_form;
                        }
                        elseif(empty($t_move_array))
                        {
                            $t_move_form = '<form id="form_move" action="index.php?content=chmod&language='
                                           . $t_language . '" method="post" autocomplete="off">' . $t_move_form;
                        }
                        else
                        {
                            $t_move_form = '<form id="form_move" action="index.php?content=move&language=' . $t_language
                                           . '" method="post" autocomplete="off">' . $t_move_form;
                        }

                        $t_move_form .= '<input type="hidden" name="email" value="' . htmlspecialchars($t_email,
                                                                                                       ENT_COMPAT,
                                                                                                       'UTF-8')
                                        . '" />';
                        $t_move_form .= '<input type="hidden" name="password" value="' . htmlspecialchars($t_password,
                                                                                                          ENT_COMPAT,
                                                                                                          'UTF-8')
                                        . '" />';
                        $t_move_form .= '</form>';

                        echo $t_move_form;
                        break;

                    case 'delete_files':
                        debug_notice('index.php: content \'delete_files\' called');

                        if(is_readable(DIR_FS_CATALOG . 'cache/update_dir_array.pdc') == false)
                        {
                            return;
                        }

                        $t_update_dir_array = unserialize(file_get_contents(DIR_FS_CATALOG
                                                                            . 'cache/update_dir_array.pdc'));
                        $coo_update_control->rebuild_gambio_update_array($t_update_dir_array);

                        $t_delete_form .= $coo_update_control->get_delete_form();

                        $t_delete_array = $coo_update_control->get_delete_list();

                        if(file_exists(DIR_FS_CATALOG . 'cache/additional_delete_list.pdc'))
                        {
                            $t_additional_delete_list = unserialize(file_get_contents(DIR_FS_CATALOG
                                                                                      . 'cache/additional_delete_list.pdc'));
                            clearstatcache();
                            foreach($t_additional_delete_list as $t_delete_file)
                            {
                                if(file_exists(DIR_FS_CATALOG . $t_delete_file) && empty($t_delete_file) == false)
                                {
                                    $t_delete_array[] = $t_delete_file;
                                }
                            }
                        }

                        $t_chmod_array = $coo_update_control->get_chmod_array();
                        if(empty($t_delete_array) && empty($t_chmod_array))
                        {
                            $t_delete_form = '<form id="form_delete" action="index.php?content=finish&language='
                                             . $t_language . '" method="post" autocomplete="off">' . $t_delete_form;
                        }
                        else if(empty($t_delete_array))
                        {
                            $t_delete_form = '<form id="form_delete" action="index.php?content=chmod&language='
                                             . $t_language . '" method="post" autocomplete="off">' . $t_delete_form;
                        }
                        else
                        {
                            $t_delete_form = '<form id="form_delete" action="index.php?content=delete_files&language='
                                             . $t_language . '" method="post" autocomplete="off">' . $t_delete_form;
                        }
                        $t_delete_form .= '<input type="hidden" name="email" value="' . htmlspecialchars($t_email,
                                                                                                         ENT_COMPAT,
                                                                                                         'UTF-8')
                                          . '" />';
                        $t_delete_form .= '<input type="hidden" name="password" value="' . htmlspecialchars($t_password,
                                                                                                            ENT_COMPAT,
                                                                                                            'UTF-8')
                                          . '" />';
                        $t_delete_form .= '</form>';

                        echo $t_delete_form;
                        break;

                    case 'chmod':
                        debug_notice('index.php: content \'chmod\' called');

                        $t_update_dir_array = unserialize(file_get_contents(DIR_FS_CATALOG
                                                                            . 'cache/update_dir_array.pdc'));
                        $coo_update_control->rebuild_gambio_update_array($t_update_dir_array);

                        $t_chmod_form = $coo_update_control->get_chmod_form();

                        $t_chmod_array = $coo_update_control->get_chmod_array();
                        if(empty($t_chmod_array))
                        {
                            $t_chmod_form = '<form id="form_chmod" action="index.php?content=finish&language='
                                            . $t_language . '" method="post" autocomplete="off">' . $t_chmod_form;
                        }
                        else
                        {
                            $t_chmod_form = '<form id="form_chmod" action="index.php?content=chmod&language='
                                            . $t_language . '" method="post" autocomplete="off">' . $t_chmod_form;
                        }

                        $t_chmod_form .= '<input type="hidden" name="email" value="' . htmlspecialchars($t_email,
                                                                                                        ENT_COMPAT,
                                                                                                        'UTF-8')
                                         . '" />';
                        $t_chmod_form .= '<input type="hidden" name="password" value="' . htmlspecialchars($t_password,
                                                                                                           ENT_COMPAT,
                                                                                                           'UTF-8')
                                         . '" />';
                        $t_chmod_form .= '</form>';

                        echo $t_chmod_form;
                        break;

                    case 'configure':

                        debug_notice('index.php: content \'configure\' called');

                        $t_update_dir_array = array();

                        foreach($coo_update_control->gambio_update_array as $coo_update)
                        {
                            $t_update_dir_array[] = $coo_update->get_update_dir();
                        }

                        file_put_contents(DIR_FS_CATALOG . 'cache/update_dir_array.pdc',
                                          serialize($t_update_dir_array));

                        echo '<form id="form_install" action="request_port.php" method="post">';
                        $t_update_forms = $coo_update_control->get_update_forms();
                        foreach($t_update_forms as $t_form)
                        {
                            echo $t_form;
                        }
                        echo '<br /><input id="button_install" type="button" name="proceed" value="' . BUTTON_INSTALL
                             . '" class="button green" />';
                        echo '<input type="hidden" name="email" value="' . htmlspecialchars($t_email, ENT_COMPAT,
                                                                                            'UTF-8') . '" />';
                        echo '<input type="hidden" name="password" value="' . htmlspecialchars($t_password, ENT_COMPAT,
                                                                                               'UTF-8') . '" />';
                        echo '</form>';

                        echo '<div id="update_status">';
                        echo '<p><strong>' . HEADING_PROGRESS . '</strong></p>
							<p>' . TEXT_PROGRESS
                             . '<span id="current_update"></span> <img src="images/processing.gif" width="16" height="16" /></p>';
                        echo '</div>';

                        break;

                    case 'login':

                        debug_notice('index.php: content \'login\' called');

                        echo '<form action="index.php?action=login&content=login&language=' . $t_language . '" method="post">
								<p><strong>' . HEADING_LOGIN . '</strong></p>
								<p>
									' . TEXT_LOGIN . '<br />
									<br />		
								</p>';

                        echo '<p class="error">' . $t_login_notification . '</p>';

                        if(isset($_GET['force_version_selection']))
                        {
                            echo '<input type="hidden" name="force_version_selection" value="1" />';
                        }

                        if(isset($_GET['no_error_output']))
                        {
                            echo '<input type="hidden" name="no_error_output" value="' . (int)$_GET['no_error_output']
                                 . '" />';
                        }

                        echo '		<table width="620" border="0" cellspacing="5" cellpadding="0">
									<tr>
										<td>' . LABEL_EMAIL . '</td>
										<td><input type="text" class="input_field" name="email" size="35" value="'
                             . htmlspecialchars($t_email, ENT_COMPAT, 'UTF-8') . '" /></td>
									</tr>
									<tr>
										<td>' . LABEL_PASSWORD . '</td>
										<td><input type="password" class="input_field" name="password" size="35" value="" /></td>
									</tr>
								</table>
								<br>
								<br>
								<input class="button green" type="submit" name="login" value="' . BUTTON_LOGIN . '" /> 								
							</form>';

                        break;
                    case 'setup':
                        debug_notice('index.php: content \'setup\' called');

                        $current_shop_version = $coo_update_control->get_current_shop_version();

                        if((int)$current_shop_version != 0
                           && $current_shop_version != $coo_update_control->current_db_version
                        )
                        {
                            $coo_update_control->insert_version_history_entry($coo_update_control->get_current_shop_version());
                            $coo_update_control->gambio_update_array = array();
                            $coo_update_control->load_updates();
                            $coo_update_control->sort_updates();
                        }
                        else
                        {
                            $coo_update_control->set_current_shop_version();
                        }

                        $t_force_version_selection_html = ' &nbsp;

<input type="submit" class="button red" name="force_version_selection" value="' . LABEL_FORCE_VERSION_SELECTION . '" />
<br style="clear: both;" />
<br/>
' . DESCRIPTION_FORCE_VERSION_SELECTION . '
<br/>
<br/>';

                        if(($coo_update_control->current_db_version === false && !isset($_POST['shop_version']))
                           || isset($_POST['force_version_selection'])
                        )
                        {
                            echo '<form id="form_setup" action="index.php?content=setup&language=' . $t_language
                                 . '" method="post">';
                            echo '<p><strong>' . HEADING_WHICH_VERSION . '</strong></p>
								<p>' . TEXT_WHICH_VERSION . '</p>';
                            echo '<table width="620" border="0" cellspacing="5" cellpadding="0">
								<tr>
									<td><label for="shop_version">' . LABEL_VERSION . '</label> </td>
									<td>';
                            echo '<select id="shop_version" name="shop_version">';
                            $t_check = $coo_db->query('SHOW tables LIKE "language_section_phrases"', true);
                            if($t_check->num_rows > 0)
                            {
                                echo '<option value="2.0.7c">v2.0.7c</option>';
                                echo '<option value="2.0.8">v2.0.8</option>';
                                echo '<option value="2.0.9">v2.0.9</option>';
                                echo '<option value="2.0.10">v2.0.10</option>';
                                echo '<option value="2.0.11">v2.0.11</option>';
                                echo '<option value="2.0.12">v2.0.12</option>';
                                echo '<option value="2.0.13">v2.0.13</option>';
                                echo '<option value="2.0.14">v2.0.14</option>';
                                echo '<option value="2.0.15">v2.0.15 oder höher (v2.0.x)</option>';
                                echo '<option value="2.1.0.0">v2.1.0</option>';
                                echo '<option value="2.1.1.0">v2.1.1</option>';
                                echo '<option value="2.1.2.0">v2.1.2</option>';
                            }

                            $versions = $coo_update_control->get_versions();
                            foreach($versions as $version => $versionName)
                            {
                                echo '<option value="' . $version . '">' . $versionName . '</option>';
                            }

                            echo '</select>';
                            echo '		</td>
								</tr>
							</table><br />';
                            echo '<input type="hidden" name="email" value="' . htmlspecialchars($t_email, ENT_COMPAT,
                                                                                                'UTF-8') . '" />';
                            echo '<input type="hidden" name="password" value="' . htmlspecialchars($t_password,
                                                                                                   ENT_COMPAT, 'UTF-8')
                                 . '" />';
                            echo '<input type="submit" class="button green" name="choose_version" value="'
                                 . BUTTON_SHOW_UPDATES . '" />';

                            echo '</form>';
                        }
                        else
                        {
                            if($coo_update_control->current_db_version === false || isset($_POST['shop_version']))
                            {
                                $coo_update_control->gambio_update_array = array();
                                $coo_update_control->current_db_version  = $_POST['shop_version'];
                                $coo_update_control->insert_version_history_entry();
                                $coo_update_control->set_current_shop_version();
                                $coo_update_control->load_updates();
                                $coo_update_control->sort_updates();
                            }

                            if(empty($t_delete_array) && empty($t_chmod_array))
                            {
                                echo '<form id="form_delete" action="index.php?content=configure&language='
                                     . $t_language . '" method="post">';
                            }

                            echo '<p><strong>' . HEADING_UPDATES . '</strong></p>';

                            if(empty($coo_update_control->gambio_update_array) == false)
                            {
                                echo '<p>' . TEXT_UPDATES . '<br /><br />';

                                $updatedFilesUploaded    = true;
                                $styleEditFilesUploaded  = true;
                                $styleEdit3FilesUploaded = true;
                                $updatesCount            = count($coo_update_control->gambio_update_array);

                                $shopFilesContainer       = array();
                                $styleEdit2FilesContainer = array();
                                $styleEdit3FilesContainer = array();

                                foreach($coo_update_control->gambio_update_array as $key => $coo_update_model)
                                {
                                    foreach($shopFilesContainer as &$shopFiles)
                                    {
                                        $requirementsTesting->filterFileList($shopFiles,
                                                                             $coo_update_model->get_update_name());
                                    }

                                    foreach($styleEdit2FilesContainer as &$styleEdit2Files)
                                    {
                                        $requirementsTesting->filterFileList($styleEdit2Files,
                                                                             $coo_update_model->get_update_name());
                                    }

                                    foreach($styleEdit3FilesContainer as &$styleEdit3Files)
                                    {
                                        $requirementsTesting->filterFileList($styleEdit3Files,
                                                                             $coo_update_model->get_update_name());
                                    }

                                    $newest = (($key + 1) === $updatesCount);
                                    $updatedFilesUploaded &= $requirementsTesting->testUpdateFiles($coo_update_model->get_update_name(),
                                                                                                   $newest);
                                    $styleEditFilesUploaded &= $requirementsTesting->testStyleEditFiles($coo_update_model->get_update_name());
                                    $styleEdit3FilesUploaded &= $requirementsTesting->testStyleEdit3Files($coo_update_model->get_update_name());

                                    echo '- ' . $coo_update_model->get_update_name() . '<br />';

                                    $info = $requirementsTesting->getInfo();

                                    if(is_array($info['updatedFiles']))
                                    {
                                        $shopFilesContainer[] = $info['updatedFiles'];
                                    }

                                    if(is_array($info['styleEditV2Files']))
                                    {
                                        $styleEdit2FilesContainer[] = $info['styleEditV2Files'];
                                    }

                                    if(is_array($info['styleEditV3Files']))
                                    {
                                        $styleEdit3FilesContainer[] = $info['styleEditV3Files'];
                                    }
                                }
    
                                if(isset($coo_update_model))
                                {
                                    foreach($shopFilesContainer as &$shopFiles)
                                    {
                                        $requirementsTesting->filterFileList($shopFiles,
                                                                             $coo_update_model->get_update_name());
                                    }
    
                                    foreach($styleEdit2FilesContainer as &$styleEdit2Files)
                                    {
                                        $requirementsTesting->filterFileList($styleEdit2Files,
                                                                             $coo_update_model->get_update_name());
                                    }
    
                                    foreach($styleEdit3FilesContainer as &$styleEdit3Files)
                                    {
                                        $requirementsTesting->filterFileList($styleEdit3Files,
                                                                             $coo_update_model->get_update_name());
                                    }
                                }
                                
                                $missingFiles = array();
                                foreach($shopFilesContainer as $files)
                                {
                                    $missingFiles = array_merge($missingFiles, $files);
                                    $missingFiles = array_unique($missingFiles);
                                    natsort($missingFiles);
                                }

                                $missingStyleEdit2Files = array();
                                foreach($styleEdit2FilesContainer as $files)
                                {
                                    $missingStyleEdit2Files = array_merge($missingStyleEdit2Files, $files);
                                    $missingStyleEdit2Files = array_unique($missingStyleEdit2Files);
                                    natsort($missingStyleEdit2Files);
                                }

                                $missingStyleEdit3Files = array();
                                foreach($styleEdit3FilesContainer as $files)
                                {
                                    $missingStyleEdit3Files = array_merge($missingStyleEdit3Files, $files);
                                    $missingStyleEdit3Files = array_unique($missingStyleEdit3Files);
                                    natsort($missingStyleEdit3Files);
                                }

                                echo '</p>';

                                if(!$updatedFilesUploaded):
                                    ?>
                                    <div class="files-not-uploaded" style="word-wrap: break-word; margin-bottom: 30px">
                                        <h4 class="warning"
                                            style="text-align: center"><?php echo TEXT_NOT_ALL_FILES_UPLOADED; ?></h4>

                                        <button class="button"
                                                type="button"
                                                onclick="$('#not-uploaded-file-list').toggle()">Dateien anzeigen
                                        </button>
                                        <button class="button"
                                                type="button"
                                                onclick="location.reload()"><?php echo BUTTON_CHECK_MOVE ?></button>
                                        <div style="display: none; margin-top: 12px; max-height: 350px; overflow: auto"
                                             id="not-uploaded-file-list">
                                            <?php
                                            foreach($missingFiles as $file):
                                                echo $file . '<hr/>';
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                endif;

                                if(!$styleEditFilesUploaded):
                                    ?>
                                    <div class="files-not-uploaded" style="word-wrap: break-word; margin-bottom: 30px">
                                        <h4 class="warning"
                                            style="text-align: center"><?php echo TEXT_NOT_ALL_SE_V2_FILES_UPLOADED; ?></h4>

                                        <button class="button"
                                                type="button"
                                                onclick="$('#not-uploaded-se2-file-list').toggle()">Dateien anzeigen
                                        </button>
                                        &nbsp;&nbsp;
                                        <button class="button"
                                                type="button"
                                                onclick="location.reload()"><?php echo BUTTON_CHECK_MOVE ?></button>
                                        <div style="display: none; margin-top: 12px; max-height: 350px; overflow: auto"
                                             id="not-uploaded-se2-file-list">
                                            <?php
                                            foreach($missingStyleEdit2Files as $file):
                                                echo $file . '<hr/>';
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                endif;

                                if(!$styleEdit3FilesUploaded):
                                    ?>
                                    <div class="files-not-uploaded" style="word-wrap: break-word; margin-bottom: 30px">
                                        <h4 class="warning"
                                            style="text-align: center"><?php echo TEXT_NOT_ALL_SE_V3_FILES_UPLOADED; ?></h4>

                                        <button class="button"
                                                type="button"
                                                onclick="$('#not-uploaded-se3-file-list').toggle()">Dateien anzeigen
                                        </button>
                                        &nbsp;&nbsp;
                                        <button class="button"
                                                type="button"
                                                onclick="location.reload()"><?php echo BUTTON_CHECK_MOVE ?></button>
                                        <div style="display: none; margin-top: 12px; max-height: 350px; overflow: auto"
                                             id="not-uploaded-se3-file-list">
                                            <?php
                                            foreach($missingStyleEdit3Files as $file):
                                                echo $file . '<hr/>';
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                endif;

                                if($updatedFilesUploaded):
                                    echo '<input type="hidden" name="email" value="' . htmlspecialchars($t_email,
                                                                                                        ENT_COMPAT,
                                                                                                        'UTF-8')
                                         . '" />';
                                    echo '<input type="hidden" name="password" value="' . htmlspecialchars($t_password,
                                                                                                           ENT_COMPAT,
                                                                                                           'UTF-8')
                                         . '" />';
                                    echo '<input type="submit" class="button green" name="configure" value="'
                                         . BUTTON_CONFIGURE . '" />';
                                    echo $t_force_version_selection_html;
                                endif;
                            }
                            else
                            {
                                echo '<p>' . TEXT_NO_UPDATES . '<br/><br/></p>';

                                if(!$coo_update_control->is_update_mandatory())
                                {
                                    echo '<a href="' . HTTP_SERVER . DIR_WS_CATALOG
                                         . '" class="button green float_left">' . BUTTON_SHOP . '</a>';
                                }

                                echo $t_force_version_selection_html;

                                echo '</p><input type="hidden" name="email" value="' . htmlspecialchars($t_email,
                                                                                                        ENT_COMPAT,
                                                                                                        'UTF-8')
                                     . '" />';
                                echo '<input type="hidden" name="password" value="' . htmlspecialchars($t_password,
                                                                                                       ENT_COMPAT,
                                                                                                       'UTF-8')
                                     . '" />';
                            }

                            echo '</form>';
                        }

                        break;

                    case 'finish':
                        debug_notice('index.php: content \'finish\' called');

                        echo '<form id="form_install">';
                        echo '<input type="hidden" name="email" value="' . htmlspecialchars($t_email, ENT_COMPAT,
                                                                                            'UTF-8') . '" />';
                        echo '<input type="hidden" name="password" value="' . htmlspecialchars($t_password, ENT_COMPAT,
                                                                                               'UTF-8') . '" />';
                        echo '</form>';

                        echo '<script type="text/javascript">
						
							$(document).ready(function(){ 
								$("#result").show(); 
								$("#clear_cache").show();
								
								var requestHandler = new RequestHandler();
								requestHandler.set_installed_version();
							});

						</script>';
                        break;

                    case 'language':
                    default:
                        debug_notice('index.php: content \'language\' called');

                        $noErrorOutput = '';
                        if(isset($_GET['no_error_output']))
                        {
                            $noErrorOutput = '&no_error_output=' . (int)$_GET['no_error_output'];
                        }

                        echo '<p><strong>' . HEADING_INSTALLATION . '</strong></p>
							<p>
								' . TEXT_INSTALLATION . '<br />
								<br />
								<a href="index.php?content=login&language=german' . $noErrorOutput . '" class="button green">Deutsch</a>&nbsp;
								<a href="index.php?content=login&language=english' . $noErrorOutput . '" class="button green">English</a>
							</p>';

                        break;
                }

                ?>

                <div id="result">

                    <div id="clear_cache">
                        <p><strong><?php echo HEADING_INSTALLATION_CLEAR_CACHE; ?></strong></p>
                        <p><?php echo TEXT_INSTALLATION_CLEAR_CACHE; ?> <img src="images/processing.gif"
                                                                             width="16"
                                                                             height="16" /></p>
                    </div>

                    <?php
                    $hasUpdates = count($coo_update_control->gambio_update_array) !== 0;
                    if($hasUpdates)
                    {
                        $latestUpdate  = $coo_update_control->gambio_update_array[count($coo_update_control->gambio_update_array)
                                                                                  - 1];
                        $latestVersion = $latestUpdate->get_update_version();
                    }
                    ?>

                    <div id="installation_success">
                        <p><strong><?php echo HEADING_INSTALLATION_SUCCESS; ?></strong></p>
                        <p>
                            <?php
                            echo TEXT_INSTALLATION_SUCCESS;
                            if($hasUpdates && $coo_update_control->current_db_version < '2.3.1.0'
                               && $latestVersion >= '2.3.1.0'
                            )
                            {
                                echo TEXT_INSTALLATION_SUCCESS_WARNING;
                            }

                            $db = new DatabaseModel(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

                            $check = $db->query("SELECT * FROM `configuration` 
												WHERE 
													`configuration_key` = 'MODULE_PAYMENT_PAYPALNG_STATUS' AND 
													`configuration_value` = 'True'", true);
                            if($check->num_rows)
                            {
                                $check = $db->query("SELECT * FROM `configuration` 
												WHERE 
													`configuration_key` = 'MODULE_PAYMENT_PAYPAL3_STATUS' AND 
													`configuration_value` = 'True'", true);
                                if($check->num_rows === 0)
                                {
                                    echo '<div>' . TEXT_PAYPAL_NOTIFICATION . '</div>';
                                }
                            }

                            ?>
                        </p>
                    </div>
                    <div id="installation_success_cache_error">
                        <p><strong><?php echo HEADING_INSTALLATION_SUCCESS; ?></strong></p>
                        <p>
                            <?php
                            echo TEXT_INSTALLATION_SUCCESS_CACHE_REBUILD_ERROR;
                            if($hasUpdates && $coo_update_control->current_db_version < '2.3.1.0'
                               && $latestVersion >= '2.3.1.0'
                            )
                            {
                                echo TEXT_INSTALLATION_SUCCESS_WARNING;
                            }

                            $db = new DatabaseModel(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

                            $check = $db->query("SELECT * FROM `configuration` 
												WHERE 
													`configuration_key` = 'MODULE_PAYMENT_PAYPALNG_STATUS' AND 
													`configuration_value` = 'True'", true);
                            if($check->num_rows)
                            {
                                $check = $db->query("SELECT * FROM `configuration` 
													WHERE 
														`configuration_key` = 'MODULE_PAYMENT_PAYPAL3_STATUS' AND 
														`configuration_value` = 'True'", true);
                                if($check->num_rows === 0)
                                {
                                    echo '<div>' . TEXT_PAYPAL_NOTIFICATION . '</div>';
                                }
                            }
                            ?>
                        </p>
                    </div>

                    <div id="errors_report">
                        <?php echo TEXT_ERRORS; ?><br />
                        <div id="errors_container"></div>
                        <br /><br />
                    </div>

                    <div id="sql_errors_report">
                        <?php echo TEXT_SQL_ERRORS; ?><br /> <textarea id="sql_errors" readonly="readonly"></textarea>
                        <br /><br />
                    </div>

                    <div id="conflicts_report"><?php echo TEXT_SECTION_CONFLICT_REPORT; ?></div>

                    <a href="<?php echo HTTP_SERVER . DIR_WS_CATALOG; ?>"
                       class="button green"><?php echo BUTTON_SHOP; ?></a>

                </div>


                <!-- CONTENT END -->

                <img id="logo" src="images/gambio_logo.png" alt="" />
            </div>

            <div id="copyright">
                <strong><a href="https://www.gambio.de" target="_blank"><strong>Gambio.de</strong></a> -
                    Gambio-Updater &copy; 2016 Gambio GmbH</strong><br /> Gambio GmbH provides no warranty. The
                Shopsoftware is <br /> redistributable under the <a href="http://www.gnu.org/licenses/gpl-2.0.html"
                                                                    target="_blank">GNU General Public License (Version
                    2)</a><br /> based on: E-Commerce Engine Copyright &copy; 2006 <a href="http://www.xt-commerce.com"
                                                                                      target="_blank">xt:Commerce</a>,
                <br /> <a href="http://www.xt-commerce.com" target="_blank">xt:Commerce</a> provides no warranty.
            </div>

        </body>
    </html>
<?php
$coo_logger = LogControl::get_instance();
$coo_logger->write_stack();
