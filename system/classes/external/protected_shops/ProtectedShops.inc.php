<?php
/* --------------------------------------------------------------
	ProtectedShops.inc.php 2014-05-26_1650 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------


	based on:
	(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
	(c) 2002-2003 osCommerce(ot_cod_fee.php,v 1.02 2003/02/24); www.oscommerce.com
	(C) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers ; http://www.themedia.at & http://www.oscommerce.at
	(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: ot_cod_fee.php 1003 2005-07-10 18:58:52Z mz $)

	Released under the GNU General Public License
	---------------------------------------------------------------------------------------*/

class ProtectedShops
{
	protected $_api_url;
	protected $_timeout;
	protected $_config;
	protected $_txt;
	public $valid_formats;

	const CFG_PREFIX = 'PROTECTEDSHOPS_';

	public function __construct()
	{
		$this->_api_url = 'https://www.protectedshops.de/api/';
		$this->valid_formats = array('Text', 'Html', 'HtmlLite', 'Pdf');
		$this->_timeout = 5;
		$this->_loadConfig();
		$this->_txt = MainFactory::create_object('LanguageTextManager', array('protectedshops', $_SESSION['languages_id']));
		$this->_logger = MainFactory::create_object('FileLog', array('protectedshops', true));
	}

	/*
	** Logging
	*/

	public function log($message) {
		$microtime = microtime(true);
		$timestamp = date('Ymd_His', floor($microtime));
		$timestamp .= '.' . sprintf('%03d', (int)(($microtime - floor($microtime)) * 1000));
		$this->_logger->write($timestamp.' | '.$message."\n");
	}

	/*
	** I18N
	*/

	public function get_text($name) {
		$replacement = $this->_txt->get_text($name);
		return $replacement;
	}

