<?php

/* --------------------------------------------------------------
   ProductPermissionSetterInterface.inc.php 2016-01-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductPermissionSetterInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductPermissionSetterInterface
{
	/**
	 * Sets the Customer Status Permissions.
	 *
	 * @param IdType   $containerId
	 * @param IdType   $customerStatusId
	 * @param BoolType $permitted
	 *
	 * @return ProductPermissionSetterInterface Same instance for chained method calls.
	 */
	public function setProductsPermissionByCategoryId(IdType $containerId,
	                                                  IdType $customerStatusId,
	                                                  BoolType $permitted);
}