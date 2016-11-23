<?php
/* --------------------------------------------------------------
   ManufacturersSource.inc.php 2014-07-14 tb@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of ManufacturersSource
 */
class ManufacturersSource
{	
	protected $v_data_array = array();
	
	public function ManufacturersSource(  )
	{
		$t_sql = 'SELECT * FROM manufacturers';
		$t_result = xtc_db_query( $t_sql );
		while( $t_row = xtc_db_fetch_array( $t_result ) )
		{
			foreach( $t_row AS $t_shipping_status_key => $t_shipping_status_value )
			{
				$this->v_data_array[ $t_row[ 'manufacturers_id' ] ][ $t_shipping_status_key ] = $t_shipping_status_value;
			}
		}
	}
	
	public function get_manufacturers(  )
	{
		return $this->v_data_array;
	}
	
	public function get_manufacturer( $p_manufacturer_id )
	{
		$c_manufacturer_id = (int)$p_manufacturer_id;
		
		if( $c_manufacturer_id == 0 ) trigger_error( 'get_manufacturer: $p_manufacturer_id is empty', E_USER_ERROR );
		
		$t_return = array();
		if( array_key_exists( $c_manufacturer_id, $this->v_data_array ) )
		{
			$t_return = $this->v_data_array[ $c_manufacturer_id ];
		}
		
		return $t_return;
	}
}