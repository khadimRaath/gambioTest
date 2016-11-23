<?php
/* --------------------------------------------------------------
   CategoriesOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class Categories
 *
 * This example overload class demonstrate how to overload the categories_ORIGIN class.
 * 
 * @see categories_ORIGIN
 */
class CategoriesOverload extends CategoriesOverload_parent
{
	public function __construct()
	{
		$GLOBALS['messageStack']->add('
			<h3>ClassOverload</h3>
			<p>
				The <strong>overload_example_categories</strong> is used instead of <strong>categories_ORIGIN</strong> 
				class.
			</p>
			<p>
				Your not able to remove the test category (cID = 1) with this overload.
			</p>
		', 'info');
		
		parent::__construct();
	}
	
	
	/**
	 * Overloaded remove category functionality.
	 *
	 * This sample overloaded method omit to remove the test category (with cID =  1).
	 *
	 * @param int $categoryId
	 */
	public function remove_categories($categoryId)
	{
		if((int)$categoryId === 1)
		{
			return;
		}
		
		parent::remove_categories($categoryId);
	}
	
	
	/**
	 * Overloaded set redirect url functionality.
	 *
	 * This sample omits to set another redirect url than the default set value.
	 *
	 * @param $url
	 */
	public function set_redirect_url($url)
	{
		// Won't do anything ... 
	}
}