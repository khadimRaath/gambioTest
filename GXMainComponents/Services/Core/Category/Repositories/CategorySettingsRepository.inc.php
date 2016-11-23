<?php

/* --------------------------------------------------------------
   CategorySettingsRepository.php 2015-11-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class CategorySettingsRepository
 *
 * This class handles the database operations that concern settings regarding display and visibility mode of category
 * related data of the database. It provides a layer for more complicated methods that use the writer, reader and
 * deleter.
 *
 * @category   System
 * @package    Category
 * @subpackage Repositories
 */
class CategorySettingsRepository implements CategorySettingsRepositoryInterface
{

	/**
	 * Category settings repository reader.
	 * 
	 * @var CategorySettingsRepositoryReaderInterface
	 */
	protected $reader;

	/**
	 * Category settings repository writer.
	 * 
	 * @var CategorySettingsRepositoryWriterInterface
	 */
	protected $writer;


	/**
	 * CategorySettingsRepository constructor.
	 *
	 * @param CategorySettingsRepositoryReaderInterface $reader Category settings repository reader.
	 * @param CategorySettingsRepositoryWriterInterface $writer Category settings repository writer
	 */
	public function __construct(CategorySettingsRepositoryReaderInterface $reader,
	                            CategorySettingsRepositoryWriterInterface $writer)
	{
		$this->reader = $reader;
		$this->writer = $writer;
	}


	/**
	 * Stores the category settings.
	 *
	 * @param IdType                    $categoryId Category ID.
	 * @param CategorySettingsInterface $settings   Category settings.
	 *
	 * @return CategorySettingsRepository Same instance for chained method calls. 
	 */
	public function store(IdType $categoryId, CategorySettingsInterface $settings)
	{
		$this->writer->update($categoryId, $settings);

		return $this;
	}


	/**
	 * Returns the category settings based on the given ID.
	 *
	 * @param IdType $categoryId Category ID.
	 *
	 * @return CategorySettingsInterface
	 */
	public function getCategorySettingsById(IdType $categoryId)
	{
		return $this->reader->getById($categoryId);
	}
}