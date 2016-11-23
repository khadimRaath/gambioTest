<?php
/* --------------------------------------------------------------
   ProductMediaContentView.inc.php 2016-09-20
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2003	 nextcommerce (products_media.php,v 1.8 2003/08/25); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: products_media.php 1259 2005-09-29 16:11:19Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

require_once(DIR_FS_INC . 'xtc_filesize.inc.php');
require_once(DIR_FS_INC . 'xtc_in_array.inc.php');

/**
 * Class ProductMediaContentView
 */
class ProductMediaContentView extends ContentView
{
	
	protected $languageId		= null;
	protected $productId		= null;
	protected $customerStatusId	= null;
	protected $moduleDataArray	= array();
	
// ########## CONSTRUCTOR ##########
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/products_media.html');
		$this->set_flat_assigns(true);
	}
	
	
// ########## GETTER & SETTER ##########

	/**
	 * @param integer $p_langId		Language ID
	 */
	public function setLanguageId($p_langId)
	{
		$this->languageId = (int)$p_langId;
	}
	
	/**
	 * @return integer				Language ID
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}
	
	/**
	 * @param integer $p_productId	Product ID
	 */
	public function setProductId($p_productId)
	{
		$this->productId = (int)$p_productId;
	}
	
	/**
	 * @return integer				Product ID
	 */
	public function getProductId()
	{
		return $this->productId;
	}

	/**
	 * @param integer $p_customerStatusId 	Customer Status ID
	 */
	public function setCustomerStatusId($p_customerStatusId)
	{
		$this->customerStatusId = (int)$p_customerStatusId;
	}

	/**
	 * @return integer						Customer Status ID
	 */
	public function getCustomerStatusId()
	{
		return $this->customerStatusId;
	}

// ########## PUBLIC METHODS ##########

	/**
	 * 
	 */
	public function prepare_data()
	{
		$checkDataArray	= $this->_getAllProductIds();

		if(xtc_in_array($this->productId, $checkDataArray))
		{
			$contentResult = xtc_db_query($this->_getContentSql());
			$this->_prepareAssignData($contentResult);
			$this->_assignData();
		}

	}
	
	
// ########## PROTECTED / PRIVATE METHODS ##########

	/**
	 * @param $contentResult	xtc_db_query object
	 */
	protected function _prepareAssignData($contentResult)
	{
		$this->moduleDataArray	= array ();

		while($contentDataArray = xtc_db_fetch_array($contentResult))
		{

			$iconsArray		= $this->_generateIconUrls($contentDataArray);
			$buttonsArray	= $this->_generateLink($contentDataArray);
			$filename		= strtolower($contentDataArray['content_name']);

			$this->moduleDataArray[] = array(	'ICON' => $iconsArray['icon'],
												 'ICON_URL' => $iconsArray['iconUrl'],
												 'FILENAME' => $filename,
												 'DESCRIPTION' => $contentDataArray['file_comment'],
												 'FILESIZE' => xtc_filesize($contentDataArray['content_file']),
												 'BUTTON' => $buttonsArray['button'],
												 'BUTTON_URL' => $buttonsArray['buttonLink'],
												 'BUTTON_TYPE' => $buttonsArray['buttonType'],
												 'CONTENT_NAME' => $contentDataArray['content_name'],
												 'HITS' => $contentDataArray['content_read']);
		}
	}
	
	

	/**
	 * @return string	SQL Statement
	 */
	protected function _getProductIdsSql()
	{
		$sql = "SELECT DISTINCT
					products_id
				FROM " . TABLE_PRODUCTS_CONTENT . "
				WHERE languages_id = '" . (int)$this->languageId . "'";
		
		return $sql;
	}


	/**
	 * @return string	SQL Statement
	 */
	protected function _getContentSql()
	{
		// get content data
		if(GROUP_CHECK == 'true')
		{
			$groupCheck = "group_ids LIKE '%c_" . (int)$this->customerStatusId . "_group%' AND";
		}

		//get download
		$sql = "SELECT
					content_id,
					content_name,
					content_link,
					content_file,
					content_read,
					file_comment
				FROM " . TABLE_PRODUCTS_CONTENT . "
				WHERE
					products_id = '" . (int)$this->productId . "' AND
					" . $groupCheck . "
					languages_id = '" . (int)$this->languageId . "'";

		return $sql;
	}


	/**
	 * @return array	Array of product IDs
	 */
	protected function _getAllProductIds()
	{
		$checkResult	= xtc_db_query($this->_getProductIdsSql());
		$checkDataArray	= array();

		while($contentDataArray = xtc_db_fetch_array($checkResult))
		{
			$checkDataArray[] = $contentDataArray['products_id'];
		}
		
		return $checkDataArray;
	}


	/**
	 * @param	array	$contentArray	Content element
	 *
	 * @return 	array				Icon Array
	 */
	protected function _generateIconUrls(array $contentArray)
	{
		
		$resultArray = array();
		
		if($contentArray['content_link'] != '')
		{
			$resultArray['icon'] = xtc_image(DIR_WS_CATALOG . 'admin/html/assets/images/legacy/icons/icon_link.gif');
			$resultArray['iconUrl'] = DIR_WS_CATALOG . 'admin/html/assets/images/legacy/icons/icon_link.gif';
		}
		else
		{
			$file = end(explode('.', $contentArray['content_file']));
			$resultArray['icon'] = xtc_image(DIR_WS_CATALOG . 'admin/html/assets/images/legacy/icons/icon_' . $file . '.gif');
			$resultArray['iconUrl'] = DIR_WS_CATALOG . 'admin/html/assets/images/legacy/icons/icon_' . $file . '.gif';
		}
		
		return $resultArray;
	}


	/**
	 * @return array		Valid file types
	 */
	protected function _getValidFileTypes()
	{
		$validArray = array('txt', 'bmp', 'jpg', 'gif', 'png', 'tif', 'jpeg', 'pjpeg');
		return $validArray;
	}


	/**
	 * @param	array	$contentArray		Content element
	 *
	 * @return 	array						Buttons array
	 */
	protected function _generateLink(array $contentArray)
	{
		$filename		= strtolower($contentArray['content_name']);
		$extensionArray	= explode('.', $filename);
		$valid			= $this->_getValidFileTypes();
		$resultArray	= array();
		
		if ($contentArray['content_link'] === '')
		{
			$inArray = in_array(end($extensionArray), $valid, true);
			if($inArray)
			{
				$resultArray['buttonType'] = 'popup';
				$resultArray['buttonLink'] = xtc_href_link(FILENAME_MEDIA_CONTENT, 'coID='.$contentArray['content_id']);
			}
			else
			{
				$resultArray['buttonType'] = 'download';
				$resultArray['buttonLink'] = xtc_href_link('media/products/' . $contentArray['content_file']);
			}
		}
		else
		{
			$resultArray['button'] = '';
			$resultArray['buttonType'] = '';
			$resultArray['buttonLink'] = $contentArray['content_link'];
		}
		
		return $resultArray;
	}


	/**
	 * 
	 */
	protected function _assignData()
	{
		$this->set_content_data('module_content', $this->moduleDataArray);
	}
}