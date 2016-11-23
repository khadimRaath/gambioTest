<?php
/* --------------------------------------------------------------
   GoogleExportAvailabilitySource.inc.php 2014-07-14 tb@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of GoogleAvailabilitySource
 */
class GoogleExportAvailabilitySource
{	
	protected $v_data_array = array();
	
	public function GoogleExportAvailabilitySource(  )
	{
		$t_sql = 'SELECT * FROM google_export_availability';
		$t_result = xtc_db_query( $t_sql );
		while( $t_row = xtc_db_fetch_array( $t_result ) )
		{
			foreach( $t_row AS $t_google_export_availability_key => $t_google_export_availability_value )
			{
				$this->v_data_array[ $t_row[ 'google_export_availability_id' ] ][ $t_google_export_availability_key ] = $t_google_export_availability_value;
			}
		}
	}
	
	public function get_google_export_availabilities(  )
	{
		return $this->v_data_array;
	}
	
	public function get_google_export_availability( $p_google_export_availability_id )
	{
		$c_google_availability_id = (int)$p_google_availability_id;
		
		// if( $c_google_availability_id == 0 ) trigger_error( 'get_google_export_availability: $p_google_export_availability_id is empty', E_USER_ERROR );
		
		$t_return = array();
		if( array_key_exists( $c_google_availability_id, $this->v_data_array ) )
		{
			$t_return = $this->v_data_array[ $c_google_availability_id ];
		}
		
		return $t_return;
	}
}