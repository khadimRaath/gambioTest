<?php
/* --------------------------------------------------------------
   CategoriesIndex.inc.php 2016-09-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

/**
 * Class CategoriesIndex
 * 
 * Use only PHP 5.2 syntax to remain compatibilty to the gambio updater
 */
class CategoriesIndex
{
	protected $queryValuesPart          = '';
	protected $queryValuesPartMaxLength = 10000;
	
	
	public function build_categories_index($p_productsId = null)
	{
		$wherePart = '';
		if($p_productsId !== null)
		{
			$wherePart = ' WHERE products_id = ' . (int)$p_productsId . ' ';
		}
		
		$productId   = null;
		$categoryIds = array();
		
		$query = 'SELECT categories_id, products_id FROM products_to_categories ' . $wherePart . 'ORDER BY products_id';
		
		$result = xtc_db_query($query);
		
		while($row = xtc_db_fetch_array($result))
		{
			if($productId === null || $productId === $row['products_id'])
			{
				$categoryIds[] = $row['categories_id'];
				$productId     = $row['products_id'];
				continue;
			}
			
			$this->_add_query_values_part($productId, $categoryIds);
			$this->_write_categories_index();
			
			$productId   = $row['products_id'];
			$categoryIds = array($row['categories_id']);
		}
		
		if($productId !== null)
		{
			$this->_add_query_values_part($productId, $categoryIds);
			$this->_write_categories_index(true);
		}
	}
	
	
	public function get_categories_parents_array($p_categoryId)
	{
		static $categoryParents;
		
		$c_categoryId = (int)$p_categoryId;
		
		if($categoryParents === null)
		{
			$categoryParents = array();
		}
		elseif(array_key_exists($c_categoryId, $categoryParents))
		{
			return $categoryParents[$c_categoryId];
		}
		
		$outputArray = array();
		
		if($c_categoryId === 0)
		{
			# categories_id is root and has no parents. return empty array.
			return $outputArray;
		}
		
		# get category's status and parent_id
		$query  = 'SELECT categories_status, parent_id FROM categories WHERE categories_id = ' . $c_categoryId;
		$result = xtc_db_query($query);
		
		$row = xtc_db_fetch_array($result);
		
		if($row['categories_status'] === '0')
		{
			# cancel recursion with false on inactive category
			return false;
		}
		
		$parentId      = $row['parent_id'];
		$outputArray[] = $parentId;
		
		if($parentId !== '0')
		{
			# get more parents, if category is not root
			$parentIds = $this->get_categories_parents_array($parentId);
			if($parentIds === false)
			{
				# cancel recursion with false on inactive category
				return false;
			}
			# merge category's parent tree to categories_id
			$outputArray = array_merge($outputArray, $parentIds);
		}
		
		$categoryParents[$c_categoryId] = $outputArray;
		
		return $outputArray;
	}
	
	
	protected function _add_query_values_part($p_productsId, array $p_categoryIds)
	{
		$categoryIds = array();
		
		foreach($p_categoryIds as $categoryId)
		{
			$t_parent_id_array = $this->get_categories_parents_array($categoryId);
			
			if($t_parent_id_array !== false)
			{
				$categoryIds[] = $categoryId;
				$categoryIds   = array_merge($categoryIds, $t_parent_id_array);
			}
		}
		
		sort($categoryIds); # sort array for cleaning
		$categoryIds = array_unique($categoryIds); # delete doubled categories_ids
		$categoryIds = array_values($categoryIds); # close key gaps after deleting duplicates
		
		# build index string
		$categoriesIndex = '';
		foreach($categoryIds as $categoryId)
		{
			$categoriesIndex .= '-' . (int)$categoryId . '-';
		}
		
		$this->queryValuesPart .= '(' . (int)$p_productsId . ',"' . $categoriesIndex . '"),';
	}
	
	
	protected function _write_categories_index($forceWrite = false)
	{
		if($this->queryValuesPart !== ''
		   && ($forceWrite
		       || strlen($this->queryValuesPart) > $this->queryValuesPartMaxLength)
		)
		{
			$query = 'REPLACE INTO `categories_index` 
							(`products_id`, `categories_index`)
						VALUES
							' . substr($this->queryValuesPart, 0, -1);
			
			# save built index
			xtc_db_query($query);
			
			$this->queryValuesPart = '';
		}
	}
}