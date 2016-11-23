<?php
/* --------------------------------------------------------------
   CurrenciesBoxContentView.inc.php 2015-05-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(currencies.php,v 1.16 2003/02/12); www.oscommerce.com
   (c) 2003	 nextcommerce (currencies.php,v 1.11 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: currencies.php 1262 2005-09-30 10:00:32Z mz $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

// include functions
require_once(DIR_FS_INC . 'xtc_hide_session_id.inc.php');

class CurrenciesBoxContentView extends ContentView
{
	protected $coo_xtc_price;
	protected $requestType;
	protected $getArray = array();
	protected $hiddenGetVariables = '';
	protected $currenciesArray = array();
	protected $hiddenGetVariablesArray = array();
	protected $formMethod = '';
	protected $currenciesCount = 0;
	protected $getVariables = '';
	
	public function __construct()
	{
		parent::__construct();
		$this->set_content_template('boxes/box_currencies.html');
		$this->formMethod = 'get';
	}

	public function prepare_data()
	{
		$this->_setHiddenGetVariables();
		$this->_setCurrenciesArray();

		// don't show box if there's only 1 currency
		if($this->currenciesCount > 1)
		{
			$this->_setFormParams();
			$this->_setHiddenGetVariablesData();
			$this->_setSessionId();
			$this->_setCurrentCurrency();
			$this->_setCurrenciesData();
		}
	}

	public function setXtcPrice(xtcPrice $p_coo_xtc_price)
	{
		$this->coo_xtc_price = $p_coo_xtc_price;
	}

	public function setRequestType($p_request_type)
	{
		$this->requestType = (string)$p_request_type;
	}

	public function setGetArray(array $p_get_array)
	{
		$this->getArray = $p_get_array;
	}

	protected function _setHiddenGetVariables()
	{
		if(sizeof($this->getArray) > 0)
		{
			foreach($this->getArray as $kVariable => $vVariable)
			{
				$c_key = htmlentities_wrapper($kVariable);
				if(is_array($vVariable) == false)
				{
					$c_value = htmlentities_wrapper($vVariable);
				}
				else
				{
					$c_value = $vVariable;
				}

				if(	$c_key != 'currency' && $c_key != xtc_session_name() && $c_key != 'x' && $c_key != 'y' )
				{
					$this->hiddenGetVariables .= xtc_draw_hidden_field($c_key, $c_value);
					$this->hiddenGetVariablesArray[] = array('KEY' => $c_key, 'VALUE' => $c_value);
					$this->getVariables .= '&' . htmlspecialchars_wrapper((string)$c_key) . '=' . htmlspecialchars_wrapper((string)$c_value);
				}
			}
		}
	}
	
	protected function _setCurrenciesArray()
	{
		if(sizeof($this->coo_xtc_price->currencies) > 0){
			foreach($this->coo_xtc_price->currencies as $kCurrency => $vCurrency)
			{
				$c_key = htmlentities_wrapper($kCurrency);
				$this->currenciesCount++;
				$this->currenciesArray[] = array('id' => $c_key,
												 'text' => htmlentities_wrapper($vCurrency['title']),
												 'link' => xtc_href_link(basename(gm_get_env_info('PHP_SELF')), 'currency=' . $c_key . $this->getVariables, $this->requestType));
			}
		}
	}

	protected function _setCurrenciesData()
	{
		$this->set_content_data('currencies_data', $this->currenciesArray);
	}

	protected function _setCurrentCurrency()
	{
		$this->set_content_data('CURRENT_CURRENCY', $_SESSION['currency']);
	}

	protected function _setSessionId()
	{
		$this->set_content_data('SESSION_ID', xtc_session_id());
	}

	protected function _setHiddenGetVariablesData()
	{
		$this->set_content_data('hidden_get_variables_data', $this->hiddenGetVariablesArray);
	}

	protected function _setFormParams()
	{
		$this->_setFormId();
		$this->_setFormActionUrl();
		$this->_setFormMethod();
	}

	protected function _setFormId()
	{
		$this->set_content_data('FORM_ID', 'currencies');
	}

	protected function _setFormActionUrl()
	{
		$this->set_content_data('FORM_ACTION_URL', xtc_href_link(basename(gm_get_env_info('PHP_SELF')), '', $this->requestType, false, true, true));
	}

	protected function _setFormMethod()
	{
		$this->set_content_data('FORM_METHOD', $this->formMethod);
	}
}