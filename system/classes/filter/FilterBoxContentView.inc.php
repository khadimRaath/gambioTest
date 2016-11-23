<?php
/* --------------------------------------------------------------
  FilterBoxContentView.inc.php 2016-08-23
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

require_once DIR_FS_INC . 'xtc_get_all_get_params.inc.php';

class FilterBoxContentView extends ContentView
{
	protected $categoryId = 0;
	protected $languageId;
	protected $selectedValuesArray;
	protected $priceStart = false;
	protected $priceEnd = false;
	protected $filterUrl = false;
	
	protected $featureControl;
	protected $featureSetSource;
	protected $gmSEOBoost;
	
	protected $featureMode; // Alle Filterboxen auswählbar || Stufenmodus
	protected $featureDisplayMode; // Nicht verfügbare Werte ausgeblendet || ausgegraut
	protected $featureEmptyBoxMode; // Hinweis-Text in leerer Filterbox || Leere Filterbox ausblenden
	
	
	/*
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_filter.html');

		$this->featureControl = MainFactory::create_object('FeatureControl');
		$this->featureSetSource = MainFactory::create_object('FeatureSetSource');
		$this->gmSEOBoost = MainFactory::create_object('GMSEOBoost');
	}

	/*
	 * create HTML code for every feature
	 * @param int $p_categories_id  category_id
	 * @param int $p_language_id  shop language_id
	 * @param array $this->selectedValuesArray  array with feature value ids
	 * @return array $t_html_output  the HTML code as array for output
	 */
	public function prepare_data()
	{
		$t_coo_cat_filter = $this->featureControl->get_categories_filter_array(array('categories_id' => $this->categoryId), array('sort_order'));

		$this->featureMode = gm_get_conf('FEATURE_MODE');
		$this->featureDisplayMode = gm_get_conf('FEATURE_DISPLAY_MODE');
		$this->featureEmptyBoxMode = gm_get_conf('FEATURE_EMPTY_BOX_MODE');
		if($this->categoryId > 0)
		{
			$coo_categories = MainFactory::create_object('GMDataObject', array('categories', array('categories_id' => $this->categoryId)));
			$this->featureMode = $coo_categories->get_data_value('feature_mode');
			$this->featureDisplayMode = $coo_categories->get_data_value('feature_display_mode');
		}

		$t_selected_feature_value_id_array = array();
		if(is_array($this->selectedValuesArray))
		{
			if(is_object($GLOBALS['coo_debugger']))
			{
				$GLOBALS['coo_debugger']->log('FilterBoxContentView get_html() $this->selectedValuesArray: ' . print_r($this->selectedValuesArray, true), 'FilterManager');
			}
			// set values array
			$t_selected_feature_value_id_array = $this->selectedValuesArray;
			// generate feature to values array
			$t_feature_values = $this->featureSetSource->convert_values_array_to_feature_values_array($this->selectedValuesArray);
		}
		else
		{
			$t_allowed_features = false;
			if($this->featureMode == 1)
			{
				// get coherently feature in steps mode
				$t_allowed_features = array();
				$t_features_array = $this->featureSetSource->extract_features_from_feature_values_string($this->selectedValuesArray);
				foreach($t_coo_cat_filter as $t_coo_filter)
				{
					if(!in_array($t_coo_filter->v_feature_id, $t_features_array))
					{
						break;
					}
					$t_allowed_features[] = $t_coo_filter->v_feature_id;
				}
			}

			$t_selected_feature_value_id_array = $this->featureSetSource->extract_values_from_feature_values_string($this->selectedValuesArray, $t_allowed_features);
			$t_feature_values = $this->selectedValuesArray;
		}

		// reset SESSION if no values selected
		if($t_feature_values == '')
		{
			$_SESSION["filter_history"] = array();
		}

		$t_html_array = array();

		$t_all_feature_values_of_all_sets = $this->featureSetSource->get_available_feature_values_by_feature_values($this->categoryId, array());
		$t_set_bound_feature_values = $this->featureSetSource->get_available_feature_values_by_feature_values($this->categoryId, $t_feature_values);
		$t_selected_values_count = 0;
		foreach($t_coo_cat_filter as $t_coo_filter)
		{
			$t_feature_id = $t_coo_filter->v_feature_id;
			$t_template = $t_coo_filter->v_selection_template;
			$t_value_conjunction = $t_coo_filter->v_value_conjunction;
			$t_feature_name = $t_coo_filter->get_feature_name($this->languageId);
			$t_feature_value_array = $this->featureControl->get_feature_value_array($t_feature_id);
			$t_feature_value_array = $this->featureControl->get_feature_value_description($t_feature_value_array);

			$t_feature_empty = true;

			$t_feature_value_data_array = array();
			foreach($t_feature_value_array as $f_coo_feature)
			{
				if(!in_array($f_coo_feature['feature_value_id'], $t_all_feature_values_of_all_sets))
					continue;
				if(!in_array($f_coo_feature['feature_value_id'], $t_set_bound_feature_values) && $this->featureDisplayMode == 0)
					continue;
				$t_visible = true;
				if(!in_array($f_coo_feature['feature_value_id'], $t_set_bound_feature_values))
				{
					$t_visible = false;
				}
				else
				{
					$t_feature_empty = false;
				}

				$t_feature_value_id = $f_coo_feature['feature_value_id'];
				$t_feature_value_name = $f_coo_feature['feature_value_text_array'][$this->languageId];

				# feature_value_id selected TRUE/FALSE
				$t_feature_value_selected = in_array($t_feature_value_id, $t_selected_feature_value_id_array);
				if($t_feature_value_selected)
					$t_selected_values_count++;

				$t_feature_value_data_array[] = array(
					'ID' => $t_feature_value_id,
					'NAME' => $t_feature_value_name,
					'SELECTED' => $t_feature_value_selected,
					'DISPLAY_MODE' => $this->featureDisplayMode,
					'VISIBLE' => $t_visible
				);
			}

			$t_coo_content_view = MainFactory::create_object('ContentView');
			$t_coo_content_view->set_content_template('module/filter_selection/' . $t_template);


			$t_coo_content_view->set_content_data('FEATURE_NAME', $t_feature_name);
			$t_coo_content_view->set_content_data('FEATURE_VALUE_DATA', $t_feature_value_data_array);
			$t_coo_content_view->set_content_data('FEATURE_ID', $t_feature_id);
			$t_coo_content_view->set_content_data('VALUE_CONJUNCTION', (int)$t_value_conjunction);

			$t_content_array = array();
			$t_content_array['html'] = $t_coo_content_view->get_html(0);
			$t_content_array['empty_feature'] = $t_feature_empty;

			$t_html_array[] = $t_content_array;
		}

		$t_html_array_count = count($t_html_array);
		for($i = 0; $i < $t_html_array_count; $i++)
		{
			if($i == 0)
			{
				$t_html_array[$i]['show'] = true;
			}
			else
			{
				if(strpos($t_html_array[$i - 1]['html'], 'checked') || strpos($t_html_array[$i - 1]['html'], 'selected'))
				{
					$t_html_array[$i]['show'] = true;
				}
				else
				{
					$t_html_array[$i]['show'] = false;
				}
			}
		}
		if($t_selected_values_count == 0)
		{
			$_SESSION["filter_history"] = array();
		}

		# contains html code for feature_value selections
		$this->set_content_data('FEATURE_DATA', $t_html_array);

		$this->set_content_data('FEATURE_MODE', $this->featureMode);
		$this->set_content_data('FEATURE_DISPLAY_MODE', $this->featureDisplayMode);
		$this->set_content_data('FEATURE_EMPTY_BOX_MODE', $this->featureEmptyBoxMode);
		$this->set_content_data('categories_id', (int)$this->categoryId);
		
		$t_action_url = '';
		
		if($this->gmSEOBoost->boost_categories == true && xtc_not_null($_GET['gm_boosted_category']))
		{
			# use boosted url
			$t_action_url = $this->gmSEOBoost->get_current_boost_url();
		}
		
		if($this->filterUrl != false)
		{
			$t_action_url = $this->filterUrl;
		}
		elseif($t_action_url === '')
		{
			$t_action_url = 'index.php';
			
			# use default url for splitting urls
			$t_all_get_params = trim(urldecode(xtc_get_all_get_params()), '&');
			
			if($t_all_get_params !== '')
			{
				$t_action_url .= '?' . $t_all_get_params;
			}
		}
		
		$t_action_url = xtc_href_link($t_action_url, '', 'NONSSL', true, true, true);

		//$t_action_url = gm_get_env_info('REQUEST_URI');
		$this->set_content_data('FILTER_URL', $t_action_url);
		$this->set_content_data('FORM_ACTION_URL', $t_action_url);
		$this->set_content_data('CURRENCY', $_SESSION['currency']);

		# entered prices
		$t_price_start = '';
		$t_price_end = '';

		if($this->priceStart !== false && !empty($this->priceStart))
		{
			$t_price_start = htmlentities_wrapper($this->priceStart);
		}
		if($this->priceEnd !== false && !empty($this->priceEnd))
		{
			$t_price_end = htmlentities_wrapper($this->priceEnd);
		}

		$this->set_content_data('DEFAULT_PRICE_START', $t_price_start);
		$this->set_content_data('DEFAULT_PRICE_END', $t_price_end);

		$this->set_content_data('PRICE_FILTER_FROM_ACTIVE', gm_get_conf('PRICE_FILTER_FROM_ACTIVE'));
		$this->set_content_data('PRICE_FILTER_TO_ACTIVE', gm_get_conf('PRICE_FILTER_TO_ACTIVE'));
	}


	/**
	 * @return int
	 */
	public function getCategoryId()
	{
		return $this->categoryId;
	}


	/**
	 * @param int $categoryId
	 */
	public function setCategoryId($categoryId)
	{
		$this->categoryId = (int)$categoryId;
	}


	/**
	 * @return boolean
	 */
	public function isFilterUrl()
	{
		return $this->filterUrl;
	}


	/**
	 * @param boolean $filterUrl
	 */
	public function setFilterUrl($filterUrl)
	{
		$this->filterUrl = (string)$filterUrl;
		
		$parts = parse_url($filterUrl);
		parse_str($parts['query'], $getParams);
		
		if(array_key_exists('filter_url', $getParams))
		{
			$this->filterUrl = (string)rawurldecode($getParams['filter_url']);
		}
	}


	/**
	 * @return mixed
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}


	/**
	 * @param mixed $languageId
	 */
	public function setLanguageId($languageId)
	{
		$this->languageId = (int)$languageId;
	}


	/**
	 * @return boolean
	 */
	public function isPriceEnd()
	{
		return $this->priceEnd;
	}


	/**
	 * @param boolean $priceEnd
	 */
	public function setPriceEnd($priceEnd)
	{
		$this->priceEnd = (string)$priceEnd;
	}


	/**
	 * @return boolean
	 */
	public function isPriceStart()
	{
		return $this->priceStart;
	}


	/**
	 * @param boolean $priceStart
	 */
	public function setPriceStart($priceStart)
	{
		$this->priceStart = (string)$priceStart;
	}


	/**
	 * @return boolean
	 */
	public function isSelectedValuesArray()
	{
		return $this->selectedValuesArray;
	}


	/**
	 * @param mixed $selectedValuesArray
	 */
	public function setSelectedValuesArray($selectedValuesArray)
	{
		$this->selectedValuesArray = $selectedValuesArray;
	}
}