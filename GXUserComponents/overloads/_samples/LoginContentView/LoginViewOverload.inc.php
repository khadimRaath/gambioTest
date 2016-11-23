<?php
/* --------------------------------------------------------------
   LoginViewOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LoginViewOverload
 *
 * This example overload class demonstrates the overloading of the LoginContentView class.
 *
 * @see LoginContentView
 */
class LoginViewOverload extends LoginViewOverload_parent
{
	/**
	 * Overloaded constructor of the login content view.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$style = 'text-align: center;padding: 25px;margin: 50px  35px;background: #D9EDF7;color: #3187CC;';
		
		echo '
			<div style="' . $style . '">
				<h4>Login Content View Overload is used!</h4>
				<p>
					This sample overload will disable the creation of guest accounts. 
				</p>
			</div>
		';
	}
	
	
	/**
	 * Overloaded "prepare_data" method.
	 *
	 * This method will disable the creation of a guest account.
	 */
	public function prepare_data()
	{
		parent::prepare_data();
		$this->content_array['account_option'] = 'account';
	}
}
