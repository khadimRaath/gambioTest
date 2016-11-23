<?php
/* --------------------------------------------------------------
  ProtectedShopsModuleCenterModuleController.inc.php 2015-09-14
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

/**
 * Class ProtectedShopsModuleCenterModuleController
 * @extends    AbstractModuleCenterModuleController
 * @category   System
 * @package    Modules
 * @subpackage Controllers
 */
class ProtectedShopsModuleCenterModuleController extends AbstractModuleCenterModuleController
{
	protected $text;

	protected function _init()
	{
		$this->pageTitle   = $this->languageTextManager->get_text('protected_shops_title');
		//$this->redirectUrl = xtc_href_link('protectedshops.php');
		$this->text = MainFactory::create('LanguageTextManager', 'protectedshops', $_SESSION['language_id']);
	}

	public function actionDefault()
	{
		$protectedShops = MainFactory::create('ProtectedShops');
		if($protectedShops->isConfigured())
		{
			$target = 'do=ProtectedShopsModuleCenterModule/Configuration';
		}
		else
		{
			$target = 'do=ProtectedShopsModuleCenterModule/Info';
		}
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', $target));
	}

	public function actionInfo()
	{
		$formdata = [
			'url_config' => xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Configuration'),
		];
		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$html = $this->_render('protectedshops_info.html', $formdata);
		$html = $this->replaceLanguagePlaceholders($html);
		return MainFactory::create('AdminPageHttpControllerResponse', $this->text->get_text('configuration_heading'), $html);
	}

	public function actionConfiguration()
	{
		$protectedShops = MainFactory::create('ProtectedShops');
		$formdata = [
			'page_token'         => isset($_SESSION['coo_page_token']) ? $_SESSION['coo_page_token']->generate_token() : '',
			'save_config_action' => xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/StoreConfiguration'),
			'action_update'      => xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Update'),
			'action_use'         => xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Use'),
			'action_use_all'     => xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/UseAll'),
			'url_info'           => xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Info'),
			'config'             => $protectedShops->getConfig(),
			'valid_formats'      => $protectedShops->valid_formats,
		];
		if($protectedShops->isConfigured())
		{
			$formdata['docinfo'] = array();
			$formdata['localdocs'] = array();

			try
			{
				$formdata['docinfo'] = $protectedShops->getDocumentInfo();
				foreach($formdata['docinfo'] as $t_docname => $t_docdate)
				{
					$formdata['localdocs'][$t_docname] = array();
					foreach($protectedShops->valid_formats as $t_format)
					{
						$formdata['localdocs'][$t_docname][$t_format] = $protectedShops->getLatestDocument($t_docname, $t_format);
					}
				}
			}
			catch(Exception $e)
			{
				$GLOBALS['messageStack']->add($protectedShops->get_text('protected_shops_unreachable'), 'info');
			}

			$formdata['contentpages'] = $this->getContentPages();
		}

		$this->contentView->set_template_dir(DIR_FS_ADMIN . 'html/content/module_center/');
		$html = $this->_render('protectedshops_config.html', $formdata);
		$html = $this->replaceLanguagePlaceholders($html);

		return MainFactory::create('AdminPageHttpControllerResponse', $this->text->get_text('configuration_heading'), $html);
	}

	public function actionStoreConfiguration()
	{
		$this->_validatePageToken();
		$protectedShops = MainFactory::create('ProtectedShops');
		$postData = $this->_getPostData('config');
		$protectedShops->setConfig($postData);
		$GLOBALS['messageStack']->add_session($protectedShops->get_text('configuration_saved'), 'info');
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Configuration'));
	}

	public function actionUpdate()
	{
		$this->_validatePageToken();
		$protectedShops = MainFactory::create('ProtectedShops');
		try
		{
			$protectedShops->updateDocument($this->_getPostData('document_name'), null, true);
			$GLOBALS['messageStack']->add_session($protectedShops->get_text('document_updated'), 'info');
		}
		catch(Exception $e)
		{
			$GLOBALS['messageStack']->add_session($protectedShops->get_text('document_update_failed').': '.$e->getMessage(), 'info');
		}
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Configuration'));
	}

	public function actionUse()
	{
		$this->_validatePageToken();
		$protectedShops = MainFactory::create('ProtectedShops');
		$protectedShops->useDocument($this->_getPostData('document_name'));
		$GLOBALS['messageStack']->add_session($protectedShops->get_text('using_document_as_per_configuration'), 'info');
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Configuration'));
	}

	public function actionUseAll()
	{
		$this->_validatePageToken();
		$protectedShops = MainFactory::create('ProtectedShops');
	    try
	    {
	      $protectedShops->updateAndUseAll();
	      $GLOBALS['messageStack']->add_session($protectedShops->get_text('all_documents_updated_and_used'), 'info');
	    }
	    catch(Exception $e)
	    {
	      $GLOBALS['messageStack']->add_session($protectedShops->get_text('an_error_occurred_during_update_of_documents'), 'info');
	    }
		return MainFactory::create('RedirectHttpControllerResponse', xtc_href_link('admin.php', 'do=ProtectedShopsModuleCenterModule/Configuration'));
	}

	protected function getContentPages()
	{
		$t_language_id = 2;
		$t_query = 'SELECT `content_title`, `content_group` FROM `content_manager` WHERE `languages_id` = '.(int)$t_language_id;
		$t_content_pages = array();
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_content_pages[$t_row['content_group']] = $t_row;
		}
		return $t_content_pages;
	}

	protected function replaceLanguagePlaceholders($content)
	{
		while(preg_match('/##(\w+)\b/', $content, $matches) == 1)
		{
			$replacement = $this->text->get_text($matches[1]);
			if(empty($replacement))
			{
				$replacement = $matches[1];
			}
			$replacement = str_replace('»', '&shy;', $replacement);
			$content     = preg_replace('/##'.$matches[1].'/', $replacement.'$1', $content, 1);
		}
		return $content;
	}
}