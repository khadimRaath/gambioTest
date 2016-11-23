<?php

/* --------------------------------------------------------------
   AdminFavoritesAjaxController.php 2016-07-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


/**
 * Class AdminFavoritesAjaxController
 *
 * This class handles the ajax requests for the favorites menu box from the admin menu.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class AdminFavoritesAjaxController extends AdminHttpViewController
{
	/**
	 * Database connection.
	 *
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * @var string Link key.
	 *
	 * Unique link key of the menu item.
	 */
	protected $linkKey;

	/**
	 * @var int Customer ID.
	 */
	protected $customerId;

	/**
	 * @var string Table name of the favorites table.
	 */
	protected $adminFavoritesTable;


	public function init()
	{
		$this->db         = StaticGXCoreLoader::getDatabaseQueryBuilder();
		$this->customerId = $_SESSION['customer_id'];

		// TODO: Use _getQueryParameter. The method had a bug which wouldn't return the value of the GET parameter.
		// That is why we have to use the global variable. When this is fixed, it should be changed.
		$this->linkKey             = addslashes($_GET['link_key']);
		$this->adminFavoritesTable = 'gm_admin_favorites';
	}


	/**
	 * Callback method
	 *
	 * This method inserts a menu item to the favorites table.
	 *
	 * @throws AuthenticationException If the customer has no admin privileges.
	 *
	 * @throws InvalidArgumentException If the link_key argument from the get request is not valid.
	 *
	 * @return HttpControllerResponse 'success' if deletion was successfully else 'error'
	 */
	public function actionAddMenuItem()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		$successStatus = $this->_addMenuItemToFavorites();

		if($successStatus)
		{
			return  MainFactory::create('HttpControllerResponse', 'success');
		}
		else
		{
			return  MainFactory::create('HttpControllerResponse', 'error');
		}
	}
	
	
	/**
	 * Callback method
	 *
	 * This method deletes a menu item from the favorites table.
	 *
	 * @throws AuthenticationException If the customer has no admin privileges.
	 *
	 * @throws InvalidArgumentException If the link_key argument from the get request is not valid.
	 *
	 * @return HttpControllerResponse 'success' if deletion was successfully else 'error'
	 */
	public function actionRemoveMenuItem()
	{
		if(!$this->_isAdmin())
		{
			throw new AuthenticationException('No admin privileges. Please contact the administrator.');
		}

		$successStatus = $this->_removeMenuItemFromFavorites();

		if($successStatus)
		{
			return  MainFactory::create('HttpControllerResponse', 'success');
		}
		else
		{
			return  MainFactory::create('HttpControllerResponse', 'error');
		}
	}
	

	/**
	 * Callback method for the default action
	 *
	 * @return HttpControllerResponse
	 */
	public function actionDefault()
	{
		return MainFactory::create('HttpControllerResponse', array());
	}


	/**
	 * Check if the customer is the admin.
	 *
	 * @return bool Is the customer the admin?
	 */
	protected function _isAdmin()
	{
		try
		{
			$this->validateCurrentAdminStatus();

			return true;
		}
		catch(LogicException $exception)
		{
			return false;
		}
	}


	/**
	 * Adds an entry to the favorites table.
	 *
	 * @throws InvalidArgumentException If the link key is not valid.
	 *
	 * @return bool true on success, else false will be returned.
	 */
	protected function _addMenuItemToFavorites()
	{
		$conditions = array('customers_id' => $this->customerId, 'link_key' => $this->linkKey);

		try
		{
			// We have to delete a possible item first in order to prevent duplication.
			// We cannot use a REPLACE INTO statement, because the table has no primary key.
			$this->_removeMenuItemFromFavorites();

			$this->db->insert($this->adminFavoritesTable, $conditions);

			// Success
			return true;
		}
		catch(Exception $e)
		{
			// Error
			return false;
		}
	}
	

	/**
	 * Removes an entry from the favorites database table.
	 *
	 * @throws InvalidArgumentException If the link key is not valid.
	 *
	 * @return bool true on success, else false will be returned.
	 */
	protected function _removeMenuItemFromFavorites()
	{
		$conditions = array('link_key' => $this->linkKey, 'customers_id' => $this->customerId);

		try
		{
			$this->db->delete($this->adminFavoritesTable, $conditions);

			// Success
			return true;
		}
		catch(Exception $e)
		{
			// Error
			return false;
		}
	}
}