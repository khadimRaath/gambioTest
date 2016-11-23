<?php

/* --------------------------------------------------------------
   StoredCategory.inc.php 2016-01-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- 
*/

MainFactory::load_class('StoredCategoryInterface');


/**
 * Class StoredCategory
 * 
 * This class extends the Category class and represents a persisted category with an unique ID.
 *
 * @category   System
 * @package    Category
 * @subpackage Entities
 */
class StoredCategory extends Category implements StoredCategoryInterface
{
	/**
	 * Category ID.
	 *
	 * @var int
	 */
	protected $categoryId = 0;
	
	
	/**
	 * Class Constructor
	 *
	 * @param IdType                    $categoryId Category ID.
	 * @param CategorySettingsInterface $settings   Category settings.
	 */
	public function __construct(IdType $categoryId, CategorySettingsInterface $settings)
	{
		parent::__construct($settings);
		
		$this->categoryId = $categoryId->asInt();
	}
	
	
	/**
	 * Gets the ID of the StoredCategory.
	 *
	 * @return int
	 */
	public function getCategoryId()
	{
		return $this->categoryId;
	}
	
	
	/**
	 * Get the addon value container ID.
	 *
	 * @return int
	 */
	public function getAddonValueContainerId()
	{
		return $this->getCategoryId();
	}
	
	
	/**
	 * Sets the parent ID of the category.
	 *
	 * @param IdType $categoryId The parent ID.
	 *
	 * @throws LogicException When the passed id is equal to the category id.
	 * 
	 * @return StoredCategory|$this Same instance for chained method calls.
	 */
	public function setParentId(IdType $categoryId)
	{
		$id = $categoryId->asInt();
		if($this->getCategoryId() === $id)
		{
			throw new LogicException('The parent id can not be equal to the category id "' . $id . '"');
		}
		parent::setParentId($categoryId);
	}
}