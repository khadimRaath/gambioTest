<?php

/* --------------------------------------------------------------
   OrderPaymentType.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderPaymentTypeInterface');

/**
 * Class OrderPaymentType
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderPaymentType implements OrderPaymentTypeInterface
{
	/**
	 * Payment type title.
	 *
	 * @var string
	 */
	protected $title = '';
	
	/**
	 * Payment type module.
	 *
	 * @var string
	 */
	protected $module = '';

	/**
	 * Payment type alias.
	 *
	 * @var string
	 */
	protected $alias = '';
	

	/**
	 * OrderPaymentType constructor
	 *
	 * @param StringType $title  Payment type title.
	 * @param StringType $module Payment type module.
	 * @param StringType|null $alias  (Optional) Payment type alias.
	 */
	public function __construct(StringType $title, StringType $module, StringType $alias = null)
	{
		$this->title  = $title->asString();
		$this->module = $module->asString();
		$this->alias  = ($alias) ? $alias->asString() : $title->asString();
	}
	
	
	/**
	 * Returns the order payment type title.
	 *
	 * @return string Order payment type title.
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	
	/**
	 * Returns the order payment type module.
	 *
	 * @return string Order payment type module.
	 */
	public function getModule()
	{
		return $this->module;
	}


	/**
	 * Returns the order payment type alias.
	 *
	 * @return string
	 */
	public function getAlias()
	{
		return $this->alias;
	}
}