<?php
/* --------------------------------------------------------------
  ItRechtModuleCenterModuleController.inc.php 2016-05-30
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class ItRechtModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class ItRechtModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected $text;

	protected function _init()
	{
		$this->pageTitle   = $this->languageTextManager->get_text('it_recht_title');
		$this->text = MainFactory::create('LanguageTextManager', 'itrecht', $_SESSION['languages_id']);
	}

	protected function _getLanguages()
	{
		$supported_languages = array('de');
		$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$db->from('languages')
		   ->select('languages_id, code')
		   ->where('code IN (' . implode(',', array_map(function ($lang) { return "'$lang'"; }, $supported_languages)) . ')');
		$languages = [];
		foreach($db->get()->result() as $lang_row)
		{
			$languages[$lang_row->code] = $lang_row->languages_id;
		}
		return $languages;
	}

	protected function _cmConfigured($languages_id, $type)
	{
		$mapping = array(
			'agb'         => 3,
			'impressum'   => 4,
			'datenschutz' => 2,
			'widerruf'    => 3889896
		);
		$isConfigured = false;
		if(array_key_exists($type, $mapping))
		{
			$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
			$db->from('content_manager')
			   ->select('content_id')
			   ->where('content_group', $mapping[$type])
			   ->where('languages_id', $languages_id)
			   ->where('content_file', sprintf('itrk_%s.php', $type));
			$num_rows = $db->get()->num_rows();
			$isConfigured = $num_rows > 0;
		}
		return $isConfigured;
	}

	protected function _getFiles()
	{
		$languages = $this->_getLanguages();
		$rechtstext_types = array('agb', 'impressum', 'datenschutz', 'widerruf');
		$files = [];
		foreach($rechtstext_types as $rtype)
		{
			$files[$rtype] = [];
			foreach($languages as $lang_code => $lang_id)
			{
				$files[$rtype][$lang_code] = [
					'isConfigured' => $this->_cmConfigured($lang_id, $rtype),
					'files' => [],
				];
				foreach(['txt', 'html', 'pdf'] as $content_type)
				{
					$file_path = sprintf('media/content/itrk_%s_%s.%s', $rtype, $lang_code, $content_type);
					$full_file_path = DIR_FS_CATALOG . '/' . $file_path;
					$file_exists = file_exists($full_file_path);
					$file_date = $file_exists ? date('c', filemtime($full_file_path)) : 'not received';
					$file_url = HTTP_SERVER . DIR_WS_CATALOG . $file_path;
					$files[$rtype][$lang_code]['files'][$content_type] = [
						'file'               => $file_path,
						'file_exists'        => $file_exists,
						'file_date'          => $file_date,
						'file_url'           => $file_url,
					];
				}
			}
		}
		return $files;
	}

	public function actionDefault()
	{
		$apiToken = gm_get_conf('ITRECHT_TOKEN');
		if(!empty($apiToken))
		{
			$response = MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/Configuration'));
		}
		else
		{
			$response = MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/Info'));
		}

		return $response;
	}

	public function actionConfiguration()
	{
		$GLOBALS['messageStack']->add($this->text->get_text('intro_note'), 'info');
		$formdata = [
			'page_token'            => isset($_SESSION['coo_page_token']) ? $_SESSION['coo_page_token']->generate_token() : '',
			'save_action'           => xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/SaveConfiguration'),
			'use_in_cm_action'      => xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/UseInContentManager'),
			'url_info_page'         => xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/Info'),
			'token'                 => gm_get_conf('ITRECHT_TOKEN'),
			'use_agb_in_pdf'        => gm_get_conf('ITRECHT_USE_AGB_IN_PDF'),
			'use_withdrawal_in_pdf' => gm_get_conf('ITRECHT_USE_WITHDRAWAL_IN_PDF'),
			'api_url'               => HTTP_SERVER.DIR_WS_CATALOG,
			'files'                 => $this->_getFiles(),
		];
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$html = $this->_render('itrk_configuration.html', $formdata);
		$html = $this->_replaceLanguagePlaceholders($html);
		$response = MainFactory::create('AdminPageHttpControllerResponse', $this->pageTitle, $html);
		return $response;
	}

	public function actionInfo()
	{
		$formdata = [
			'url_config_page'         => xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/Configuration'),
		];
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$html = $this->_render('itrk_info.html', $formdata);
		return MainFactory::create('AdminPageHttpControllerResponse', $this->pageTitle, $html);
	}

	public function actionSaveConfiguration()
	{
		$this->_validatePageToken();
		$postData = $this->_getPostDataCollection();
		if($postData->keyExists('gen_token'))
		{
			$token = md5(uniqid().uniqid());
			gm_set_conf('ITRECHT_TOKEN', $token);
			$GLOBALS['messageStack']->add_session($this->text->get_text('token_generated'), 'info');
		}
		else
		{
			$useAgbInPdf      = $postData->keyExists('use_agb_in_pdf')        && $postData->getValue('use_agb_in_pdf') == true;
			$useWiderrufInPdf = $postData->keyExists('use_withdrawal_in_pdf') && $postData->getValue('use_withdrawal_in_pdf') == true;

			gm_set_conf('ITRECHT_TOKEN',                 xtc_db_input(trim($postData->getValue('token'))));
			gm_set_conf('ITRECHT_USE_AGB_IN_PDF',        ($useAgbInPdf == true      ? '1' : '0'));
			gm_set_conf('ITRECHT_USE_WITHDRAWAL_IN_PDF', ($useWiderrufInPdf == true ? '1' : '0'));

			$languages = $this->_getLanguages();
			$files     = $this->_getFiles();
			foreach($languages as $code => $l_id)
			{
				if($useAgbInPdf && $files['agb'][$code]['files']['txt']['file_exists'] == true)
				{
					$conditions_txt = file_get_contents(DIR_FS_CATALOG.$files['agb'][$code]['files']['txt']['file']);
					gm_set_content('GM_PDF_CONDITIONS', $conditions_txt, 2);
				}
				if($useWiderrufInPdf && $files['widerruf'][$code]['files']['txt']['file_exists'] == true)
				{
					$withdrawal_txt = file_get_contents(DIR_FS_CATALOG.$files['widerruf'][$code]['files']['txt']['file']);
					gm_set_content('GM_PDF_WITHDRAWAL', $withdrawal_txt, 2);
				}
			}
			$GLOBALS['messageStack']->add_session($this->text->get_text('configuration_saved'), 'info');
		}
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/Configuration'));
	}

	public function actionUseInContentManager()
	{
		$this->_validatePageToken();
		$postData = $this->_getPostDataCollection();
		$languages = $this->_getLanguages();
		$lang_id = $languages[$postData->getValue('lang')];
		$mapping = array(
			'agb'         => 3,
			'impressum'   => 4,
			'datenschutz' => 2,
			'widerruf'    => 3889896
		);
		$content_group = array_key_exists($postData->getValue('type'), $mapping) ? $mapping[$postData->getValue('type')] : false;
		if($content_group !== false)
		{
			$db = StaticGXCoreLoader::getDatabaseQueryBuilder();
			$db->set('content_file', $postData->getValue('file'));
			$db->where('languages_id', $lang_id);
			$db->where('content_group', $content_group);
			$db->update('content_manager');
			$GLOBALS['messageStack']->add_session($this->text->get_text('legal_text_copied_to_content_manager'), 'info');
		}
		else
		{
			$GLOBALS['messageStack']->add_session($this->text->get_text('not_copied_to_cm_type_incompatible'), 'info');
		}
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ItRechtModuleCenterModule/Configuration'));
	}

	protected function _replaceLanguagePlaceholders($content)
	{
		while(preg_match('/##(\w+)\b/', $content, $matches) == 1) {
			$replacement = $this->text->get_text($matches[1]);
			if(empty($replacement)) {
				$replacement = $matches[1];
			}
			$content = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
		}
		return $content;
	}
}
