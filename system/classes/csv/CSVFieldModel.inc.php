<?php
/* --------------------------------------------------------------
   CSVFieldModel.inc.php 2014-07-15 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of CSVFieldModel
 */
class CSVFieldModel extends BaseClass
{	
	public $v_field_id = 0;
	public $v_data_array = array();
	
	public function __construct( $p_field_id = 0 )
	{
		$this->set_field_id($p_field_id);
		if($this->get_field_id() > 0)
		{
			$this->load_data();
		}
	}
	
	
	protected function load_data()
	{
		$this->v_data_array = array();
		
		$t_sql = "SELECT * FROM export_scheme_fields WHERE field_id = '" . $this->get_field_id() . "'";
		$t_query = xtc_db_query($t_sql, 'db_link', false);
		if(xtc_db_num_rows($t_query) == 1)
		{
			$t_query_result = xtc_db_fetch_array($t_query);
			$this->set_data_array($t_query_result);
		}
		
		return true;
	}
	
	
	public function is_collective_field()
	{
		return strpos_wrapper($this->v_data_array['field_content'], '{collective_field||') !== false;
	}
	
	
	public function get_collective_source_names()
	{
		$t_source_names = '';
		
		if($this->is_collective_field())
		{
			$t_collective_content = substr_wrapper($this->v_data_array['field_content'], strpos_wrapper($this->v_data_array['field_content'], '{collective_field||'));
			$t_collective_content = substr_wrapper($t_collective_content, strpos_wrapper($t_collective_content, '||') + 2);
			$t_collective_content = substr_wrapper($t_collective_content, 0, strpos_wrapper($t_collective_content, '||'));
			$t_source_names = $t_collective_content;
		}
		
		return $t_source_names;
	}
	
	
	public function is_source_included($p_source)
	{
		if($this->is_collective_field())
		{
			$t_collective_content = substr_wrapper($this->v_data_array['field_content'], strpos_wrapper($this->v_data_array['field_content'], '{collective_field||'));
			$t_collective_content = substr_wrapper($t_collective_content, strpos_wrapper($t_collective_content, '||') + 2);
			$t_collective_content = substr_wrapper($t_collective_content, strpos_wrapper($t_collective_content, '||') + 2);
			$t_collective_content = substr_wrapper($t_collective_content, 0, strpos_wrapper($t_collective_content, '}'));
			$t_sources = explode(';', $t_collective_content);
			return in_array($p_source, $t_sources);
		}
		
		return false;
	}
	
	
	public function save($p_data_array=array())
	{
		if (empty($p_data_array))
		{
			$p_data_array = $this->v_data_array;
		}
		
		$c_field_id = false;
		
		if(isset($p_data_array['field_id']) && (int)$p_data_array['field_id'] > 0)
		{
			$c_field_id = (int)$p_data_array['field_id'];
		}
		
		// get_last_sort_order
		if( $c_field_id == false )
		{
			$t_result = xtc_db_query( 'SELECT sort_order FROM export_scheme_fields WHERE scheme_id = "' . (int)$p_data_array['scheme_id'] . '" ORDER BY sort_order DESC LIMIT 1' );
			if( xtc_db_num_rows( $t_result ) == 1 )
			{
				$t_row = xtc_db_fetch_array( $t_result );
				$p_data_array[ 'sort_order' ] = $t_row[ 'sort_order' ] + 1;
			}
			else
			{
				$p_data_array[ 'sort_order' ] = 1;
			}
		}
		
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_scheme_fields'));		
		$coo_data_object->set_keys(array('field_id' => $c_field_id));
		unset( $p_data_array['field_id'] );
		
		foreach( $p_data_array AS $t_data_key => $t_data_value )
		{
			$coo_data_object->set_data_value( $t_data_key, str_replace("\'", "'", str_replace('\"', '"', str_replace('\\\\', '\\', str_replace('\\\\\\', '\\',  $t_data_value)))) );
		}

		$t_field_id = $coo_data_object->save_body_data();
		
		if($c_field_id !== false)
		{
			$t_field_id = $c_field_id;
		}		
		
		$this->set_field_id($t_field_id);
		$this->load_data();
		
		return $t_field_id;
	}
	
	
	public function delete()
	{
		$coo_data_object = MainFactory::create_object('GMDataObject', array('export_scheme_fields'));
		$coo_data_object->set_keys(array('field_id' => $this->get_field_id()));
		$coo_data_object->delete();
		
		return true;
	}
	
	
	public function get_field_id()
	{
		return (int)$this->v_field_id;
	}
	
	
	public function set_field_id( $p_field_id )
	{
		$this->v_field_id = (int)$p_field_id;
	}
	
	
	public function set_data_array($p_data_array)
	{
		$this->v_data_array = $p_data_array;
	}
}