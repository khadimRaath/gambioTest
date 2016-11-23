<?php
/* --------------------------------------------------------------
  OrderAdminAjaxHandler.inc.php 2015-06-22 tb@gambio
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

class OrderAdminAjaxHandler extends AjaxHandler
{
	protected $languageTextManager;

	/**
	 *
	 */
	function __construct()
	{
		$this->languageTextManager = MainFactory::create_object('LanguageTextManager', array('orders'));
	}
	
	function get_permission_status($p_customers_id = NULL)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}

		return false;
	}

	function proceed()
	{
		$response = array();
		$action   = $this->v_data_array['GET']['action'];

		$funcName = '_action' . ucfirst($action);

		if(method_exists($this, $funcName))
		{
			$response = call_user_func(array($this, $funcName));
		}

		/** @noinspection PhpUndefinedClassInspection */
		$json       = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$outputJson = $json->encode($response);

		$this->v_output_buffer = $outputJson;

		return true;
	}
	
	
	protected function _actionDownloadPdf()
	{
		$t_type = trim(basename($_GET['type']));
		$t_file = trim(basename($_GET['file']));
		if($t_type != '' && $t_file != '' && file_exists(DIR_FS_CATALOG . 'export/' . $t_type . '/' . $t_file))
		{
			$t_file_parts = explode('__', $t_file);

			if($t_type == 'invoice')
			{
				$t_file_name = $this->languageTextManager->get_text('ADMIN_INVOICE_PDF_NAME');
				$t_file_name = str_replace('{ORDER_ID}', $t_file_parts[0], $t_file_name);
				$t_file_name = str_replace('{INVOICE_ID}', $t_file_parts[1], $t_file_name);
				$t_file_name = str_replace('{DATE}', $t_file_parts[2], $t_file_name);
				$t_file_name = xtc_cleanName($t_file_name, '_');
			}
			elseif($t_type == 'packingslip')
			{
				$t_file_name = $this->languageTextManager->get_text('ADMIN_PACKINGSLIP_PDF_NAME');
				$t_file_name = str_replace('{ORDER_ID}', $t_file_parts[0], $t_file_name);
				$t_file_name = str_replace('{DATE}', $t_file_parts[1], $t_file_name);
				$t_file_name = xtc_cleanName($t_file_name, '_');
			}

			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header('Content-Disposition: attachment; filename="' . $t_file_name . '.pdf"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			readfile(DIR_FS_CATALOG . 'export/' . $t_type . '/' . $t_file);
		}
		xtc_db_close();
		exit(0);
	}


	protected function _actionShowPdf()
	{
		$t_type = trim(basename($_GET['type']));
		$t_file = trim(basename($_GET['file']));
		if($t_type != '' && $t_file != '' && file_exists(DIR_FS_CATALOG . 'export/' . $t_type . '/' . $t_file))
		{
			header('Content-type: application/pdf');
			echo file_get_contents(DIR_FS_CATALOG . 'export/' . $t_type . '/' . $t_file);
		}
		xtc_db_close();
		exit(0);
	}


	protected function _actionDeletePdf()
	{
		$_SESSION['coo_page_token']->is_valid($this->v_data_array['POST']['page_token']);
		
		$response = array('status' => 'error');
		
		$t_type = trim(basename($_POST['type']));
		$t_file = trim(basename($_POST['file']));
		if($t_type != '' && $t_file != '' && file_exists(DIR_FS_CATALOG . 'export/' . $t_type . '/' . $t_file))
		{
			@unlink(DIR_FS_CATALOG . 'export/' . $t_type . '/' . $t_file);
			$response['status'] = 'success';
		}
		$response['page_token'] = $_SESSION['coo_page_token']->generate_token();
		return $response;
	}
}