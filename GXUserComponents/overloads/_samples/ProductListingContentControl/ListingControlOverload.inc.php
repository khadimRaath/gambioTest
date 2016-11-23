<?php
/* --------------------------------------------------------------
   ListingControlOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ListingControlOverload
 *
 * This sample demonstrates the overloading of ProductListingContentControl class. After enabling this class head to
 * the product listing of a category where the "tiled" mode is selected instead of the "listing" mode by default.
 *
 * @see ProductListingContentControl
 */
class ListingControlOverload extends ListingControlOverload_parent
{
	/**
	 * Overloaded Class Constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$style = 'text-align: center;padding: 25px;margin: 50px  35px;background: #D9EDF7;color: #3187CC;';
		
		echo '
			<div style="' . $style . '">
				<h4>Product Listing Content Control Overload is used!</h4>
				<p>
					This overload will always set the default display mode to "tiled".   
				</p>
			</div>
		';
	}
	
	/**
	 * Overloaded "determine_view_mode" method.
	 *
	 * This method changes the default view mode to "tiled".
	 *
	 * @param int $viewModeTiles
	 *
	 * @return string
	 */
	public function determine_view_mode($viewModeTiles)
	{
		parent::determine_view_mode($viewModeTiles);
		
		return 'tiled';
	}
}
