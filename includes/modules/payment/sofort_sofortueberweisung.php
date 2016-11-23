<?php
/* --------------------------------------------------------------
   sofort_sofortueberweisung.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * @version SOFORT Gateway 5.2.0 - $Date: 2013-04-22 14:00:13 +0200 (Mon, 22 Apr 2013) $
 * @author SOFORT AG (integration@sofort.com)
 * @link http://www.sofort.com/
 *
 * Copyright (c) 2012 SOFORT AG
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 *
 * $Id: sofort_sofortueberweisung.php 6097 2013-04-22 12:00:13Z rotsch $
 */

require_once(DIR_FS_CATALOG.'callback/sofort/sofort.php');
require_once(DIR_FS_CATALOG.'callback/sofort/library/sofortLib.php');

class sofort_sofortueberweisung_ORIGIN extends sofort {

	public function __construct() {
		global $order;
		
		parent::__construct();
		
		$this->_checkExistingSofortConstants('su');
		$this->code = 'sofort_sofortueberweisung';
		$this->title = MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_TEXT_TITLE_ADMIN;
		$this->title_extern = MODULE_PAYMENT_SOFORT_SU_TEXT_TITLE;
		$this->paymentMethod = 'SU';
		
		if (defined('MODULE_PAYMENT_SOFORT_SU_KS_STATUS') && MODULE_PAYMENT_SOFORT_SU_KS_STATUS == 'True') $this->title_extern = MODULE_PAYMENT_SOFORT_SU_KS_TEXT_TITLE;
		
		if(defined('MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT') && MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT == 'True') $this->title_extern .= ' ' . MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT_TEXT;
		
		$this->enabled = ((defined('MODULE_PAYMENT_SOFORT_SU_STATUS') && MODULE_PAYMENT_SOFORT_SU_STATUS == 'True') ? true : false);
		$this->description = MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION.'<br />'.MODULE_PAYMENT_SOFORT_MULTIPAY_VERSIONNUMBER.': '.HelperFunctions::getSofortmodulVersion();
		
		if ($this->_isInstalled() && !$this->_modulVersionCheck()) {
			$this->description = '<span style ="color:red; font-weight: bold; font-size: 1.2em">'.MODULE_PAYMENT_SOFORT_MULTIPAY_UPDATE_NOTICE.'</span><br /><br />'.$this->description;
		}
		
		$this->description .= MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_EXTRA;
		$this->sort_order = (defined('MODULE_PAYMENT_SOFORT_SU_SORT_ORDER') ? MODULE_PAYMENT_SOFORT_SU_SORT_ORDER : false);
		
		if (is_object($order)) $this->update_status();
		
		if (defined('MODULE_PAYMENT_SOFORT_SU_STATUS')) {
			$this->sofort = new SofortLib_Multipay(MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY);
			$this->sofort->setVersion(HelperFunctions::getSofortmodulVersion());
			
			if (defined('MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED') && MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED == "True") $this->sofort->setLogEnabled();
		}
	}
	
	
	function selection() {
		if (!parent::selection()) {
			$this->sofort->log("Notice: Paymentmethod ".$this->code." will be deactivated for selection.");
			$this->enabled = false;
			
			return false;
		}
		
		$title = '';
		
		switch (MODULE_PAYMENT_SOFORT_MULTIPAY_IMAGE) {
			case 'Logo & Text':
				if(MODULE_PAYMENT_SOFORT_SU_KS_STATUS == 'True') {
					$title = $this->_setImageText('logo_155x50.png', MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_TEXT_KS);
				} else {
					$title = $this->_setImageText('logo_155x50.png', MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_TEXT);
				}
				break;
			case 'Infographic':
				if(MODULE_PAYMENT_SOFORT_SU_KS_STATUS == 'True') {
					$title = $this->_setImageText('banner_400x100_ks.png', '');
				} else {
					$title = $this->_setImageText('banner_300x100.png', '');
				}
				break;
		}
		
		//add ks-link, if ks is active
		$title = str_replace('[[link_beginn]]', '<a href="'.MODULE_PAYMENT_SOFORT_MULTIPAY_SU_CHECKOUT_INFOLINK_KS.'" target="_blank" style="cursor: pointer; text-decoration: underline;">', $title);
		$title = str_replace('[[link_end]]', '</a>', $title);
		$cost = '';
		
		if(array_key_exists('ot_sofort',  $GLOBALS)) $cost = $GLOBALS['ot_sofort']->get_percent($this->code, 'price');
		
		return array('id' => $this->code , 'module' => $this->title_extern, 'description' => $title, 'module_cost' => $cost);
	}
	
	
	function _setImageText($image, $text) {
		$lng = HelperFunctions::getShortCode($_SESSION['language']);
		$image = 'https://images.sofort.com/'.$lng.'/su/'.$image;
		$image = xtc_image($image, MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGEALT);
		$title = MODULE_PAYMENT_SOFORT_SU_TEXT_DESCRIPTION_CHECKOUT_PAYMENT_IMAGE;
		$title = str_replace('{{image}}', $image, $title);
		$title = str_replace('{{text}}', $text, $title);
		
		return $title;
	}
	
	
	function install() {
		$sofortStatuses = $this->_insertAndReturnSofortStatus();
		$checkStatus = (isset($sofortStatuses['check'])&& !empty($sofortStatuses['check']))? $sofortStatuses['check'] : '';
		$refundedStatus = (isset($sofortStatuses['refunded'])&& !empty($sofortStatuses['refunded']))? $sofortStatuses['refunded'] : '';
		$confirmedStatus = (isset($sofortStatuses['translate_confirmed'])&& !empty($sofortStatuses['translate_confirmed']))? $sofortStatuses['translate_confirmed'] : '';
		$unchangedStatus = (isset($sofortStatuses['unchanged'])&& !empty($sofortStatuses['unchanged']))? $sofortStatuses['unchanged'] : '';
		
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_STATUS', 'False', '6', '1', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_KS_STATUS', 'False', '6', '30', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED', '', '6', '12', now())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_SORT_ORDER', '0', '6', '16', now())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT', 'False', '6', '5', 'xtc_cfg_select_option(array(\'True\', \'False\'), ', now())");
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, use_function, set_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_ZONE', '0', '6', '13', 'xtc_get_zone_class_title', 'xtc_cfg_pull_down_zone_classes(', now())");
		//xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." ( configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_CHECK_STATUS_ID', '".HelperFunctions::escapeSql($checkStatus)."',  '6', '35', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
		//"Best�tigt": pending-not_credited_yet
		//Important notice: constantname is also used for status: untraceable-sofort_bank_account_needed
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID', '".HelperFunctions::escapeSql($confirmedStatus)."',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
		//"Bestellung pr�fen": loss-not_credited
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID', '".HelperFunctions::escapeSql($checkStatus)."',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
		//"Geldeingang": received-credited
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID', '".HelperFunctions::escapeSql($unchangedStatus)."',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
		//"Teilr�ckbuchung": refunded-compensation
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID', '".HelperFunctions::escapeSql($unchangedStatus)."',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
		//"Vollst�ndige R�ckbuchung": refunded-refunded
		xtc_db_query("INSERT INTO ".TABLE_CONFIGURATION." (configuration_key, configuration_value,  configuration_group_id, sort_order, set_function, use_function, date_added) VALUES ('MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID', '".HelperFunctions::escapeSql($refundedStatus)."',  '6', '30', 'xtc_cfg_pull_down_order_statuses(', 'xtc_get_order_status_name', now())");
		
		//install shared keys, that are used by all/most multipay-modules
		parent::install();
	}
	
	
	function remove() {
		xtc_db_query("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_SU%'");
		xtc_db_query("DELETE FROM ".TABLE_CONFIGURATION." WHERE configuration_key LIKE 'MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG%'");
		
		//if this is the last removing of a multipay-paymentmethod --> we also remove all shared keys, that are used by all/most multipay-modules
		parent::remove();
	}
	
	
	function keys() {
		parent::keys();
		
		return array(
			'MODULE_PAYMENT_SOFORT_SU_STATUS' ,
			'MODULE_PAYMENT_SOFORT_MULTIPAY_APIKEY',
			'MODULE_PAYMENT_SOFORT_MULTIPAY_AUTH',
			'MODULE_PAYMENT_SOFORT_SU_RECOMMENDED_PAYMENT',
			'MODULE_PAYMENT_SOFORT_MULTIPAY_IMAGE' ,
			'MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_1',
			'MODULE_PAYMENT_SOFORT_MULTIPAY_REASON_2' ,
			'MODULE_PAYMENT_SOFORT_SOFORTUEBERWEISUNG_ALLOWED' ,
			'MODULE_PAYMENT_SOFORT_SU_ZONE',
			'MODULE_PAYMENT_SOFORT_SU_SORT_ORDER',
			'MODULE_PAYMENT_SOFORT_MULTIPAY_PROF_SETTINGS',
			'MODULE_PAYMENT_SOFORT_MULTIPAY_TEMP_STATUS_ID',
			'MODULE_PAYMENT_SOFORT_MULTIPAY_ABORTED_STATUS_ID',
			'MODULE_PAYMENT_SOFORT_SU_PEN_NOT_CRE_YET_STATUS_ID',
			'MODULE_PAYMENT_SOFORT_SU_LOS_NOT_CRE_STATUS_ID',
			'MODULE_PAYMENT_SOFORT_SU_REC_CRE_STATUS_ID',
			'MODULE_PAYMENT_SOFORT_SU_REF_COM_STATUS_ID',
			'MODULE_PAYMENT_SOFORT_SU_REF_REF_STATUS_ID',
			//'MODULE_PAYMENT_SOFORT_SU_CHECK_STATUS_ID',
			'MODULE_PAYMENT_SOFORT_SU_KS_STATUS',
			//'MODULE_PAYMENT_SOFORT_MULTIPAY_LOG_ENABLED',
		);
	}
}
MainFactory::load_origin_class('sofort_sofortueberweisung');