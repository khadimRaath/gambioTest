<?php
/* --------------------------------------------------------------
   FieldReplaceJob.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJob');
MainFactory::load_class('FieldReplaceJob');

class ProductsFieldReplaceJob extends FieldReplaceJob
{
	public function __construct($p_field = null, $p_oldValue = null, $p_newValue = null, $p_fieldReplaceJobId = null)
	{
		parent::__construct('products', $p_field, $p_oldValue, $p_newValue, $p_fieldReplaceJobId);
	}


	public function execute()
	{
		parent::execute();

		$c_table    = xtc_db_input($this->getTableName());
		$c_field    = xtc_db_input($this->getFieldName());
		$c_oldValue = xtc_db_input($this->getOldValue());
		$c_newValue = xtc_db_input($this->getNewValue());

		if($c_table == 'products' && $c_field == 'products_shippingtime')
		{
			$sql = '
				UPDATE products_properties_combis
				SET combi_shipping_status_id = "' . $c_newValue . '"
				WHERE
					combi_shipping_status_id = "' . $c_oldValue . '"
			';
			xtc_db_query($sql);
		}

		return true;
	}
}
