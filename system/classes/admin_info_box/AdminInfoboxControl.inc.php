<?php
/* --------------------------------------------------------------
   AdminInfoboxControl.inc.php 2015-04-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

defined('TABLE_INFOBOX_MESSAGES') or define('TABLE_INFOBOX_MESSAGES', 'infobox_messages');
defined('TABLE_INFOBOX_MESSAGES_DESCRIPTION') or define('TABLE_INFOBOX_MESSAGES_DESCRIPTION', 'infobox_messages_description');

class AdminInfoboxControl
{
	var $v_reactivation_time_limit = 604800; // in seconds (604800 = 1 week)

	function add_message($p_message_array, $p_type = 'info', $p_headline_array = array(), $p_button_label_array = array(), $p_button_link = '', $p_visibility = 'hideable', $p_status = 'new', $p_identifier = '', $p_source = 'intern', $p_visible_for_all = false, $p_overwrite = false)
	{
		$t_infobox_message_id = false;

		if(is_array($p_message_array))
		{
			$c_type = 'info';
			if(in_array($p_type, array('info', 'success', 'warning')))
			{
				$c_type = (string)$p_type;
			}

			$c_button_link = gm_prepare_string((string)$p_button_link, true);
			$c_visibility = gm_prepare_string((string)$p_visibility, true);
			$c_status = gm_prepare_string((string)$p_status, true);
			$c_identifier = gm_prepare_string((string)$p_identifier, true);
			$c_source = gm_prepare_string((string)$p_source, true);

			$c_customers_id = $_SESSION['customer_id'];
			if($p_visible_for_all)
			{
				$c_customers_id = 0;
			}

			$coo_infobox_messages = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES, array('identifier' => $c_identifier, 'customers_id' => $c_customers_id)));
			$t_infobox_messages_id = (int)$coo_infobox_messages->get_data_value('infobox_messages_id');
			if($t_infobox_messages_id > 0 && $p_overwrite === false)
			{
				return $t_infobox_messages_id;
			}
			elseif($t_infobox_messages_id > 0)
			{
				$this->delete($t_infobox_messages_id);
			}

			$coo_infobox_messages = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES));

			$coo_infobox_messages->set_data_value('source', $c_source);
			$coo_infobox_messages->set_data_value('identifier', $c_identifier);
			$coo_infobox_messages->set_data_value('status', $c_status);
			$coo_infobox_messages->set_data_value('type', $c_type);
			$coo_infobox_messages->set_data_value('visibility', $c_visibility);
			$coo_infobox_messages->set_data_value('button_link', $c_button_link);
			$coo_infobox_messages->set_data_value('customers_id', $c_customers_id);
			$coo_infobox_messages->set_data_value('date_added', date('Y-m-d H:i:s'));

			$t_infobox_message_id = $coo_infobox_messages->save_body_data();

			foreach($p_message_array AS $t_languages_id => $t_message)
			{
				$c_languages_id = (int)$t_languages_id;
				$c_message = gm_prepare_string((string)$t_message, true);

				$c_headline = '';
				if(isset($p_headline_array[$c_languages_id]))
				{
					$c_headline = gm_prepare_string((string)$p_headline_array[$c_languages_id], true);
				}

				$c_button_label = '';
				if(isset($p_button_label_array[$c_languages_id]))
				{
					$c_button_label = gm_prepare_string((string)$p_button_label_array[$c_languages_id], true);
				}

				$coo_infobox_messages_description = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES_DESCRIPTION));

				$coo_infobox_messages_description->set_data_value('infobox_messages_id', $t_infobox_message_id);
				$coo_infobox_messages_description->set_data_value('languages_id', $c_languages_id);
				$coo_infobox_messages_description->set_data_value('headline', $c_headline);
				$coo_infobox_messages_description->set_data_value('message', $c_message);
				$coo_infobox_messages_description->set_data_value('button_label', $c_button_label);

				$coo_infobox_messages_description->save_body_data();
			}
		}

		return $t_infobox_message_id;
	}


	function set_status($p_infobox_messages_id, $p_status)
	{
		$c_infobox_messages_id = (int)$p_infobox_messages_id;
		$c_status = gm_prepare_string($p_status, true);

		$coo_infobox_messages = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES, array('infobox_messages_id' => $c_infobox_messages_id)));
		$coo_infobox_messages->set_data_value('status', $c_status);
		$coo_infobox_messages->set_data_value('date_modified', date('Y-m-d H:i:s'));
		$coo_infobox_messages->save_body_data();

		return true;
	}


	function delete($p_infobox_messages_id)
	{
		$c_infobox_messages_id = (int)$p_infobox_messages_id;

		$coo_infobox_messages = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES, array('infobox_messages_id' => $c_infobox_messages_id)));
		$coo_infobox_messages->delete();

		$coo_infobox_messages = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES_DESCRIPTION, array('infobox_messages_id' => $c_infobox_messages_id)));
		$coo_infobox_messages->delete();

		return true;
	}


	function delete_by_identifier($p_identifier, $p_ignore_customers_id = false)
	{
		$c_identifier = gm_prepare_string($p_identifier, true);

		if($p_ignore_customers_id === true)
		{
			$coo_infobox_messages = MainFactory::create_object('GMDataObjectGroup', array(TABLE_INFOBOX_MESSAGES, array('identifier' => $c_identifier)));
			$t_messages_array = $coo_infobox_messages->get_data_objects_array();

			foreach($t_messages_array AS $t_message_object)
			{
				$this->delete($t_message_object->get_data_value('infobox_messages_id'));
			}
		}
		else
		{
			$coo_infobox_messages = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES, array('identifier' => $c_identifier, 'customers_id' => $_SESSION['customer_id'])));
			$t_infobox_messages_id = (int)$coo_infobox_messages->get_data_value('infobox_messages_id');
			$this->delete($t_infobox_messages_id);

			$coo_infobox_messages = MainFactory::create_object('GMDataObject', array(TABLE_INFOBOX_MESSAGES, array('identifier' => $c_identifier, 'customers_id' => 0)));
			$t_infobox_messages_id = (int)$coo_infobox_messages->get_data_value('infobox_messages_id');
			$this->delete($t_infobox_messages_id);
		}

		return true;
	}


	function delete_by_source($p_identifier_to_exclude_array, $p_source)
	{
		$c_source = gm_prepare_string((string)$p_source, true);

		if(is_array($p_identifier_to_exclude_array) && !empty($c_source))
		{
			$coo_infobox_messages = MainFactory::create_object('GMDataObjectGroup', array(TABLE_INFOBOX_MESSAGES, array('source' => $c_source, 'customers_id' => $_SESSION['customer_id'])));
			$t_messages_array = $coo_infobox_messages->get_data_objects_array();

			foreach($t_messages_array AS $t_message_object)
			{
				if(in_array($t_message_object->get_data_value('identifier'), $p_identifier_to_exclude_array) === false)
				{
					$this->delete($t_message_object->get_data_value('infobox_messages_id'));
				}
			}
		}
	}


	function get_all_messages()
	{
		$t_messages_array = array();

		$t_sql = "SELECT
						i.infobox_messages_id,
						i.identifier,
						i.status,
						i.type,
						i.visibility,
						i.button_link,
						i.customers_id,
						i.date_added,
						id.headline,
						id.message,
						id.button_label
					FROM
						infobox_messages i,
						infobox_messages_description id
					WHERE
						i.infobox_messages_id = id.infobox_messages_id AND
						id.languages_id = '" . (int)$_SESSION['languages_id'] . "' AND
						(i.customers_id = '" . (int)$_SESSION['customer_id'] . "' OR i.customers_id = 0) AND
						i.status != 'deleted'
					ORDER BY i.date_added DESC";
		$t_result = xtc_db_query($t_sql);
		while($_result_array = xtc_db_fetch_array($t_result))
		{
			$_result_array['ajax'] = 0;

			if(strpos($_result_array['button_link'], 'request_port.php') !== false)
			{
				$_result_array['ajax'] = 1;
			}

			$t_messages_array[] = $_result_array;
		}

		return $t_messages_array;
	}


	function reactivate_messages()
	{
		$t_messages_array = array();

		$t_limit = date('Y-m-d H:s:i', time() - $this->get_reactivation_time_limit());

		// use only PHP-time to ensure consistent time settings
		$t_sql = "UPDATE infobox_messages SET status = 'new', date_modified = '" . date('Y-m-d H:s:i') . "' WHERE ((date_added < '" . $t_limit . "' AND date_modified = '1000-01-01 00:00:00') || (date_modified < '" . $t_limit . "' AND date_modified != '1000-01-01 00:00:00')) AND status = 'hidden'";
		$t_result = xtc_db_query($t_sql);

		return true;
	}


	function get_reactivation_time_limit()
	{
		return (int)$this->v_reactivation_time_limit;
	}
}