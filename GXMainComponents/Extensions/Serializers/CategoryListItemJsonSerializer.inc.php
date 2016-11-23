<?php

/* --------------------------------------------------------------
   CategoryListItemJsonSerializer.inc.php 2016-01-14
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJsonSerializer');

/**
 * Class CategoryListItemJsonSerializer
 *
 * This class will serialize and deserialize an CategoryListItem entity. It can be used into many
 * places where PHP interacts with external requests such as AJAX or API communication.
 *
 * @category   System
 * @package    Extensions
 * @subpackage Serializers
 */
class CategoryListItemJsonSerializer extends AbstractJsonSerializer
{
	/**
	 * Serialize an CategoryListItem object to a JSON string.
	 *
	 * @param CategoryListItem $object         Object instance to be serialized.
	 * @param bool             $encode         (optional) Whether to json_encode the result of the method (default
	 *                                         true).
	 *
	 * @return string|array Returns the json encoded category list item (string) or an array that can be easily encoded
	 *                      into a JSON string.
	 * @throws InvalidArgumentException If the provided object type is invalid.
	 */
	public function serialize($object, $encode = true)
	{
		if(!is_a($object, 'CategoryListItem'))
		{
			throw new InvalidArgumentException('Invalid argument provided, CategoryListItem object required: '
			                                   . get_class($object));
		}
		
		$categoryListItem = array(
			'id'           => $object->getCategoryId(),
			'parentId'     => $object->getParentId(),
			'isActive'     => $object->isActive(),
			'name'         => $object->getName(),
			'headingTitle' => $object->getHeadingTitle(),
			'description'  => $object->getDescription(),
			'urlKeywords'  => $object->getUrlKeywords(),
			'icon'         => $object->getIcon(),
			'image'        => $object->getImage(),
			'imageAltText' => $object->getImageAltText(),
		);
		
		return ($encode) ? $this->jsonEncode($categoryListItem) : $categoryListItem;
	}
	
	
	/**
	 * Deserialize method is not used by the api.
	 *
	 * @param string $string     JSON string that contains the data of the address.
	 * @param object $baseObject (optional) This parameter is not supported for this serializer because the
	 *                           CategoryListItem does not have any setter methods.
	 *
	 * @throws RuntimeException If the argument is not a string or is empty.
	 */
	public function deserialize($string, $baseObject = null)
	{
		throw new RuntimeException('Method is not implemented');
	}
}