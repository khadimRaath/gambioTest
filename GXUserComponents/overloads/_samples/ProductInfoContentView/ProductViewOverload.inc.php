<?php
/* --------------------------------------------------------------
   ProductViewOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductViewOverload
 *
 * This sample demonstrates the overloading of the ProductInfoContentView class. After enabling this class navigate
 * to a product details page in shop frontend. The quantity units description will be slightly different.
 *
 * @see ProductInfoContentView
 */
class ProductViewOverload extends ProductViewOverload_parent
{
	/**
	 * Overloaded constructor of the product info content view.
	 *
	 * @param string $template
	 */
	public function __construct($template = 'default')
	{
		parent::__construct($template);
		
		$style = 'text-align: center;padding: 25px;margin: 50px  35px;background: #D9EDF7;color: #3187CC;';
		
		echo '
			<div style="' . $style . '">
				<h4>Product Info Content View Overload is used!</h4>
				<p>
					This overload will display custom quantity units description.   
				</p>
			</div>
		';
	}
	
	
	/**
	 * Overloaded "_assignQuantity" method.
	 *
	 * Assign a custom quantity unit text.
	 */
	public function _assignQuantity()
	{
		parent::_assignQuantity();
		$this->set_content_data('PRODUCTS_QUANTITY_UNIT',
		                        '<strong style="color:red">Custom Quantity Unit (Displayed over quantity selection)</strong>');
	}
}
