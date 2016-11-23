<?php
/* --------------------------------------------------------------
   MoneyOrderOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class MoneyOrderOverload
 *
 * This sample overload class demonstrates various modifications in the "moneyorder" module.
 */
class MoneyOrderOverload extends MoneyOrderOverload_parent
{
	/**
	 * Overloaded moneyorder constructor.
	 *
	 * Extends the moneyorder title.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->title = 'Extended moneyorder title ' . MODULE_PAYMENT_MONEYORDER_TEXT_TITLE;
	}
	
	
	/**
	 * Overloaded "javascript_validation" method.
	 *
	 * Enables js validation.
	 *
	 * @return true
	 */
	public function javascript_validation()
	{
		return true;
	}
	
	
	/**
	 * Overloaded "selection" method.
	 *
	 * Extends the selection array.
	 *
	 * @return array
	 */
	public function selection()
	{
		$selectionArray                   = parent::selection();
		$selectionArray['additionalInfo'] = 'This is an additional information for the selection array!';
		
		return $selectionArray;
	}
	
	
	/**
	 * Overloaded "pre_confirmation_check" method.
	 *
	 * Enables the pre confirmation check.
	 *
	 * @return bool
	 */
	public function pre_confirmation_check()
	{
		return true;
	}
	
	
	/**
	 * Overloaded "confirmation" method.
	 *
	 * Extends the confirmation array.
	 *
	 * @return array
	 */
	public function confirmation()
	{
		$confirmationArray                   = parent::confirmation();
		$confirmationArray['additionalInfo'] = 'This is an additional information for the confirmation array!';
		
		return $confirmationArray;
	}
	
	
	/**
	 * Overloaded "process_button" method.
	 *
	 * Enables the process button.
	 *
	 * @return bool
	 */
	public function process_button()
	{
		return true;
	}
	
	
	/**
	 * Overloaded "before_process" method.
	 *
	 * Enables the "before process" functionality.
	 *
	 * @return bool
	 */
	public function before_process()
	{
		return true;
	}
}
