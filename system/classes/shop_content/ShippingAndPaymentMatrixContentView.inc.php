<?php
/* --------------------------------------------------------------
   ShippingAndPaymentMatrixContentView.inc.php 2014-11-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
 */

class ShippingAndPaymentMatrixContentView extends ContentView
{	
	public function __construct()
    {
		$this->set_content_template('module/shipping_and_payment_matrix.html');
    }

	//No flat assigns for gambio template
	public function init_smarty()
	{
		parent::init_smarty();
		$this->set_flat_assigns(false);
	}
	
    public function prepare_data()
    {
		$coo_language_text_manager = MainFactory::create_object('LanguageTextManager', array('countries', $_SESSION['languages_id']));
		$t_content = array();
		
		$t_query = 'SELECT
						*
					FROM
						shipping_and_payment_matrix
					WHERE
						language_id = ' . $_SESSION['languages_id'];
		$t_result = xtc_db_query($t_query);
		while($t_row = xtc_db_fetch_array($t_result))
		{
			$t_row['country'] = $coo_language_text_manager->get_text(strtoupper($t_row['country_code']));
			$t_content[] = $t_row;
		}
		$this->set_content_data('content', $t_content);
    }
}