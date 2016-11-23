<?php
/* --------------------------------------------------------------
   get_robots.php 2014-11-10 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');

/*
 * creates a robot file and exports it
 *
 * @param string $p_shop_path Shop path
 * @return string
 */
function get_robots($p_shop_path, $p_content_links = '')
{
	$t_content_links = '';
	if($p_content_links != '')
	{
		$t_content_links = (string)$p_content_links;
	}
	
	if($t_content_links === '')
	{
		/* 2 = Datenschutz
		 * 3 = AGB
		 * 4 = Impressum
		 * 9 = Widerruf
		 * 3889891 = Versand- & Zahlungsbedingungen
		 * 3889895 = Widerrufsrecht und Widerrufsformular
		 */
		$t_group_ids = array(2, 3, 4, 9, 3889891, 3889895);
		
		$languagesArray = gm_get_language();
	
		foreach($t_group_ids AS $t_content_group_id)
		{
			$t_content_links .= "\nDisallow: {PATH}shop_content.php?coID=" . $t_content_group_id;

			$coo_content_manager_group = MainFactory::create_object('GMDataObjectGroup', array('content_manager', array('content_group' => $t_content_group_id)));
			$t_content_manager_array = $coo_content_manager_group->get_data_objects_array();
			foreach($t_content_manager_array AS $coo_content_manager)
			{
				$t_content_links .= "\nDisallow: {PATH}info/" . $coo_content_manager->get_data_value('gm_url_keywords') . '.html';
				foreach($languagesArray as $language)
				{
					$t_content_links .= "\nDisallow: {PATH}" . $language['code'] . "/info/" . $coo_content_manager->get_data_value('gm_url_keywords') . '.html';	
				}
			}
			$t_content_links .= "\nDisallow: {PATH}popup_content.php?coID=" . $t_content_group_id;
		}	
	}
		
    $t_file = DIR_FS_CATALOG.'export/robots.txt.tpl';

	$t_lines = file($t_file);
	
    $t_result = '';
    foreach($t_lines as $line) {
        $t_result .= str_replace('{PATH}', $p_shop_path, $line);
    }
	$t_result .= str_replace('{PATH}', $p_shop_path, $t_content_links);

    // check SSL
    if(ENABLE_SSL_CATALOG === 'true' || ENABLE_SSL_CATALOG === true) {
        // check if ssl is in a subdirectory
        $t_http_parsed = parse_url(HTTPS_CATALOG_SERVER);
        if(isset($t_http_parsed['path'])) {
            $t_result .= "\n\n";
            $t_path = substr($t_http_parsed['path'], 1);
            if(substr($t_path, -1, 1) != '/') {
                $t_path = $t_path.'/';
            }
            // again for ssl
            foreach($t_lines as $line) {
                $t_result .= str_replace('{PATH}', $p_shop_path.$t_path, $line);
            }
			$t_result .= str_replace('{PATH}', $p_shop_path.$t_path, $t_content_links);
        }
    }

	// convert into UNIX-file format
	$t_result = str_replace("\r\n", "\n", $t_result);
	// convert into Windows-file format
	$t_result = str_replace("\n", "\r\n", $t_result);

    header("Expires: Mon, 26 Nov 1962 00:00:00 GMT");
    header("Last-Modified: ".gmdate("D,d M Y H:i:s")." GMT");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Content-Type: Application/octet-stream");
    header("Content-disposition: attachment; filename=\"robots.txt\"");

    echo $t_result;
    exit;
}

/**
 * check if robots.txt obsolete
 *
 * @param string $p_shop_path Shop path
 * @return bool
 */
function check_robots($p_shop_path)
{
	$t_file = substr(DIR_FS_CATALOG, 0, (strlen(DIR_WS_CATALOG) * -1)) . '/robots.txt';
	
	// check if robots.txt is a regular file
	if(!is_file($t_file)) {
		return true;
	}
	
	$file = file_get_contents($t_file);
	$position = strpos($file, $p_shop_path.'admin/');
	if($position !== false) {
		return true;
	}

	return false;
}