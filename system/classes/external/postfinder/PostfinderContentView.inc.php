<?php
/* --------------------------------------------------------------
  PostfinderContentView.inc.php 2016-09-01
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
*/

class PostfinderContentView extends ContentView
{
	function PostfinderContentView()
	{
		parent::__construct();
		$this->set_content_template('module/postfinder.html');
		$this->set_flat_assigns(true);
	}

	function get_html()
	{
		if(empty($_SESSION['customer_id']))
		{
			xtc_redirect(GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_LOGIN);
		}

		$utility = MainFactory::create('PostfinderUtility');

		if($GLOBALS['messageStack']->size('postfinder') > 0)
		{
			$this->set_content_data('error', $GLOBALS['messageStack']->output('postfinder'));
		}

		if(isset($_GET['ab']))
		{
			$ab_id = (int)$_GET['ab'];
		}
		else
		{
			$ab_id = $_SESSION['customer_default_address_id'];
		}
		$address_data = $this->_getFromAddressBook($ab_id);

		if(isset($_REQUEST['pfinder_search_pstation']))
		{
			$result = $utility->findPackstations($_REQUEST['street'], $_REQUEST['streetno'], $_REQUEST['zip'], $_REQUEST['city'], true);
			//$this->set_content_data('RESULT', print_r($result, true));
			if(isset($result->packstation))
			{
				$this->set_content_data('PACKSTATIONS', $result->packstation);
			}
			else if(isset($result->packstation_filialedirekt))
			{
				$this->set_content_data('PACKSTATIONS', $result->packstation_filialedirekt);
			}
			else
			{
				$this->set_content_data('NO_RESULT', true);
			}
		}

		if(isset($_POST['ps2ab']) || isset($_POST['branch2ab']))
		{
			$postnumber = trim($_POST['postnumber']);
			if(empty($postnumber))
			{
				$GLOBALS['messageStack']->add_session('postfinder', $utility->get_text('postfinder_error_no_postnumber'));
				$redirect_url = GM_HTTP_SERVER . DIR_WS_CATALOG . 'postfinder.php?' . http_build_query($_GET);
				xtc_redirect($redirect_url);
			}
			if($utility->isValidPostnummer($postnumber) !== true)
			{
				$GLOBALS['messageStack']->add_session('postfinder', $utility->get_text('postfinder_error_postnumber_invalid'));
				$redirect_url = GM_HTTP_SERVER . DIR_WS_CATALOG . 'postfinder.php?' . http_build_query($_GET);
				xtc_redirect($redirect_url);
			}
			$ab_columns = $this->_getColumns('address_book');
			$insert_data = array();
			foreach($ab_columns as $colname)
			{
				if(array_key_exists($colname, $address_data))
				{
					$insert_data[$colname] = $address_data[$colname];
				}
			}
			unset($insert_data['address_book_id']);
			$insert_data['address_date_added'] = date('Y-m-d H:i-s');
			$insert_data['address_last_modified'] = date('Y-m-d H:i-s');
			$address_class = 'postfiliale';
			if(isset($_POST['ps2ab']))
			{
				$insert_data['entry_street_address'] = 'Packstation ' . xtc_db_input($_POST['psid']);
				$address_class = 'packstation';
			}
			if(isset($_POST['branch2ab']))
			{
				$insert_data['entry_street_address'] = 'Postfiliale ' . xtc_db_input($_POST['depotid']);
			}
			$insert_data['entry_postcode'] = xtc_db_input($_POST['pszip']);
			$insert_data['entry_city'] = xtc_db_input($_POST['pscity']);
			$insert_data['entry_lastname'] .= '/' . $postnumber; // for the time being, this is the best solution.
			$insert_data['entry_house_number'] = '';
			$insert_data['entry_additional_info'] = '';
			$insert_data['address_class'] = $address_class;
			xtc_db_perform('address_book', $insert_data, 'insert');
			$ps_ab_id = xtc_db_insert_id();
			if(isset($_GET['checkout_started']))
			{
				$_SESSION['sendto'] = $ps_ab_id;
				$redirect_url = GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_CHECKOUT_SHIPPING;
			}
			else
			{
				$redirect_url = GM_HTTP_SERVER . DIR_WS_CATALOG . FILENAME_ADDRESS_BOOK;
			}
			$redirect_url .= '?'.xtc_session_name().'='.xtc_session_id();
			xtc_redirect($redirect_url);
		}

		$this->set_content_data('STREET', $address_data['street']);
		$this->set_content_data('STREETNO', $address_data['streetno']);
		$this->set_content_data('ZIP', $address_data['zip']);
		$this->set_content_data('CITY', $address_data['city']);
		$this->set_content_data('ABOOK', $this->_getAddressBook($_SESSION['customer_id']));
		$page_url = 'postfinder.php?';
		$this->set_content_data('FORM_ACTION', $page_url . http_build_query($_GET));
		if(isset($_GET['checkout_started']))
		{
			$page_url .= 'checkout_started=1&';
			$this->set_content_data('CHECKOUT_STARTED', true);
		}
		$this->set_content_data('PAGE_URL', $page_url);

		$t_html_output = $this->build_html();
		return $t_html_output;
	}

