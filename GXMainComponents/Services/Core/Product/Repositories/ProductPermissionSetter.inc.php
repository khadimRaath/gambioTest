<?php

/* --------------------------------------------------------------
   ProductPermissionSetter.inc.php 2016-01-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductPermissionSetter
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductPermissionSetter implements ProductPermissionSetterInterface
{
	/**
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * ProductPermissionSetter constructor.
	 *
	 * @param CI_DB_query_builder $db
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Sets the Customer Status Permissions for all products which are linked with the provided category ID.
	 *
	 * @param IdType   $categoryId
	 * @param IdType   $customerStatusId
	 * @param BoolType $permitted
	 *
	 * @return ProductPermissionSetterInterface Same instance for chained method calls.
	 */
	public function setProductsPermissionByCategoryId(IdType $categoryId,
	                                                  IdType $customerStatusId,
	                                                  BoolType $permitted)
	{
		$column = 'group_permission_' . $customerStatusId->asInt();
		
		// Sub Query
		$this->db->select('products_id')->from('products_to_categories')->where('categories_id', $categoryId->asInt());
		$subQuery = $this->db->get_compiled_select();
		
		// Main Query
		$this->db->set($column, (int)$permitted->asBool())
		         ->where("products_id IN ($subQuery)", null, false)
		         ->update('products');
		
		return $this;
	}
}