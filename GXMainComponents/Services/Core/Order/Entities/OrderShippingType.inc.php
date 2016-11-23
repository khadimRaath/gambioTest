<?php

/* --------------------------------------------------------------
   OrderShippingType.php 2015-10-27 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('OrderShippingTypeInterface');

/**
 * Class OrderShippingType
 *
 * @category   System
 * @package    Order
 * @subpackage Entities
 */
class OrderShippingType implements OrderShippingTypeInterface
{
	/**
	 * Shipping type title.
	 *
	 * @var string
	 */
	protected $title = '';
	
	/**
	 * Shipping type module.
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
	 * OrderShippingType constructor.
	 *
	 * @param StringType $title  Order shipping type title.
	 * @param StringType $module Order shipping type module.
	 * @param StringType|null $alias  Order shipping type alias.
	 */
	public function __construct(StringType $title, StringType $module, StringType $alias = null)
	{
		$this->title  = $title->asString();
		$this->module = $module->asString();
		$this->alias  = ($alias) ? $alias->asString() : $title->asString();
	}


	/**
	 * Returns the order shipping type title.
	 *
	 * @return string Order shipping type title.
	 */
	public function getTitle()
	{
		return $this->title;
	}
	
	
	/**
	 * Returns the order shipping type module.
	 *
	 * @return string Order shipping type module.
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