	function _getAddressBook($customers_id)
	{
		$query = "SELECT * FROM address_book WHERE customers_id = :customers_id AND address_class <> 'packstation'AND address_class <> 'postfiliale'";
		$query = strtr($query, array(':customers_id' => (int)$customers_id));
		$result = xtc_db_query($query);
		$ab = array();
		while($row = xtc_db_fetch_array($result))
		{
			$ab[] = $row;
		}
		return $ab;
	}

	function _getFromAddressBook($ab_id = null)
	{
		$ab_data = false;
		$query_raw = "SELECT * FROM address_book WHERE address_book_id = :ab_id AND customers_id = :customers_id";
		$query_default = strtr($query_raw, array(':ab_id' => $_SESSION['customer_default_address_id'], ':customers_id' => (int)$_SESSION['customer_id']));
		$result_default = xtc_db_query($query_default);
		while($row = xtc_db_fetch_array($result_default))
		{
			if(empty($row['entry_house_number']))
			{
				list($street, $streetno) = $this->_splitStreetAddress($row['entry_street_address']);
			}
			else
			{
				$street   = $row['entry_street_address'];
				$streetno = $row['entry_house_number'];
			}
			$ab_data = array(
				'street' => $street,
				'streetno' => $streetno,
				'zip' => $row['entry_postcode'],
				'city' => $row['entry_city'],
			);
			$ab_data = array_merge($ab_data, $row);
		}
		if($ab_id !== null)
		{
			$query = strtr($query_raw, array(':ab_id' => (int)$ab_id, ':customers_id' => (int)$_SESSION['customer_id']));
			$result = xtc_db_query($query);
			while($row = xtc_db_fetch_array($result))
			{
				if(empty($row['entry_house_number']))
				{
					list($street, $streetno) = $this->_splitStreetAddress($row['entry_street_address']);
				}
				else
				{
					$street   = $row['entry_street_address'];
					$streetno = $row['entry_house_number'];
				}
				$ab_data = array(
					'street' => $street,
					'streetno' => $streetno,
					'zip' => $row['entry_postcode'],
					'city' => $row['entry_city'],
				);
				$ab_data = array_merge($ab_data, $row);
			}
		}
		return $ab_data;
	}

	function _splitStreetAddress($street_address)
	{
		if(preg_match('/(.*)\s+(\d+.*)/', $street_address, $matches) == 1)
		{
			$street = $matches[1];
			$streetno = $matches[2];
		}
		else
		{
			$street = $street_address;
			$streetno = '';
		}
		return array($street, $streetno);
	}

	function _getColumns($table)
	{
		$query = "SHOW COLUMNS FROM $table";
		$result = xtc_db_query($query);
		$cols = array();
		while($row = xtc_db_fetch_array($result))
		{
			$cols[] = $row['Field'];
		}
		return $cols;
	}

}