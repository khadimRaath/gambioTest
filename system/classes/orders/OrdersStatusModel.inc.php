<?php
/* --------------------------------------------------------------
	OrdersStatusModel.inc.php 2014-11-05
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class OrdersStatusModel extends BaseClass
{
	const MAX_NAME_LENGTH = 32;
	/* @var $orders_status_id int */
	protected $orders_status_id;

	/* @var $orders_status_names array */
	protected $orders_status_names;

	/** initializes OrderStatusModel instance, optionally using database data identified by orders_status_id */
	public function __construct($p_orders_status_id = null)
	{
		if(!is_null($p_orders_status_id))
		{
			$this->set_('orders_status_id', (int)$p_orders_status_id);
			$this->load();
		}
	}

	/** load data belonging to the currently set orders_status_id */
	public function load()
	{
		if(is_null($this->orders_status_id))
		{
			throw new OrdersStatusModelInvalidIDException();
		}

		$load_query =
			'SELECT
				*
			FROM
				orders_status
			WHERE
				orders_status_id = \':orders_status_id\'';
		$load_query = strtr($load_query, array(':orders_status_id' => (int)$this->orders_status_id));
		$load_result = xtc_db_query($load_query, 'db_link', false);
		while($load_row = xtc_db_fetch_array($load_result))
		{
			$this->setStatusName($load_row['orders_status_name'], (int)$load_row['language_id']);
		}
	}

	/** load data identified by status name
	@param $p_orders_status_name string
	@param $p_language_id int/null
	*/
	public function loadByName($p_orders_status_name, $p_language_id = null)
	{
		if(mb_strlen($p_orders_status_name) > self::MAX_NAME_LENGTH)
		{
			throw new OrderStatusModelNameTooLongException();
		}
		$orders_status_id = $this->findOrdersStatusIDByName($p_orders_status_name, $p_language_id);
		$this->orders_status_id = $orders_status_id;
		$this->load();
	}

	/** find orders_status_id corresponding to a given status name
	Search can optionally be limited to one language by giving a language_id.
	@param $p_orders_status_name string
	@param $p_languages_id int/null
	@return int language_id
	@throws OrdersStatusModelStatusNotFoundException
	*/
	public function findOrdersStatusIDByName($p_orders_status_name, $p_language_id = null)
	{
		$find_query =
			'SELECT
				orders_status_id
			FROM
				orders_status
			WHERE
				orders_status_name = \':orders_status_name\'';
		if(!is_null($p_language_id))
		{
			$find_query .= ' AND language_id = \':language_id\'';
		}
		$find_query = strtr($find_query,
			array(
				':orders_status_name' => xtc_db_input($p_orders_status_name),
				':language_id' => (int)$p_language_id,
			)
		);
		$find_result = xtc_db_query($find_query);
		$orders_status_id = null;
		while($find_row = xtc_db_fetch_array($find_result))
		{
			$orders_status_id = $find_row['orders_status_id'];
		}
		if($orders_status_id === null)
		{
			throw new OrdersStatusModelStatusNotFoundException();
		}
		return $orders_status_id;
	}

	/** save OrdersStatus to database
	If orders_status_id is not set, a new status will be created.
	@return int orders_status_id
	*/
	public function save()
	{
		if(is_null($this->orders_status_id))
		{
			// create new OrdersStatus
			$this->orders_status_id = $this->findNextOrdersStatusID();
		}

		$replace_query =
			'REPLACE INTO
				orders_status
			SET
				orders_status_id = \':orders_status_id\',
				language_id = \':language_id\',
				orders_status_name = \':orders_status_name\'';
		foreach($this->orders_status_names as $language_id => $orders_status_name)
		{
			$current_query = strtr($replace_query,
				array(
					':orders_status_id' => (int)$this->orders_status_id,
					':language_id' => (int)$language_id,
					':orders_status_name' => xtc_db_input($orders_status_name),
					)
			);
			xtc_db_query($current_query);
		}

		return $this->orders_status_id;
	}

	/** sets orders_status_id for this status
	@param $p_orders_status_id int
	*/
	public function set_orders_status_id($p_orders_status_id)
	{
		$this->orders_status_id = (int)$p_orders_status_id;
	}

	/** sets name for status
	@param $p_name string
	@param $p_language_id int
	*/
	public function setStatusName($p_name, $p_language_id)
	{
		if(mb_strlen($p_name) > self::MAX_NAME_LENGTH)
		{
			throw new OrderStatusModelNameTooLongException();
		}
		$this->orders_status_names[(int)$p_language_id] = $p_name;
	}

	/** returns the name (textual representation) for the OrdersStatus
	If $p_language_id is null, the session's default language will be used
	@param $p_language_id int/null
	*/
	public function getStatusName($p_language_id = null)
	{
		if(is_null($p_language_id))
		{
			$p_language_id = $_SESSION['languages_id'];
		}
		if(!isset($this->orders_status_names[(int)$p_language_id]))
		{
			throw new OrdersStatusModelNameNotFoundException('orders_status_name for language '.(int)$p_language_id.' not available');
		}
		return $this->orders_status_names[(int)$p_language_id];
	}

	/** finds lowest unused orders_status_id
	@return int
	*/
	public function findNextOrdersStatusID()
	{
		$next_id = null;
		$max_query =
			'SELECT
				MAX(orders_status_id) AS max_id
			FROM
				orders_status';
		$max_result = xtc_db_query($max_query, 'db_link', false);
		while($max_row = xtc_db_fetch_array($max_result))
		{
			$next_id = $max_row['max_id'];
		}
		$next_id = $next_id + 1;
		return $next_id;
	}

	/** deletes this status
	Throws OrdersStatusModelStatusInUseException if this status is still referenced by any order
	*/
	public function delete()
	{
		if($this->isInUse())
		{
			throw new OrdersStatusModelStatusInUseException();
		}
		$delete_query =
			'DELETE FROM orders_status
			WHERE orders_status_id = \':orders_status_id\'';
		$delete_query = strtr($delete_query, array(':orders_status_id' => (int)$this->orders_status_id));
		xtc_db_query($delete_query);
		$this->orders_status_id = null;
	}

	/** returns whether or not this status is currently in use by an order */
	public function isInUse()
	{
		$num_uses = 0;

		$orders_query =
			'SELECT
				COUNT(*) AS num_uses
			FROM
				orders
			WHERE
				orders_status = \':orders_status_id\'';
		$orders_query = strtr($orders_query, array(':orders_status_id' => (int)$this->orders_status_id));
		$orders_result = xtc_db_query($orders_query);
		while($orders_row = xtc_db_fetch_array($orders_result))
		{
			$num_uses += (int)$orders_row['num_uses'];
		}

		$orders_status_history_query =
			'SELECT
				COUNT(*) AS num_uses
			FROM
				orders_status_history
			WHERE
				orders_status_id = \':orders_status_id\'';
		$orders_status_history_query = strtr($orders_status_history_query, array(':orders_status_id' => (int)$this->orders_status_id));
		$orders_status_history_result = xtc_db_query($orders_status_history_query);
		while($orders_status_history_row = xtc_db_fetch_array($orders_status_history_result))
		{
			$num_uses += $$orders_status_history_row['num_uses'];
		}

		if($num_uses > 0)
		{
			$isInUse = true;
		}
		else
		{
			$isInUse = false;
		}
		return $isInUse;
	}
}


class OrdersStatusModelException extends Exception {
	public function __construct($message = '')
	{
		parent::__construct($message);
	}
}
class OrdersStatusModelInvalidIDException extends OrdersStatusModelException {}
class OrdersStatusModelStatusNotFoundException extends OrdersStatusModelException {}
class OrdersStatusModelNameNotFoundException extends OrdersStatusModelException {}
class OrderStatusModelNameTooLongException extends OrdersStatusModelException {}
class OrdersStatusModelStatusInUseException extends OrdersStatusModelException {}
