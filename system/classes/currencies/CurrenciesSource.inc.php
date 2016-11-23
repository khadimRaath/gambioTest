<?php
/* --------------------------------------------------------------
   CurrenciesSource.inc.php 2014-08-28 tb@gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Description of CurrenciesSource
 */
class CurrenciesSource
{	
	protected $v_data_array = array();
	
	public function CurrenciesSource(  )
	{
		$t_sql = 'SELECT * FROM currencies';
		$t_result = xtc_db_query( $t_sql );
		while( $t_row = xtc_db_fetch_array( $t_result ) )
		{
			foreach( $t_row AS $t_currencies_key => $t_currencies_value )
			{
				$this->v_data_array[ $t_row[ 'currencies_id' ] ][ $t_currencies_key ] = $t_currencies_value;
			}
		}
	}
	
	public function get_currencies( $p_currency_id = 0 )
	{
		$c_currency_id = (int)$p_currency_id;
		
		$t_return = array();
		
		if( $c_currency_id == 0 )
		{
			foreach($this->v_data_array AS $t_currency_data)
			{
				if($t_currency_data['code'] == DEFAULT_CURRENCY)
				{
					$t_return = $t_currency_data;
				}
			}			
		}
		else if( array_key_exists( $c_currency_id, $this->v_data_array ) )
		{
			$t_return = $this->v_data_array[ $c_currency_id ];
		}
		
		return $t_return;
	}
}