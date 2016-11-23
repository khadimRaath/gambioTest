<?php
/* --------------------------------------------------------------
   ContentBoxContentView.inc.php 2014-10-31 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(information.php,v 1.6 2003/02/10); www.oscommerce.com
   (c) 2003	 nextcommerce (content.php,v 1.2 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: content.php 1302 2005-10-12 16:21:29Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/**
 * Class ContentBoxContentView
 */
class ContentBoxContentView extends ContentView
{
	protected $fileFlagName			= '';
	protected $requestUri			= '';
	protected $contentString		= '';
	protected $customerStatusId		= null;
	protected $languagesId			= 0;
	protected $contentLinksArray	= array();
	
// ########## CONSTRUCTOR ########## 
	
	function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_content.html');
	}
	
	
// ########## GETTER & SETTER ##########

	/**
	 * @param	string	$p_name		File Flag Name
	 */
	public function setFileFlagName($p_name)
	{
		if(check_data_type($p_name, 'string'))
		{
			$this->fileFlagName = $p_name;
		}
	}

	/**
	 * @return	string				File Flag Name
	 */
	public function getFileFlagName()
	{
		return $this->fileFlagName;
	}


	/**
	 * @param	string	$p_uri		Request URI
	 */
	public function setRequestUri($p_uri)
	{
		if(check_data_type($p_uri, 'string'))
		{
			$this->requestUri = $p_uri;
		}
	}

	/**
	 * @return	string				Request URI
	 */
	public function getRequestUri()
	{
		return $this->requestUri;
	}

	/**
	 * @param	int		$p_id		Customer Status ID
	 */
	public function setCustomerStatusId($p_id)
	{
		if(check_data_type($p_id, 'int'))
		{
			$this->customerStatusId = $p_id;
		}
	}

	/**
	 * @return	int					Customer Status ID
	 */
	public function getCustomerStatusId()
	{
		return $this->customerStatusId;
	}

	/**
	 * @param 	int		$p_langId	Language ID
	 */
	public function setLanguagesId($p_langId)
	{
		if(check_data_type($p_langId, 'int'))
		{
			$this->languagesId = $p_langId;
		}
	}

	/**
	 * @return int					Language ID
	 */
	public function getLanguagesId()
	{
		return $this->languagesId;
	}
	
// ########## PUBLIC METHODS ##########

	/**
	 * 
	 */
	public function prepare_data()
	{
		$seoBoost					= MainFactory::create_object('GMSEOBoost');
		$dbData						= xtc_db_query($this->_getSqlString());
		$this->contentLinksArray 	= array();
		$this->contentString		= '';

		while($contentArray = xtc_db_fetch_array($dbData))
		{
			if(empty($contentArray['gm_link']))
			{
				$contentUrl = $this->_getContentURL($seoBoost, $contentArray['content_id'], $contentArray['content_group'], $contentArray['content_title']);
				$this->_generateContent($contentUrl, $contentArray['content_title']);
			}
			else
			{
				$this->_generateContent($contentArray['gm_link'], $contentArray['content_title'], $contentArray['gm_link_target']);
			}
		}


		if($this->contentString !== '')
		{
			$this->_assignContentData();
		}
		else
		{
			$this->build_html = false;
		}

	}
	
// ########## PROTECTED / PRIVATE METHODS ##########


	/**
	 * @return string		SQL string
	 */
	protected function _getSqlString()
	{
		if(GROUP_CHECK == 'true')
		{
			$groupCheck = " AND group_ids LIKE '%c_" . (int)$this->customerStatusId . "_group%' ";
		}

		$sql 	= "SELECT
						content_id,
						categories_id,
						parent_id,
						content_title,
						content_group,
						gm_link,
						gm_link_target
					FROM
						content_manager AS cm 
						LEFT JOIN cm_file_flags AS ff USING (file_flag)
					WHERE
						ff.file_flag_name = '" . xtc_db_input($this->fileFlagName) . "' AND
						languages_id = '" . (int)$this->languagesId . "' AND
						content_status = 1
						" . $groupCheck . "
					ORDER BY
						sort_order";

		return $sql;
	}

	/**
	 * 
	 */
	protected function _assignContentData()
	{
		$this->set_content_data('CONTENT_LINKS_DATA', $this->contentLinksArray);
		$this->_assignDeprecated();
	}


	/**
	 * @param GMSEOBoost $seoBoost
	 * @param int        $p_contentId
	 * @param int        $p_contentGroupId
	 * @param string     $p_contentTitle
	 *
	 * @return string
	 */
	protected function _getContentURL(GMSEOBoost $seoBoost, $p_contentId, $p_contentGroupId, $p_contentTitle)
	{
		if($seoBoost->boost_content)
		{
			return xtc_href_link($seoBoost->get_boosted_content_url($p_contentId, $this->languagesId));
		}

		$searchEngineFriendly = (SEARCH_ENGINE_FRIENDLY_URLS == 'true') ? '&content=' . xtc_cleanName($p_contentTitle) : '';
		
		return xtc_href_link(FILENAME_CONTENT, 'coID=' . $p_contentGroupId . $searchEngineFriendly);
	}


	/**
	 * @param string $p_href
	 * @param string $p_title
	 * @param string $p_target
	 */
	protected function _generateContent($p_href, $p_title, $p_target = '')
	{
		$selected 	= (int)( strstr($p_href, $this->requestUri) !== false && $this->requestUri != DIR_WS_CATALOG );
		$title		= htmlspecialchars_wrapper($p_title);
		$target		= (!empty($p_target)) ? ('target="' . $p_target . '"') : '';

		$this->contentLinksArray[]	= array(
			'NAME' => $title,
			'SELECTED' => $selected,
			'URL' => $p_href,
			'URL_TARGET' => $p_target
		);
		
		// DEPRECATED
		$this->contentString 		.= '<img src="templates/' . CURRENT_TEMPLATE . '/img/icon_arrow.gif" alt="" />';
		$this->contentString 		.= '<a href="' . $p_href . '" '. $target . '>' . $title . '</a><br />';
	}


	protected function _assignDeprecated()
	{
		$this->set_content_data('CONTENT', $this->contentString, 2);
	}

}