<?php
/* --------------------------------------------------------------
	ConfigurationStorage.inc.php 2015-04-13
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/*
CREATE TABLE IF NOT EXISTS `configuration_storage` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `last_modified` timestamp NOT NULL DEFAULT '1000-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM
*/


class ConfigurationStorage
{
	const CS_SEPARATOR = '/';
	protected $_namespace;
	protected $_use_cache;

	public function __construct($p_namespace = '')
	{
		$this->_namespace = $p_namespace;
		$this->_use_cache = false;
	}

	/*
	** Create/Update
	*/

	public function set($p_key, $p_value)
	{
		$t_dbkey = $this->_make_db_key($p_key);
		$t_set_query =
			'REPLACE INTO
				`configuration_storage`
			SET
				`key` = \':key\',
				`value` = \':value\',
				`last_modified` = NOW()
			';
		$t_set_query = strtr($t_set_query, array(':key' => $t_dbkey, ':value' => $this->db_input($p_value)));
		xtc_db_query($t_set_query, 'db_link', $this->_use_cache);
	}

	public function set_all(array $p_tree)
	{
		$t_flat_array = $this->_flatten_array($p_tree);
		foreach($t_flat_array as $t_key => $t_value)
		{
			$this->set($t_key, $t_value);
		}
	}


	/*
	** Read
	*/

	public function get($p_key)
	{
		$t_dbkey = $this->_make_db_key($p_key);
		$t_get_query =
			'SELECT
				`value`
			FROM
				`configuration_storage`
			WHERE
				`key` = \':key\'
			';
		$t_get_query = strtr($t_get_query, array(':key' => $this->db_input($t_dbkey)));
		$t_value = false;
		$t_result = xtc_db_query($t_get_query, 'db_link', $this->_use_cache);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_value = $t_row['value'];
		}
		return $t_value;
	}

	public function get_all($p_prefix = '')
	{
		$t_dbkey = $this->_make_db_key($p_prefix.'%');
		$t_get_query =
			'SELECT
				`key`,
				`value`
			FROM
				`configuration_storage`
			WHERE
				`key` LIKE \':key\'
			ORDER BY
				`key`
			';
		$t_get_query = strtr($t_get_query, array(':key' => $this->db_input($t_dbkey)));
		$t_values = array();
		$t_result = xtc_db_query($t_get_query, 'db_link', $this->_use_cache);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_key = str_replace($this->_make_db_key(''), '', $t_row['key']);
			$t_values[$t_key] = $t_row['value'];
		}
		return $t_values;
	}

	public function get_all_tree($p_prefix = '')
	{
		$t_flat = $this->get_all($p_prefix);
		$t_tree = $this->_convert_to_tree_array($t_flat);
		return $t_tree;
	}


	/*
	** Delete
	*/

	public function delete($p_key)
	{
		$t_delete_query =
			'DELETE
				FROM `configuration_storage`
			WHERE
				`key` = \':key\'
			';
		$t_delete_query = strtr($t_delete_query, array(':key' => $this->_make_db_key($p_key)));
		xtc_db_query($t_delete_query);
	}

	/**
	* deletes entire namespace or a subtree from the namespace
	*/
	public function delete_all($p_prefix = '')
	{
		if($p_prefix != '')
		{
			$t_prefix = $p_prefix . self::CS_SEPARATOR;
		}
		else
		{
			$t_prefix = '';
		}
		$t_delete_query =
			'DELETE
				FROM `configuration_storage`
			WHERE
				`key` LIKE \':prefix%\'
			';
		$t_delete_query = strtr($t_delete_query, array(':prefix' => $this->_make_db_key($t_prefix)));
		xtc_db_query($t_delete_query);
	}



	/*
	** Helper functions
	*/

	protected function _make_db_key($p_key)
	{
		$t_db_key = $this->db_input($this->_namespace . self::CS_SEPARATOR . $p_key);
		return $t_db_key;
	}

	protected function _convert_to_tree_array(array $p_flat_array)
	{
		$t_out_array = array();
		foreach($p_flat_array as $t_flat_key => $t_value)
		{
			$t_split_key = explode(self::CS_SEPARATOR, $t_flat_key);
			$t_current_skey = array_shift($t_split_key);
			$t_current_node =& $t_out_array;
			while(empty($t_split_key) !== true)
			{
				if(isset($t_current_node[$t_current_skey]))
				{
					if(is_array($t_current_node[$t_current_skey]) !== true)
					{
						$t_current_node_value = $t_current_node[$t_current_skey];
						$t_current_node[$t_current_skey] = array('_' => $t_current_node_value);
					}
				}
				else
				{
					$t_current_node[$t_current_skey] = array();
				}
				$t_current_node =& $t_current_node[$t_current_skey];
				$t_current_skey = array_shift($t_split_key);
			}
			$t_current_node[$t_current_skey] = $t_value;
		}
		return $t_out_array;
	}

	public function _flatten_array(array $p_tree_array, $p_prefix = '')
	{
		$t_out_array = array();
		if(empty($p_prefix))
		{
			$t_top_prefix = '';
		}
		else
		{
			$t_top_prefix = $p_prefix . self::CS_SEPARATOR;
		}

		foreach($p_tree_array as $t_key => $t_value)
		{
			if(is_array($t_value))
			{
				$t_flattened_sub_tree = $this->_flatten_array($t_value, $t_key);
				foreach($t_flattened_sub_tree as $subtree_key => $subtree_value)
				{
					$t_out_array[$t_top_prefix . $subtree_key] = $subtree_value;
				}
			}
			else
			{
				if($t_key === '_')
				{
					$t_out_array[$p_prefix] = $t_value;
				}
				else
				{
					$t_out_array[$p_prefix . self::CS_SEPARATOR . $t_key] = $t_value;
				}
			}
		}

		return $t_out_array;
	}

	protected function db_input($string, $link = 'db_link')
	{
  		$$link = $GLOBALS[$link];
		if(function_exists('mysqli_real_escape_string'))
		{
    		return mysqli_real_escape_string( $$link, $string);
  		}
  		elseif(function_exists('mysqli_real_escape_string'))
  		{
    		return ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
  		}
		else
		{
			return addslashes($string);
		}
	}
}

