<?php

/* --------------------------------------------------------------
   ProductSettingsRepository.php 2015-12-07
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ProductSettingsRepository
 *
 * @category   System
 * @package    Product
 * @subpackage Repositories
 */
class ProductSettingsRepository implements ProductSettingsRepositoryInterface
{
	/**
	 * @var ProductSettingsRepositoryReaderInterface
	 */
	protected $reader;

	/**
	 * @var ProductSettingsRepositoryWriterInterface
	 */
	protected $writer;


	/**
	 * Initialize the product settings repository.
	 *
	 * @param ProductSettingsRepositoryReaderInterface $reader Instance to perform db read actions.
	 * @param ProductSettingsRepositoryWriterInterface $writer Instance to perform db write actions.
	 */
	public function __construct(ProductSettingsRepositoryReaderInterface $reader,
	                            ProductSettingsRepositoryWriterInterface $writer)
	{
		$this->reader = $reader;
		$this->writer = $writer;
	}


	/**
	 * Saves product settings in the database by the given id.
	 *
	 * @param IdType                   $productId Id of product entity.
	 * @param ProductSettingsInterface $settings  Settings entity with values to store.
	 *
	 * @return ProductSettingsRepositoryInterface|$this Same instance for chained method calls.
	 */
	public function store(IdType $productId, ProductSettingsInterface $settings)
	{
		$this->writer->update($productId, $settings);

		return $this;
	}


	/**
	 * Returns a product settings by the given product id.
	 *
	 * @param IdType $productId Id of product entity.
	 *
	 * @return ProductSettingsInterface Entity with product settings for the expected product id.
	 */
	public function getProductSettingsById(IdType $productId)
	{
		return $this->reader->getById($productId);
	}
}