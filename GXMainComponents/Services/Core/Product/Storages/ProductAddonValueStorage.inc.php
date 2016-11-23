<?php

/* --------------------------------------------------------------
   ProductAddonValueStorage.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractAddonValueStorage');

/**
 * Class ProductAddonValueStorage
 *
 * @category   System
 * @package    Product
 * @subpackage Storages
 */
class ProductAddonValueStorage extends AbstractAddonValueStorage
{
	/**
	 * Get Container Type
	 *
	 * Returns the container class type.
	 *
	 * @return string
	 */
	protected function _getContainerType()
	{
		return 'ProductInterface';
	}
	

	/**
	 * Get External Fields Array
	 *
	 * Returns a multidimensional array with the primary key of the products_item_codes table
	 * and the required column names with the corresponding key used in the KeyValueCollection.
	 *
	 * @return array
	 */
	protected function _getExternalFieldsArray()
	{
		$externalFields = array();

		// Product item codes.
		$externalFields['products_item_codes']['primary_key'] = 'products_id';
		$externalFields['products_item_codes']['fields']      = array(
			'code_isbn'                     => 'codeIsbn',
			'code_upc'                      => 'codeUpc',
			'code_mpn'                      => 'codeMpn',
			'code_jan'                      => 'codeJan',
			'google_export_condition'       => 'googleExportCondition',
			'google_export_availability_id' => 'googleExportAvailabilityId',
			'brand_name'                    => 'brandName',
			'identifier_exists'             => 'identifierExists',
			'gender'                        => 'gender',
			'age_group'                     => 'ageGroup',
			'expiration_date'               => 'expirationDate',
		);

		// Product primary image dimensions.
		$externalFields['products']['primary_key'] = 'products_id';
		$externalFields['products']['fields']      = array(
			'products_image_w' => 'productsImageWidth',
			'products_image_h' => 'productsImageHeight',
		);

		return $externalFields;
	}
}