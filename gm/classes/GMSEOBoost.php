<?php
/* --------------------------------------------------------------
   GMSEOBoost.php 2016-08-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(boxes.php,v 1.32 2003/05/27); www.oscommerce.com
   (c) 2003	 nextcommerce (boxes.php,v 1.11 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: boxes.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class GMSEOBoost_ORIGIN implements UrlKeywordsRepairerInterface
{
	public $boost_products		= false;
	public $boost_categories	= false;
	public $boost_content		= false;
	public $v_binary_string	= '';

	/**
	 * @var LanguageProviderInterface
	 */
	protected $languageProvider;

	/**
	 * @var DataCache
	 */
	protected $dataCache;

	protected $seoBoostCache = array();
	
	/**
	 * @var CategoryReadServiceInterface $urlRewritesReader
	 */
	protected $categoryUrlRewritesReader;
	
	/**
	 * @var ProductReadServiceInterface $urlRewritesReader
	 */
	protected $productUrlRewritesReader;
	
	/**
	 * @var UrlRewriteStorage $urlRewritesReader
	 */
	protected $contentUrlRewritesReader;
	
	
	public function &get_instance()
	{
		static $s_instance;

		if($s_instance === NULL)   {
			$s_instance = MainFactory::create_object('GMSEOBoost');
		}
		return $s_instance;
	}

	public function __construct()
	{
		$this->set_binary_string();

		if(gm_get_conf('GM_SEO_BOOST_PRODUCTS') 	== 'true') $this->boost_products 		= true;
		if(gm_get_conf('GM_SEO_BOOST_CATEGORIES')	== 'true') $this->boost_categories 	= true;
		if(gm_get_conf('GM_SEO_BOOST_CONTENT') 		== 'true') $this->boost_content 		= true;
		
		$this->languageProvider = MainFactory::create('LanguageProvider',
		                                              StaticGXCoreLoader::getDatabaseQueryBuilder());
		
		$this->dataCache                 = DataCache::get_instance();
		$this->productUrlRewritesReader  = $this->_getUrlRewriteReader('product');
		$this->categoryUrlRewritesReader = $this->_getUrlRewriteReader('category');
		$this->contentUrlRewritesReader  = $this->_getUrlRewriteReader('content');

		if($this->dataCache->key_exists('seo_boost_cache', true))
		{
			$seoBoostCache = $this->dataCache->get_persistent_data('seo_boost_cache');
			
			if(is_array($seoBoostCache))
			{
				$this->seoBoostCache = $seoBoostCache;
			}
		}
	}


	public function boost_active() //DEPRECATED!!
	{
		return false;
	}


	public function is_collation_supported()
	{
		$t_collation_supported = true;

		$t_sql = "SELECT VERSION() AS version";
		$t_result = @mysqli_query($GLOBALS["___mysqli_ston"], $t_sql);
		$t_result_array = @mysqli_fetch_array($t_result);

		if(isset($t_result_array['version']))
		{
			if(version_compare($t_result_array['version'], '4.1') == -1)
			{
				$t_collation_supported = false;
			}
		}

		return $t_collation_supported;
	}


	public function set_binary_string()
	{
		if($this->is_collation_supported() === false)
		{
			$this->v_binary_string = 'BINARY ';
		}
		else
		{
			$this->v_binary_string = '';
		}
	}


	public function get_current_boost_url()
	{
		$t_output_url = '';
		$t_language_id = false;

		if(xtc_not_null($_GET['gm_boosted_product']))
		{
			$t_boosted_name = xtc_db_prepare_input($_GET['gm_boosted_product']);
			$t_products_id = (int)$this->get_products_id_by_boost($t_boosted_name);
			if($t_products_id != 0) $t_output_url = $this->get_boosted_product_url($t_products_id, $t_boosted_name, $t_language_id, $_GET['gm_boosted_product']);
		}

		if(xtc_not_null($_GET['gm_boosted_category']))
		{
			$t_boosted_name = xtc_db_prepare_input($_GET['gm_boosted_category']);
			$t_categories_id = (int)$this->get_categories_id_by_boost($t_boosted_name);
			if($t_categories_id != 0) $t_output_url = $this->get_boosted_category_url($t_categories_id, $t_language_id, $_GET['gm_boosted_category']);
		}

		return $t_output_url;
	}


	public function get_content_id_by_content_group($p_content_group, $p_languages_id = false)
	{
		$t_content_id = 0;
		
		$c_content_group = (int)$p_content_group;
		$c_languages_id = (int)$p_languages_id;
		if($p_languages_id === false)
		{
			$c_languages_id = (int)$_SESSION['languages_id'];
		}		

		$t_result = xtc_db_query("SELECT content_id
									FROM " . TABLE_CONTENT_MANAGER . "
									WHERE
										content_group = '" . $c_content_group . "' AND
										languages_id = '" . $c_languages_id . "'
									LIMIT 1");
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_content_id = (int)$t_result_array['content_id'];
		}
		
		return $t_content_id;
	}


	public function get_content_group_by_content_id($p_content_id)
	{
		$t_content_group = 0;
		$c_content_id = (int)$p_content_id;	

		$t_result = xtc_db_query("SELECT content_group
									FROM " . TABLE_CONTENT_MANAGER . "
									WHERE
										content_id = '" . $c_content_id . "'
									LIMIT 1");
		if(xtc_db_num_rows($t_result) == 1)
		{
			$t_result_array = xtc_db_fetch_array($t_result);
			$t_content_group = (int)$t_result_array['content_group'];
		}
		
		return $t_content_group;
	}

	public function get_content_coID_by_boost($boosted_name, $language_id=false)
	{
		$coID = 0;
		
		$languageId = ($language_id !== false) ? (int)$language_id : (int)$_SESSION['languages_id'];
		if(is_null($languageId))
		{
			$languageId = $this->languageProvider->getDefaultLanguageId();
		}
		
		$cacheKey = 'co-id-' . $languageId . '-' . $boosted_name;

		if(array_key_exists($cacheKey, $this->seoBoostCache))
		{
			return $this->seoBoostCache[$cacheKey];
		}
		
		$urlRewrites = $this->contentUrlRewritesReader->findByRewriteUrl(new NonEmptyStringType($boosted_name));
		
		if($urlRewrites->count())
		{
			/** @var UrlRewrite $urlRewrite */
			$urlRewrite = array_shift($urlRewrites->getArray());
			$coID       = $urlRewrite->getContentId();
		}
		else
		{
			if($language_id === false)
			{
				$boosted_name = basename($boosted_name);
				
				$language_id = (int)$_SESSION['languages_id'];
				
				$result = xtc_db_query('SELECT
										content_group,
										languages_id
									FROM content_manager
									WHERE
										gm_url_keywords = ' . $this->v_binary_string . '"'. mysqli_real_escape_string($GLOBALS['db_link'], $boosted_name)	.'"');
				while($result_array = xtc_db_fetch_array($result))
				{
					$coID = $result_array['content_group'];
					
					if($result_array['languages_id'] == $language_id)
					{
						break;
					}
				}
			}
			else
			{
				$result = xtc_db_query('SELECT content_group
									FROM content_manager
									WHERE
										gm_url_keywords = ' . $this->v_binary_string . '"'
				                       . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"]))
					                       ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $boosted_name)
					                       : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.",
					                                         E_USER_ERROR))
						                       ? ""
						                       : "")) . '" AND
										languages_id = "' . (int)$language_id . '"');
				if(mysqli_num_rows($result) > 0)
				{
					$coID = $this->_mysqlResult($result, 0, 'content_group');
				}
			}
		}

		$this->_writeCache($cacheKey, $coID);

		return $coID;
	}


	public function get_products_id_by_boost($boosted_name, $language_id=false)
	{
		$products_id = 0;
		
		$languageId = ($language_id !== false) ? (int)$language_id : (int)$_SESSION['languages_id'];
		if(is_null($languageId))
		{
			$languageId = $this->languageProvider->getDefaultLanguageId();
		}
		
		$cacheKey = 'p-id-' . $languageId . '-' . $boosted_name;

		if(array_key_exists($cacheKey, $this->seoBoostCache))
		{
			return $this->seoBoostCache[$cacheKey];
		}
		
		$urlRewrites = $this->productUrlRewritesReader->findUrlRewritesByRewriteUrl(new NonEmptyStringType($boosted_name));
		
		if($urlRewrites->count())
		{
			/** @var UrlRewrite $urlRewrite */
			$urlRewrite  = array_shift($urlRewrites->getArray());
			$products_id = $urlRewrite->getContentId();
		}
		else
		{
			$boosted_name = basename($boosted_name);
			
			if($language_id === false)
			{
				$language_id = (int)$_SESSION['languages_id'];
				
				$result = xtc_db_query('SELECT
										products_id,
										language_id
									FROM products_description
									WHERE
										gm_url_keywords = ' . $this->v_binary_string . '"'. ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $boosted_name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))	.'"');
				while($result_array = xtc_db_fetch_array($result))
				{
					$products_id = $result_array['products_id'];
					
					if($result_array['language_id'] == $language_id)
					{
						break;
					}
				}
			}
			else
			{
				$result = xtc_db_query('SELECT products_id
									FROM products_description
									WHERE
										gm_url_keywords = ' . $this->v_binary_string . '"'. ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $boosted_name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""))	.'" AND
										language_id = "' . (int)$language_id . '"');
				if(mysqli_num_rows($result) > 0)
				{
					$products_id = $this->_mysqlResult($result, 0, 'products_id');
				}
			}
		}

		$this->_writeCache($cacheKey, $products_id);

		return $products_id;
	}


	public function get_categories_id_by_boost($boosted_name, $language_id=false)
	{
		if(strlen($boosted_name) && substr($boosted_name, -1) === '/')
		{
			$boosted_name = substr($boosted_name, 0, -1);
		}
		
		$categories_id = 0;
		
		$languageId = ($language_id !== false) ? (int)$language_id : (int)$_SESSION['languages_id'];
		if(is_null($languageId))
		{
			$languageId = $this->languageProvider->getDefaultLanguageId();
		}
		
		$cacheKey = 'c-id-' . $languageId . '-' . $boosted_name;

		if(array_key_exists($cacheKey, $this->seoBoostCache))
		{
			return $this->seoBoostCache[$cacheKey];
		}
		
		$urlRewrites = $this->categoryUrlRewritesReader->findUrlRewritesByRewriteUrl(new NonEmptyStringType($boosted_name));
		
		if($urlRewrites->count())
		{
			/** @var UrlRewrite $urlRewrite */
			$urlRewrite    = array_shift($urlRewrites->getArray());
			$categories_id = $urlRewrite->getContentId();
		}
		else
		{
			$boosted_name = basename($boosted_name);
			
			if($language_id === false)
			{
				$language_id = (int)$_SESSION['languages_id'];
				
				$result = xtc_db_query('SELECT
										categories_id,
										language_id
									FROM categories_description
									WHERE
										gm_url_keywords = ' . $this->v_binary_string . '"'. ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $boosted_name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) .'"');
				while($result_array = xtc_db_fetch_array($result))
				{
					$categories_id = $result_array['categories_id'];
					
					if($result_array['language_id'] == $language_id)
					{
						break;
					}
				}
			}
			else
			{
				$result = xtc_db_query('SELECT categories_id
									FROM categories_description
									WHERE
										gm_url_keywords = ' . $this->v_binary_string . '"'. ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $boosted_name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) .'" AND
										language_id = "' . (int)$language_id . '"');
				if(mysqli_num_rows($result) > 0)
				{
					$categories_id = $this->_mysqlResult($result, 0, 'categories_id');
				}
			}
		}

		$this->_writeCache($cacheKey, $categories_id);

		return $categories_id;
	}


	public function get_boosted_content_url($p_content_id, $p_language_id = false, $p_item_name = '')
	{
		$languageId = ($p_language_id !== false) ? (int)$p_language_id : (int)$_SESSION['languages_id'];
		if(is_null($languageId))
		{
			$languageId = $this->languageProvider->getDefaultLanguageId();
		}
		
		$cacheKey = 'co-url-' . $p_content_id . '-' . $languageId . '-' . $p_item_name;
		
		if(array_key_exists($cacheKey, $this->seoBoostCache))
		{
			return $this->seoBoostCache[$cacheKey];
		}
		
		$languageId = ($p_language_id !== false) ? (int)$p_language_id : (int)$_SESSION['languages_id'];
		if(is_null($languageId))
		{
			$languageId = $this->languageProvider->getDefaultLanguageId();
		}

		$t_content_group = $this->get_content_group_by_content_id((int)$p_content_id);
		
		$urlRewrites = $this->contentUrlRewritesReader->get(new IdType($t_content_group));
		
		if($urlRewrites->count())
		{
			$languageCode = $this->languageProvider->getCodeById(new IdType($languageId))->asString();
			$urlRewrite   = $urlRewrites->keyExists($languageCode)
				? $urlRewrites->getValue($languageCode)
				: array_shift($urlRewrites->getArray());
			
			if(gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') == 'true')
			{
				$url = strtolower($languageCode) . '/info/' . $urlRewrite->getRewriteUrl() . '.html';
			}
			else
			{
				$url = 'info/' . $urlRewrite->getRewriteUrl() . '.html';
			}
			
			$this->_writeCache($cacheKey, $url);

			return $url;
		}
		
		$t_language_data_array = $this->get_language_data('content', $t_content_group, $p_language_id, $p_item_name);
		
		if(gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') == 'true')
		{
			$t_language_data_array['code'] .= '/';
		}
		else
		{
			$t_language_data_array['code'] = '';
		}

		$result = xtc_db_query('SELECT
									content_title 	AS content_title,
									content_heading AS content_heading,
									gm_url_keywords AS gm_url_keywords
								FROM content_manager
								WHERE
									content_id		= "'. (int)$p_content_id	.'" AND
									languages_id 	= "'. (int)$t_language_data_array['language_id']	.'"');
		if(xtc_db_num_rows($result) == 0)
		{
			$this->_writeCache($cacheKey, false);
			return false;
		}

		$data = xtc_db_fetch_array($result);

		$link_name = $this->clean_name($data['gm_url_keywords']);
		$renewed = false;

		if($link_name == '')
		{
			$link_name = $this->clean_keyword($data['content_heading']);
			$renewed = true;
		}
		if($link_name == '')
		{
			$link_name = $this->clean_keyword($data['content_title']);
			$renewed = true;
		}
		if($link_name == '')
		{
			$link_name = 'info-content-'.((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $p_content_id) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
			$renewed = true;
		}

		if($renewed)
		{
			xtc_db_query('UPDATE content_manager
							SET	gm_url_keywords = "' . ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $link_name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) . '"
							WHERE content_id = "' . (int)$p_content_id . '"');

			$this->repair('contents');

			$t_sql = "SELECT gm_url_keywords
						FROM content_manager
						WHERE content_id = '" . (int)$p_content_id . "'";
			$t_result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$link_name = $t_result_array['gm_url_keywords'];
			}
		}

		$link = $t_language_data_array['code'] . 'info/'. $link_name .'.html';

		$this->_writeCache($cacheKey, $link);

		return $link;
	}


	public function get_boosted_product_url($p_pID, $p_pName = '', $p_language_id = false, $p_url_keywords = '')
	{
		$languageId = ($p_language_id !== false) ? (int)$p_language_id : (int)$_SESSION['languages_id'];
		if(is_null($languageId))
		{
			$languageId = $this->languageProvider->getDefaultLanguageId();
		}
		
		$cacheKey = 'p-url-' . $p_pID . '-' . $languageId . '-' . $p_pName . '-' . $p_url_keywords;
		
		if(array_key_exists($cacheKey, $this->seoBoostCache))
		{
			return $this->seoBoostCache[$cacheKey];
		}
		
		$urlRewrites = $this->productUrlRewritesReader->getRewriteUrls(new IdType((int)$p_pID));
		
		if($urlRewrites->count())
		{
			$languageCode = $this->languageProvider->getCodeById(new IdType($languageId))->asString();
			$urlRewrite   = $urlRewrites->keyExists($languageCode)
				? $urlRewrites->getValue($languageCode)
				: array_shift($urlRewrites->getArray());

            if(gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') == 'true')
            {
                $url = strtolower($languageCode) . '/' . $urlRewrite->getRewriteUrl() . '.html';
            }
            else
            {
                $url = $urlRewrite->getRewriteUrl() . '.html';
            }
			$this->_writeCache($cacheKey, $url);

			return $url;
		}

		$t_language_data_array = $this->get_language_data('product', $p_pID, $p_language_id, $p_url_keywords);

		if(gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') == 'true')
		{
			$t_language_data_array['code'] .= '/';
		}
		else
		{
			$t_language_data_array['code'] = '';
		}
		
		$p_pName = $this->get_coolerized_product_name($p_pID, $t_language_data_array['language_id']);
		$t_link = $t_language_data_array['code'];

		$t_path = '';
		if(gm_get_conf('GM_SEO_BOOST_SHORT_URLS') == 'false')
		{
			$t_path = $this->get_product_path($p_pID, $t_language_data_array['language_id']);

			if($t_path != '')
			{
				$t_link .= $t_path . '/';
			}
		}

		$t_link .= $p_pName;
		$t_link .= '.html';

		$this->_writeCache($cacheKey, $t_link);

		return $t_link;
	}


	public function get_boosted_category_url($p_cID, $p_language_id = false, $p_item_name = '')
	{
		if(strlen($p_item_name) && substr($p_item_name, -1) === '/')
		{
			$p_item_name = substr($p_item_name, 0, -1);
		}
		
		$languageId = ($p_language_id !== false) ? (int)$p_language_id : (int)$_SESSION['languages_id'];
		if(is_null($languageId))
		{
			$languageId = $this->languageProvider->getDefaultLanguageId();
		}
		
		$cacheKey = 'c-url-' . $p_cID . '-' . $languageId . '-' . $p_item_name;
		
		if(array_key_exists($cacheKey, $this->seoBoostCache))
		{
			return $this->seoBoostCache[$cacheKey];
		}
		
		$urlRewrites = $this->categoryUrlRewritesReader->getRewriteUrls(new IdType((int)$p_cID));
		
		if($urlRewrites->count())
		{
			$languageCode = $this->languageProvider->getCodeById(new IdType($languageId))->asString();
			$urlRewrite   = $urlRewrites->keyExists($languageCode)
				? $urlRewrites->getValue($languageCode)
				: array_shift($urlRewrites->getArray());
			
			if(gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') == 'true')
			{
				$url = strtolower($languageCode) . '/' . $urlRewrite->getRewriteUrl() . '/';
			}
			else
			{
				$url = $urlRewrite->getRewriteUrl() . '/';
			}
			
			$this->_writeCache($cacheKey, $url);

			return $url;
		}
		
		$t_language_data_array = $this->get_language_data('category', $p_cID, $p_language_id, $p_item_name);
		
		if(gm_get_conf('USE_SEO_BOOST_LANGUAGE_CODE') == 'true')
		{
			$t_language_data_array['code'] .= '/';
		}
		else
		{
			$t_language_data_array['code'] = '';
		}

		$t_link = $t_language_data_array['code'] . $this->get_full_categories_names($p_cID, $t_language_data_array['language_id']);
		$t_link .= '/';

		$this->_writeCache($cacheKey, $t_link);

		return $t_link;
	}
	
	
	// $p_item_type: 'product' | 'category' | 'content'
	public function get_language_data($p_item_type, $p_item_id, $p_language_id = false, $p_item_name = '')
	{
		if($p_item_name === '' && !empty($p_language_id))
		{
			$languageId = new IdType((int)$p_language_id);
			
			return array(
				'language_id'      => $languageId->asInt(),
				'code'             => strtolower($this->languageProvider->getCodeById($languageId)->asString()),
				'directory'        => $this->languageProvider->getDirectoryById($languageId),
				'language_charset' => $this->languageProvider->getCharsetById($languageId)
			);
		}
		
		if(strlen($p_item_name) && substr($p_item_name, -1) === '/')
		{
			$p_item_name = substr($p_item_name, 0, -1);
		}
		
		$c_item_id = (int)$p_item_id;
		$c_item_name = xtc_db_input($p_item_name);
		
		// URL Rewrites START
		switch($p_item_type)
		{
			case 'product':
				$urlRewritesReader = $this->productUrlRewritesReader;
				break;
			
			case 'category':
				$urlRewritesReader = $this->categoryUrlRewritesReader;
				break;
			
			case 'content':
				$urlRewritesReader = $this->contentUrlRewritesReader;
				break;
			
			default:
				$urlRewritesReader = $this->_getUrlRewriteReader($p_item_type);
		}
		
		if($p_item_type === 'product' || $p_item_type === 'category')
		{
			if(is_string($p_item_name) && $p_item_name !== '')
			{
				$urlRewrites = $urlRewritesReader->findUrlRewritesByRewriteUrl(new NonEmptyStringType($p_item_name));
			}
			else
			{
				$urlRewrites = $urlRewritesReader->getRewriteUrls(new IdType($c_item_id));
			}
		}
		elseif($p_item_type === 'content')
		{
			if(is_string($p_item_name) && $p_item_name !== '')
			{
				$urlRewrites = $urlRewritesReader->findByRewriteUrl(new NonEmptyStringType($p_item_name));
			}
			else
			{
				$urlRewrites = $urlRewritesReader->get(new IdType($c_item_id));
			}
		}
		
		if(isset($urlRewrites) && $urlRewrites->count())
		{
            $languageId = ($p_language_id !== false) ? (int)$p_language_id : (int)$_SESSION['languages_id'];
            if(!empty($languageId))
            {
                $languageId   = new IdType($languageId);
            }
            else
            {
                $languageId   = new IdType($this->languageProvider->getDefaultLanguageId());
            }
            
            /** @var UrlRewrite $urlRewrite */
            foreach($urlRewrites as $urlRewrite)
            {
                if($languageId->asInt() === $urlRewrite->getLanguageId())
                {
                    return array(
                        'language_id'      => $languageId->asInt(),
                        'code'             => strtolower($this->languageProvider->getCodeById($languageId)->asString()),
                        'directory'        => $this->languageProvider->getDirectoryById($languageId),
                        'language_charset' => $this->languageProvider->getCharsetById($languageId)
                    );
                }
            }
            
            $urlRewrite = array_shift($urlRewrites->getArray());
            $languageId = new IdType($urlRewrite->getLanguageId());
            return array(
                'language_id'      => $languageId->asInt(),
                'code'             => strtolower($this->languageProvider->getCodeById($languageId)->asString()),
                'directory'        => $this->languageProvider->getDirectoryById($languageId),
                'language_charset' => $this->languageProvider->getCharsetById($languageId)
            );
            
		}
		// URL Rewrites END
        
        $t_language_data = array();
		$t_item_language_data_array = $this->get_language_item_data($p_item_type, $c_item_id, $c_item_name, $p_language_id);
		foreach($t_item_language_data_array as $t_current_language_data)
		{
			if($t_current_language_data['language_id'] == $_SESSION['languages_id'])
			{
				$t_language_data = $t_current_language_data;
			}
		}
		
		if(empty($t_language_data))
		{
			$t_language_data = array_shift($t_item_language_data_array);
		}
		
		return $t_language_data;
	}
	
	
	// $p_item_type: 'product' | 'category' | 'content'
	public function get_language_item_data($p_item_type, $p_item_id, $p_item_name, $p_language_id = false)
	{
		switch($p_item_type)
		{
			case 'product':
				$t_table_name = 'products_description';
				$t_item_id_name = 'products_id';
				$t_language_id_name = 'language_id';
				$t_gm_url_keywords_name = 'gm_url_keywords';
				break;
			case 'category':
				$t_table_name = 'categories_description';
				$t_item_id_name = 'categories_id';
				$t_language_id_name = 'language_id';
				$t_gm_url_keywords_name = 'gm_url_keywords';
				break;
			case 'content':
				$t_table_name = 'content_manager';
				$t_item_id_name = 'content_group';
				$t_language_id_name = 'languages_id';
				$t_gm_url_keywords_name = 'gm_url_keywords';
				break;
			default:
				return array();
		}
		
		if($p_language_id === false)
		{
			$t_sql_language_condition = '';
		}
		else
		{
			$t_sql_language_condition = ' AND item_table.' . $t_language_id_name . ' = "' . (int)$p_language_id . '"';
		}
		
		$c_item_name = $this->clean_name(basename($p_item_name));
		
		if(empty($c_item_name))
		{
			$t_sql_item_name_condition = '';
		}
		else
		{
			$t_sql_item_name_condition = ' AND item_table.' . $t_gm_url_keywords_name . ' LIKE "' . $c_item_name . '"';
		}
		
		$t_query = 'SELECT
						l.languages_id AS language_id,
						l.code AS code,
						l.directory,
						l.language_charset
					FROM
						' . $t_table_name . ' item_table
					LEFT JOIN
						languages l ON (l.languages_id = item_table.' . $t_language_id_name . ')
					WHERE
						item_table.' . $t_item_id_name . ' = ' . $p_item_id . '
						' . $t_sql_language_condition . '
						' . $t_sql_item_name_condition . '
					ORDER BY
						l.sort_order';
		$t_result = xtc_db_query($t_query);
		
		$t_language_item_data_array = array();
		
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_language_item_data_array[] = $t_row;
		}
		
		if(empty($t_language_item_data_array))
		{
			$t_language_item_data_array[] = array(
				'language_id' => (int)$_SESSION['languages_id'],
				'code' => $_SESSION['language_code'],
				'directory' => $_SESSION['directory'],
				'language_charset' => $_SESSION['language_charset']
			);
		}
		
		return $t_language_item_data_array;
	}


	public function get_coolerized_product_name($pID, $language_id=false)
	{
		if($language_id === false)
		{
			$language_id = (int)$_SESSION['languages_id'];
		}

		$result = xtc_db_query('SELECT
									products_name,
									gm_url_keywords
								FROM products_description
								WHERE
									products_id = "'. (int)$pID 				.'" AND
									language_id = "'. (int)$language_id .'"');
		$data = xtc_db_fetch_array($result);

		$link_name = $this->clean_name($data['gm_url_keywords']);
		$renewed = false;

		if($link_name == '')
		{
			$link_name = $this->clean_keyword($data['products_name']);
			$renewed = true;
		}

		if($renewed)
		{
			xtc_db_query('UPDATE products_description
							SET	gm_url_keywords = "'.((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $link_name) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")).'"
							WHERE
								products_id = "' . (int)$pID . '" AND
								language_id = "' . (int)$language_id . '"');

			$this->repair('products');

			$t_sql = "SELECT gm_url_keywords
						FROM products_description
						WHERE
							products_id = '" . (int)$pID . "' AND
							language_id = '" . (int)$language_id . "'";
			$t_result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$link_name = $t_result_array['gm_url_keywords'];
			}
		}

		$pName = $this->clean_name($link_name);

		return $pName;
	}


	public function get_product_path($products_id, $languages_id = false)
	{
		if($languages_id === false)
		{
			$languages_id = (int)$_SESSION['languages_id'];
		}

		$out = '';

		$result = xtc_db_query("SELECT
									categories_id
								FROM
									products_to_categories AS p2c
								WHERE
									p2c.products_id = '" . (int)$products_id . "'
									AND p2c.categories_id != 0
								ORDER BY categories_id ASC
								LIMIT 1");
		if(xtc_db_num_rows($result) > 0)
		{
			$data = xtc_db_fetch_array($result);
			$out = $this->get_full_categories_names($data['categories_id'], $languages_id);
		}

		return $out;
	}


	public function get_full_categories_names($categories_id, $languages_id = false)
	{
		if($languages_id === false)
		{
			$languages_id = (int)$_SESSION['languages_id'];
		}

		$result = xtc_db_query('SELECT
									c.parent_id AS parent_id,
									cd.categories_name 	AS categories_name,
									cd.gm_url_keywords 	AS gm_url_keywords
								FROM
									categories AS c
									LEFT JOIN categories_description AS cd USING (categories_id)
								WHERE
									cd.categories_id 	= "'.(int)$categories_id.'" AND
									cd.language_id 		= "'.(int)$languages_id	.'"');
		$data = xtc_db_fetch_array($result);

		$link_name = $this->clean_name($data['gm_url_keywords']);
		$renewed = false;

		if($link_name == '')
		{
			$link_name = $this->clean_keyword($data['categories_name']);
			$renewed = true;
		}

		if($renewed)
		{
			xtc_db_query('UPDATE categories_description
							SET	gm_url_keywords = "' . $link_name . '"
							WHERE
								categories_id 	= "' . (int)$categories_id . '" AND
								language_id 	= "' . (int)$languages_id . '"');

			$this->repair('categories');

			$t_sql = "SELECT gm_url_keywords
						FROM categories_description
						WHERE
							categories_id = '" . (int)$categories_id . "' AND
							language_id = '" . (int)$languages_id . "'";
			$t_result = xtc_db_query($t_sql);
			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				$link_name = $t_result_array['gm_url_keywords'];
			}
		}

		if($link_name != '')
		{
			$out = $link_name;
		}

		if($data['parent_id'] != 0)
		{
			$parent = $this->get_full_categories_names($data['parent_id'], $languages_id);
			$out = $parent .'/'. $out;
		}

		return $out;
	}


	public function repair($p_type = 'all')
	{
		if($p_type == 'all' || $p_type == 'products')
		{
			$t_get_languages_ids = xtc_db_query("SELECT languages_id
													FROM " . TABLE_LANGUAGES . "", 'db_link', false);
			while($t_result_array = xtc_db_fetch_array($t_get_languages_ids))
			{
				$c_languages_id = (int)$t_result_array['languages_id'];

				$t_get_empty_keywords = xtc_db_query("SELECT
															products_id,
															products_name
														FROM " . TABLE_PRODUCTS_DESCRIPTION. "
														WHERE
															(gm_url_keywords = '' OR LENGTH(gm_url_keywords) >= 255)
															AND language_id = '" . $c_languages_id . "'", 'db_link', false);
				while($t_product_array = xtc_db_fetch_array($t_get_empty_keywords))
				{
					$c_cleaned_name = $this->clean_keyword($t_product_array['products_name']);
					if(strlen_wrapper($c_cleaned_name . '-' . $t_product_array['products_id']) >= 255)
					{
						$c_cleaned_name = substr_wrapper($c_cleaned_name, 0, 100);
					}

					if($c_cleaned_name != '')
					{
						$t_update = xtc_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION. "
													SET gm_url_keywords = '" . $c_cleaned_name . "'
													WHERE
														products_id = '" . (int)$t_product_array['products_id'] . "'
														AND language_id = '" . $c_languages_id . "'", 'db_link', false);
					}
					else
					{
						$t_update = xtc_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION. "
													SET gm_url_keywords = 'product-" . (int)$t_product_array['products_id']  . "'
													WHERE
														products_id = '" . (int)$t_product_array['products_id'] . "'
														AND language_id = '" . $c_languages_id . "'", 'db_link', false);
					}
				}

				$t_found_double_keywords = true;

				while($t_found_double_keywords)
				{
					$t_products_array = array();
					$t_found = false;

					$t_get_double_keywords = xtc_db_query("SELECT DISTINCT
																a.products_id,
																a.gm_url_keywords
															FROM " . TABLE_PRODUCTS_DESCRIPTION. " a
															LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION. " AS b ON (" . $this->v_binary_string . "a.gm_url_keywords = " . $this->v_binary_string . "b.gm_url_keywords)
															WHERE
																a.products_id != b.products_id
																AND a.language_id = '" . $c_languages_id . "'
																AND b.language_id = '" . $c_languages_id . "'
															ORDER BY a.products_id", 'db_link', false);

					while($t_result_array = xtc_db_fetch_array($t_get_double_keywords))
					{
						$t_found = true;
						$t_products_array[$t_result_array['products_id']] = $t_result_array['gm_url_keywords'];
					}

					if(!$t_found)
					{
						$t_found_double_keywords = false;
					}
					else
					{
						$t_cleared_keywords_array = array();

						foreach($t_products_array AS $t_products_id => $t_gm_url_keywords)
						{
							$t_keys_array = array();
							$t_keys_array = array_keys($t_products_array, $t_gm_url_keywords);

							for($i = 1; $i < count($t_keys_array); $i++)
							{
								if(!in_array($t_gm_url_keywords, $t_cleared_keywords_array))
								{
									$t_new_url_keyword = preg_replace('/(.+?)(-[0-9]+)$/', "$1", $t_gm_url_keywords);
									$c_new_url_keyword = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_new_url_keyword) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

									$t_sql_products_id = $t_keys_array[$i];
									if($c_new_url_keyword . '-' . $t_keys_array[$i] == $t_gm_url_keywords)
									{
										$t_sql_products_id = $t_keys_array[0];
									}

									$t_update = xtc_db_query("UPDATE " . TABLE_PRODUCTS_DESCRIPTION. "
																SET gm_url_keywords = '" . $c_new_url_keyword . "-" . (int)$t_sql_products_id  . "'
																WHERE
																	products_id = '" . (int)$t_sql_products_id . "'
																	AND language_id = '" . $c_languages_id . "'", 'db_link', false);
								}
							}

							$t_cleared_keywords_array[] = $t_gm_url_keywords;
						}
					}
				}
			}
		}

		if($p_type == 'all' || $p_type == 'categories')
		{
			$t_get_languages_ids = xtc_db_query("SELECT languages_id
													FROM " . TABLE_LANGUAGES . "", 'db_link', false);
			while($t_result_array = xtc_db_fetch_array($t_get_languages_ids))
			{
				$c_languages_id = (int)$t_result_array['languages_id'];

				$t_get_empty_keywords = xtc_db_query("SELECT
															categories_id,
															categories_name
														FROM " . TABLE_CATEGORIES_DESCRIPTION. "
														WHERE
															(gm_url_keywords = '' OR LENGTH(gm_url_keywords) >= 255)
															AND language_id = '" . $c_languages_id . "'", 'db_link', false);
				while($t_category_array = xtc_db_fetch_array($t_get_empty_keywords))
				{
					$c_cleaned_name = $this->clean_keyword($t_category_array['categories_name']);
					if(strlen_wrapper($c_cleaned_name . '-' . $t_category_array['categories_id']) >= 255)
					{
						$c_cleaned_name = substr_wrapper($c_cleaned_name, 0, 100);
					}

					if($c_cleaned_name != '')
					{
						$t_update = xtc_db_query("UPDATE " . TABLE_CATEGORIES_DESCRIPTION. "
													SET gm_url_keywords = '" . $c_cleaned_name . "'
													WHERE
														categories_id = '" . (int)$t_category_array['categories_id'] . "'
														AND language_id = '" . $c_languages_id . "'", 'db_link', false);
					}
					else
					{
						$t_update = xtc_db_query("UPDATE " . TABLE_CATEGORIES_DESCRIPTION. "
													SET gm_url_keywords = 'category-" . (int)$t_category_array['categories_id']  . "'
													WHERE
														categories_id = '" . (int)$t_category_array['categories_id'] . "'
														AND language_id = '" . $c_languages_id . "'", 'db_link', false);
					}
				}

				$t_found_double_keywords = true;
				while($t_found_double_keywords)
				{
					$t_categories_array = array();
					$t_found = false;

					$t_get_double_keywords = xtc_db_query("SELECT DISTINCT
																a.categories_id,
																a.gm_url_keywords
															FROM " . TABLE_CATEGORIES_DESCRIPTION. " a
															LEFT JOIN " . TABLE_CATEGORIES_DESCRIPTION. " AS b ON (" . $this->v_binary_string . "a.gm_url_keywords = " . $this->v_binary_string . "b.gm_url_keywords)
															WHERE
																a.categories_id != b.categories_id
																AND a.language_id = '" . $c_languages_id . "'
																AND b.language_id = '" . $c_languages_id . "'
															ORDER BY a.categories_id ASC", 'db_link', false);
					while($t_result_array = xtc_db_fetch_array($t_get_double_keywords))
					{
						$t_found = true;
						$t_categories_array[$t_result_array['categories_id']] = $t_result_array['gm_url_keywords'];
					}

					if(!$t_found)
					{
						$t_found_double_keywords = false;
					}
					else
					{
						$t_cleared_keywords_array = array();

						foreach($t_categories_array AS $t_categories_id => $t_gm_url_keywords)
						{
							$t_keys_array = array();
							$t_keys_array = array_keys($t_categories_array, $t_gm_url_keywords);

							for($i = 1; $i < count($t_keys_array); $i++)
							{
								if(!in_array($t_gm_url_keywords, $t_cleared_keywords_array))
								{
									$t_new_url_keyword = preg_replace('/(.+?)(-[0-9]+)$/', "$1", $t_gm_url_keywords);
									$c_new_url_keyword = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $t_new_url_keyword) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));

									$t_sql_categories_id = $t_keys_array[$i];
									if($c_new_url_keyword . '-' . $t_keys_array[$i] == $t_gm_url_keywords)
									{
										$t_sql_categories_id = $t_keys_array[0];
									}

									$t_update = xtc_db_query("UPDATE " . TABLE_CATEGORIES_DESCRIPTION. "
																SET gm_url_keywords = '" . $c_new_url_keyword . "-" . (int)$t_sql_categories_id  . "'
																WHERE
																	categories_id = '" . (int)$t_sql_categories_id . "'
																	AND language_id = '" . $c_languages_id . "'", 'db_link', false);
								}
							}

							$t_cleared_keywords_array[] = $t_gm_url_keywords;
						}
					}
				}
			}
		}

		if($p_type == 'all' || $p_type == 'contents')
		{
			$t_get_languages_ids = xtc_db_query("SELECT languages_id
													FROM " . TABLE_LANGUAGES . "", 'db_link', false);
			while($t_result_array = xtc_db_fetch_array($t_get_languages_ids))
			{
				$c_languages_id = (int)$t_result_array['languages_id'];

				$t_get_empty_keywords = xtc_db_query("SELECT
															content_id,
															content_group,
															content_title,
															content_heading
														FROM " . TABLE_CONTENT_MANAGER . "
														WHERE
															(gm_url_keywords = '' OR LENGTH(gm_url_keywords) >= 255)
															AND languages_id = '" . $c_languages_id . "'", 'db_link', false);
				while($t_content_array = xtc_db_fetch_array($t_get_empty_keywords))
				{
					$c_cleaned_content_heading = $this->clean_keyword($t_content_array['content_heading']);
					if(strlen_wrapper($c_cleaned_content_heading . '-' . $t_content_array['content_id']) >= 255)
					{
						$c_cleaned_content_heading = substr_wrapper($c_cleaned_content_heading, 0, 100);
					}

					$c_cleaned_content_title = $this->clean_keyword($t_content_array['content_title']);
					if(strlen_wrapper($c_cleaned_content_title . '-' . $t_content_array['content_id']) >= 255)
					{
						$c_cleaned_content_title = substr_wrapper($c_cleaned_content_title, 0, 100);
					}

					if($c_cleaned_content_heading != '')
					{
						$t_update = xtc_db_query("UPDATE " . TABLE_CONTENT_MANAGER. "
													SET gm_url_keywords = '" . $c_cleaned_content_heading . "'
													WHERE
														content_group = '" . (int)$t_content_array['content_group'] . "'
														AND languages_id = '" . $c_languages_id . "'", 'db_link', false);
					}
					elseif($c_cleaned_content_title != '')
					{
						$t_update = xtc_db_query("UPDATE " . TABLE_CONTENT_MANAGER. "
													SET gm_url_keywords = '" . $c_cleaned_content_title . "'
													WHERE
														content_group = '" . (int)$t_content_array['content_group'] . "'
														AND languages_id = '" . $c_languages_id . "'", 'db_link', false);
					}
					else
					{
						$t_update = xtc_db_query("UPDATE " . TABLE_CONTENT_MANAGER. "
													SET gm_url_keywords = 'info-content-" . (int)$t_content_array['content_id'] . "'
													WHERE
														content_group = '" . (int)$t_content_array['content_group'] . "'
														AND languages_id = '" . $c_languages_id . "'", 'db_link', false);
					}
				}

				$t_found_double_keywords = true;
				while($t_found_double_keywords)
				{
					$t_content_array = array();
					$t_found = false;

					$t_get_double_keywords = xtc_db_query("SELECT DISTINCT
																a.content_group,
																a.gm_url_keywords
															FROM " . TABLE_CONTENT_MANAGER. " a
															LEFT JOIN " . TABLE_CONTENT_MANAGER. " AS b ON (" . $this->v_binary_string . "a.gm_url_keywords = " . $this->v_binary_string . "b.gm_url_keywords)
															WHERE
																a.content_group != b.content_group
																AND a.languages_id = '" . $c_languages_id . "'
																AND b.languages_id = '" . $c_languages_id . "'
															ORDER BY a.content_id ASC", 'db_link', false);
					while($t_result_array = xtc_db_fetch_array($t_get_double_keywords))
					{
						$t_found = true;
						$t_content_array[$t_result_array['content_group']] = $t_result_array['gm_url_keywords'];
					}

					if(!$t_found)
					{
						$t_found_double_keywords = false;
					}
					else
					{
						$t_cleared_keywords_array = array();

						foreach($t_content_array AS $t_content_group => $t_gm_url_keywords)
						{
							$t_keys_array = array();
							$t_keys_array = array_keys($t_content_array, $t_gm_url_keywords);

							for($i = 1; $i < count($t_keys_array); $i++)
							{
								if(!in_array($t_gm_url_keywords, $t_cleared_keywords_array))
								{
									$t_update = xtc_db_query("UPDATE " . TABLE_CONTENT_MANAGER. "
																SET gm_url_keywords = CONCAT(gm_url_keywords, '-" . $t_keys_array[$i]  . "')
																WHERE
																	content_group = '" . $t_keys_array[$i] . "'
																	AND languages_id = '" . $c_languages_id . "'", 'db_link', false);
								}
							}

							$t_cleared_keywords_array[] = $t_gm_url_keywords;
						}
					}
				}
			}
		}
	}


	public function clean_name($p_string, $p_strip_only_illegal_characters = false)
	{
		return xtc_cleanName($p_string);
	}
	
	
	public function clean_keyword($string)
	{
		$search  = array('ä', 'Ä', 'ö', 'Ö', 'ü', 'Ü', '&auml;', '&Auml;', '&ouml;', '&Ouml;', '&uuml;', '&Uuml;', 'ß', '&szlig;');
		$replace = array('ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', 'ae', 'Ae', 'oe', 'Oe', 'ue', 'Ue', 'ss', 'ss');
		$string  = str_replace($search, $replace, $string);
		
		$search  = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
		$replace = array('A', 'B', 'W', 'G', 'D', 'Ie', 'Io', 'Z', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'Ch', 'C', 'Tch', 'Sh', 'Shtch', '', 'Y', '', 'E', 'Iu', 'Ia', 'a', 'b', 'w', 'g', 'd', 'ie', 'io', 'z', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'ch', 'c', 'tch', 'sh', 'shtch', '', 'y', '', 'e', 'iu', 'ia');
		$string  = str_replace($search, $replace, $string);
		
		$search  = array('Á', 'À', 'Â', 'Ä', 'Ă', 'Ā', 'Ã', 'Å', 'Ą', 'Æ', 'Ć', 'Ċ', 'Ĉ', 'Č', 'Ç', 'Ď', 'Đ', 'Ð', 'É', 'È', 'Ė', 'Ê', 'Ë', 'Ě', 'Ē', 'Ę', 'Ə', 'Ġ', 'Ĝ', 'Ğ', 'Ģ', 'á', 'à', 'â', 'ä', 'ă', 'ā', 'ã', 'å', 'ą', 'æ', 'ć', 'ċ', 'ĉ', 'č', 'ç', 'ď', 'đ', 'ð', 'é', 'è', 'ė', 'ê', 'ë', 'ě', 'ē', 'ę', 'ə', 'ġ', 'ĝ', 'ğ', 'ģ', 'Ĥ', 'Ħ', 'I', 'Í', 'Ì', 'İ', 'Î', 'Ï', 'Ī', 'Į', 'Ĳ', 'Ĵ', 'Ķ', 'Ļ', 'Ł', 'Ń', 'Ň', 'Ñ', 'Ņ', 'Ó', 'Ò', 'Ô', 'Ö', 'Õ', 'Ő', 'Ø', 'Ơ', 'Œ', 'ĥ', 'ħ', 'ı', 'í', 'ì', 'i', 'î', 'ï', 'ī', 'į', 'ĳ', 'ĵ', 'ķ', 'ļ', 'ł', 'ń', 'ň', 'ñ', 'ņ', 'ó', 'ò', 'ô', 'ö', 'õ', 'ő', 'ø', 'ơ', 'œ', 'Ŕ', 'Ř', 'Ś', 'Ŝ', 'Š', 'Ş', 'Ť', 'Ţ', 'Þ', 'Ú', 'Ù', 'Û', 'Ü', 'Ŭ', 'Ū', 'Ů', 'Ų', 'Ű', 'Ư', 'Ŵ', 'Ý', 'Ŷ', 'Ÿ', 'Ź', 'Ż', 'Ž', 'ŕ', 'ř', 'ś', 'ŝ', 'š', 'ş', 'ß', 'ť', 'ţ', 'þ', 'ú', 'ù', 'û', 'ü', 'ŭ', 'ū', 'ů', 'ų', 'ű', 'ư', 'ŵ', 'ý', 'ŷ', 'ÿ', 'ź', 'ż', 'ž');
		$replace = array('A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'C', 'C', 'C', 'C', 'D', 'D', 'D', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'G', 'G', 'G', 'G', 'G', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'c', 'c', 'c', 'c', 'd', 'd', 'd', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'g', 'g', 'g', 'g', 'g', 'H', 'H', 'I', 'I', 'I', 'I', 'I', 'I', 'I', 'I', 'IJ', 'J', 'K', 'L', 'L', 'N', 'N', 'N', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'CE', 'h', 'h', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'ij', 'j', 'k', 'l', 'l', 'n', 'n', 'n', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'R', 'R', 'S', 'S', 'S', 'S', 'T', 'T', 'T', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'W', 'Y', 'Y', 'Y', 'Z', 'Z', 'Z', 'r', 'r', 's', 's', 's', 's', 'B', 't', 't', 'b', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'w', 'y', 'y', 'y', 'z', 'z', 'z');
		$string  = str_replace($search, $replace, $string);
		
		$string = strtolower($string);
		
		$string = preg_replace('/[^a-z0-9]/', '-', $string);
		$string = preg_replace('/--+/', '-', $string);
		$string = preg_replace('/^-(.*)/', "$1", $string);
		$string = preg_replace('/(.*)-$/', "$1", $string);
		
		return $string;
	}


	protected function _writeCache($key, $value)
	{
		$this->seoBoostCache[$key] = $value;
		$this->_updateCache();
	}


	protected function _updateCache()
	{
		$this->dataCache->set_data('seo_boost_cache', $this->seoBoostCache, true);
	}
	
	
	protected function _getUrlRewriteReader($p_contentType)
	{
		switch($p_contentType)
		{
			case 'product':
				$urlRewritesReader = StaticGXCoreLoader::getService('ProductRead');
				break;
			case 'category':
				$urlRewritesReader = StaticGXCoreLoader::getService('CategoryRead');
				break;
			case 'content':
				$db                           = StaticGXCoreLoader::getDatabaseQueryBuilder();
				$urlRewriteStorageContentType = new NonEmptyStringType('content');
				$urlRewritesReader            = MainFactory::create('UrlRewriteStorage', $urlRewriteStorageContentType,
				                                                    $db, $this->languageProvider);
				break;
			default:
				throw new InvalidArgumentException('GMSEOBoost: Unsupported content type for URL rewrites given. '
				                                   . 'Supported content types are \'product\', \'category\' and \'content\'. Got '
				                                   . gettype($p_contentType) . '): ' . $p_contentType);
		}
		
		return $urlRewritesReader;
	}

	protected function _mysqlResult($result, $row, $field)
	{
		$result->data_seek($row);
		$datarow = $result->fetch_array();

		return $datarow[$field];
	}
}

MainFactory::load_origin_class('GMSEOBoost');