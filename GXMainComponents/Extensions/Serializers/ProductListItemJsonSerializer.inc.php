<?php

/* --------------------------------------------------------------
   ProductListItemJsonSerializer.inc.php 2016-01-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class ProductListItemJsonSerializer
 *
 * This class will serialize and deserialize an ProductListItem entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communications.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class ProductListItemJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize an ProductListItem object to a JSON string.
	 *
	 * @param ProductListItem $object          Object instance to be serialized.
	 * @param bool            $encode          (optional) Whether to json_encode the result of the method (default
	 *                                         true).
	 *
	 * @return string|array Returns the json encoded product list item (string) or an array that can be easily encoded
	 *                      into a JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'ProductListItem'))
		{
			throw new InvalidArgumentException('Invalid argument provided, ProductListItem object required: '
			                                   . get_class($object));
		}
		
		$productListItem = array(
			'id'           => $object->getProductId(),
			'isActive'     => $object->isActive(),
			'name'         => $object->getName(),
			'image'        => $object->getImage(),
			'imageAltText' => $object->getImageAltText(),
			'urlKeywords'  => $object->getUrlKeywords()
		);
		
		return ($encode) ? $this->jsonEncode($productListItem) : $productListItem;
	}
	
	
	/**
	 * Deserialize method is not used by the api.
	 *
	 * @param string $string     JSON string that contains the data of the address.
	 * @param object $baseObject (optional) This parameter is not supported for this serializer because the
	 *                           ProductListItem does not have any setter methods.
	 *
	 * @throws RuntimeException If the argument is not a string or is empty.
	 */
	public function deserialize($string, $baseObject = null)
	{
		throw new RuntimeException('Method is not implemented.');
	}
}