	public function replaceTextPlaceholders($content) {
		while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
			$replacement = $this->get_text($matches[1]);
			if(empty($replacement)) {
				$replacement = $matches[1];
			}
			$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
		}
		return $content;
	}

	/*
	** Configuration
	*/

	protected function _loadConfig()
	{
		$this->_config = array(
				'shop_id' => '',
				# 'user' => '',
				# 'pass' => '',
				'content_group_impressum' => '-1',
				'content_group_agb' => '-1',
				'content_group_datenschutz' => '-1',
				'content_group_widerruf' => '-1',
				'content_group_rueckgabe' => '-1',
				'content_group_versandinfo' => '-1',
				'content_group_handlungsanleitung' => '-1',
				'content_group_batteriegesetz' => '-1',
				'use_for_pdf_conditions' => '0',
				'use_for_pdf_withdrawal' => '0',
				'update_interval' => '0',
			);
		foreach(array_keys($this->_config) as $cfg_key)
		{
			$cfg_value = gm_get_conf(self::CFG_PREFIX.strtoupper($cfg_key));
			if(empty($cfg_value) !== true)
			{
				$this->_config[$cfg_key] = $cfg_value;
			}
		}
	}

	public function setConfig($p_config_array)
	{
		foreach(array_keys($p_config_array) as $cfg_key)
		{
			if(array_key_exists($cfg_key, $this->_config))
			{
				$this->_config[$cfg_key] = $p_config_array[$cfg_key];
				gm_set_conf(self::CFG_PREFIX.strtoupper($cfg_key), $p_config_array[$cfg_key]);
			}
		}
	}

	public function getConfig()
	{
		return $this->_config;
	}

	public function isConfigured()
	{
		$t_is_configured = empty($this->_config['shop_id']) == false /* && empty($this->_config['user']) == false && empty($this->_config['pass']  == false */;
		return $t_is_configured;
	}

	/*
	** Protected Shops API
	*/

	public function doRequest($p_params)
	{
		$default_params = array(
				'Platform' => 'OnlineShop',
				'ShopId' => $this->_config['shop_id'],
			);

		$params = array_merge($default_params, $p_params);

		$ch = curl_init();
		$curl_options = array(
				CURLOPT_URL => $this->_api_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $params,
				CURLOPT_SSL_VERIFYPEER => false,
				#CURLOPT_USERPWD => $this->_config['user'].':'.$this->_config['pass'],
				CURLOPT_TIMEOUT => $this->_timeout,
			);
		curl_setopt_array($ch, $curl_options);
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if($errno !== 0)
		{
			throw new Exception($error);
		}

		$xml = simplexml_load_string($result);
		$t_error_msg = (string)$xml->error->msg;
		if(empty($t_error_msg) !== true)
		{
			//echo $result;
			throw new Exception($t_error_msg);
		}
		return $xml;
	}

	public function checkMD5($data)
	{
		#$hashdata = $this->_config['user'].$this->_config['pass'].$data;
		$hashdata = $data;
		$my_hash = md5($hashdata);
		return $my_hash;
	}

	public function getDocumentInfo()
	{
		$params = array(
				'Request' => 'GetDocumentInfo',
			);
		$result = $this->doRequest($params);
		$docinfo = false;
		if(isset($result->DocumentDate))
		{
			$docinfo = array();
			foreach($result->DocumentDate->children() as $docdate)
			{
				$docinfo[$docdate->getName()] = (string)$docdate;
			}
		}
		return $docinfo;
	}

	public function getDocument($p_document, $p_format = 'Text')
	{
		if(in_array($p_format, $this->valid_formats) === false)
		{
			throw new Exception('format unsupported');
		}

		$params = array(
				'Request' => 'GetDocument',
				'Document' => $p_document,
				'Format' => $p_format,
			);

		$result = $this->doRequest($params);

		$t_doc = (string)$result->Document;
		if($p_format == 'Text')
		{
			# work around SimpleXML/libxml converting DOS line endings (\r\n) to UNIX line endings (\n)
			$t_doc = str_replace("\n", "\r\n", $t_doc);
		}

		$t_check_md5 = $this->checkMD5($t_doc);
		if($t_check_md5 != (string)$result->MD5)
		{
			throw new Exception('MD5 mismatch! '.$t_check_md5.' vs. '.(string)$result->MD5);
		}
		return $result;
	}

	public function storeDocument($p_document_name, $p_document_date, $p_document_type, $p_md5, $p_content)
	{
		$t_insert_query =
			'INSERT INTO `protectedshops`
			SET
				`document_name` = \':document_name\',
				`document_date` = \':document_date\',
				`document_type` = \':document_type\',
				`md5` = \':md5\',
				`content` = \':content\'
			';
		$t_insert_query = strtr($t_insert_query, array(
				':document_name' => xtc_db_input($p_document_name),
				':document_type' => xtc_db_input($p_document_type),
				':document_date' => xtc_db_input($p_document_date),
				':md5' => xtc_db_input($p_md5),
				':content' => xtc_db_input($p_content),
			));
		xtc_db_query($t_insert_query);
	}

	public function getLatestDocument($p_document_name, $p_document_type)
	{
		$t_query =
			'SELECT * FROM `protectedshops`
			WHERE
				`document_name` = \':document_name\' AND
				`document_type` = \':document_type\'
			ORDER BY
				`document_date` DESC
			LIMIT 1';
		$t_query = strtr($t_query, array(
				':document_name' => xtc_db_input($p_document_name),
				':document_type' => xtc_db_input($p_document_type),
			));
		$t_document = false;
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_document = $t_row;
		}
		return $t_document;
	}

	public function updateDocument($p_document_name, $p_document_type = null, $p_force = false)
	{
		#$p_force = true;
		foreach($this->valid_formats as $t_format)
		{
			$this->log('Updating '.$p_document_name.' ('.$t_format.')');
			$t_document = $this->getDocument($p_document_name, $t_format);
			$t_latest_document = $this->getLatestDocument($p_document_name, $t_format);
			$t_document_is_newer = true;
			if($t_latest_document !== false)
			{
				$t_local_timestamp = strtotime($t_latest_document['document_date']);
			}
			else {
				$t_local_timestamp = 0;
			}
			$t_remote_timestamp = strtotime((string)$t_document->DocumentDate);
			$t_document_is_newer = $t_local_timestamp < $t_remote_timestamp;

			if($t_document_is_newer == true || $p_force == true)
			{
				$this->log('Storing updated version of '.$p_document_name);
				if($t_format === 'Pdf')
				{
					$t_document_content = '';
					$t_file_content = base64_decode((string)$t_document->Document);
				}
				else
				{
					$t_document_content = (string)$t_document->Document;
					$t_file_content = $t_document_content;
				}
				$this->storeDocument($p_document_name, (string)$t_document->DocumentDate, $t_format, (string)$t_document->MD5, $t_document_content);
				switch($t_format)
				{
					case 'Text':
						$t_filename = 'ps_'.strtolower($p_document_name).'.txt';
						break;
					case 'Html':
						$t_filename = 'ps_'.strtolower($p_document_name).'.html';
						break;
					case 'HtmlLite':
						$t_filename = 'ps_'.strtolower($p_document_name).'_lite.html';
						break;
					case 'Pdf':
						$t_filename = 'ps_'.strtolower($p_document_name).'.pdf';
						break;
					default:
						$t_filename = false;
				}


				if($t_filename !== false)
				{
					$t_full_file_path = DIR_FS_CATALOG.'media/content/'.$t_filename;
					$t_file_is_writable = is_writable($t_full_file_path);
					$t_media_path = dirname($t_full_file_path);
					$t_dir_is_writable = is_writable($t_media_path);

					if((file_exists($t_full_file_path) && is_writable($t_full_file_path) === false) || (file_exists($t_full_file_path) === false && $t_dir_is_writable === false))
					{
						throw new Exception($t_full_file_path .' is not writable!');
					}
					file_put_contents($t_full_file_path, $t_file_content);
				}
			}
			else {
				$this->log('Local document is up-to-date, no update required');
			}
		}
	}

	public function useDocument($p_document_name)
	{
		$t_languages_id = 2;
		$t_content_manager_type = 'HtmlLite';
		$t_latest_document = $this->getLatestDocument($p_document_name, $t_content_manager_type);

		if($t_latest_document === false)
		{
			$this->log('Cannot use document '.$p_document_name.' for content manager entry or PDFs: data missing');
			return false;
		}

		$t_content_group_id = $this->_config['content_group_'.strtolower($p_document_name)];
		if($t_content_group_id < 0)
		{
			$this->log('Not using '.$p_document_name.' for content manager entry: no entry configured');
		}
		else
		{
			$this->log('Using document '.$p_document_name.' for content manager entry, group '.$t_content_group_id);
			$t_cm_query =
				'UPDATE `content_manager`
				SET
					`content_text` = \':content_text\',
					`gm_last_modified` = \':gm_last_modified\',
					`content_file` = \':content_file\'
				WHERE
					`content_group` = :content_group AND
					`languages_id` = :languages_id
				';
			$t_cm_query = strtr($t_cm_query, array(
					':content_text' => xtc_db_input($t_latest_document['content']),
					':content_file' => xtc_db_input('protected_shops_'.strtolower($p_document_name).'.php'),
					':gm_last_modified' => xtc_db_input($t_latest_document['document_date']),
					':content_group' => (int)$t_content_group_id,
					':languages_id' => (int)$t_languages_id,
				));
			xtc_db_query($t_cm_query);
		}


		$t_pdf_type = 'Text';
		if($p_document_name == 'AGB' && $this->_config['use_for_pdf_conditions'] == true)
		{
			$t_conditions_document = $this->getLatestDocument($p_document_name, $t_pdf_type);

			$t_pdftext_query =
				'UPDATE `gm_contents`
				SET
					`gm_value` = \':new_text\'
				WHERE
					`gm_key` = \'GM_PDF_CONDITIONS\' AND
					`languages_id` = :languages_id';

			$t_pdftext_query = strtr($t_pdftext_query, array(
					':new_text' => xtc_db_input($t_conditions_document['content']),
					':languages_id' => (int)$t_languages_id,
				));

			xtc_db_query($t_pdftext_query);
		}

		if(($p_document_name == 'Widerruf' || $p_document_name == 'Rueckgabe') && $this->_config['use_for_pdf_withdrawal'] != '0')
		{
			$t_withdrawal_document = $this->getLatestDocument(ucfirst($this->_config['use_for_pdf_withdrawal']), $t_pdf_type);

			$t_pdftext_query =
				'UPDATE `gm_contents`
				SET
					`gm_value` = \':new_text\'
				WHERE
					`gm_key` = \'GM_PDF_WITHDRAWAL\' AND
					`languages_id` = :languages_id';

			$t_pdftext_query = strtr($t_pdftext_query, array(
					':new_text' => xtc_db_input($t_withdrawal_document['content']),
					':languages_id' => (int)$t_languages_id,
				));

			xtc_db_query($t_pdftext_query);
		}

		return true;
	}

	public function updateAndUseAll()
	{
		$t_output = '';
		$t_docinfo = $this->getDocumentInfo();
		foreach($t_docinfo as $t_docname => $t_docdate)
		{
			$t_output .= 'Updating '.$t_docname.', latest is '.$t_docdate.PHP_EOL;
			try
			{
				$this->updateDocument($t_docname);
				$this->useDocument($t_docname);
			}
			catch(Exception $e)
			{
				$t_output .= 'ERROR: '. $e->getMessage() . PHP_EOL;
			}
		}
		return $t_output;
	}
}
