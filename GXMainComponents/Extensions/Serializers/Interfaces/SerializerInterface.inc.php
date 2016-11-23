<?php
/* --------------------------------------------------------------
   SerializerInterface.inc.php 2015-04-29 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SerializerInterface
 * 
 * Serializers that implement this interface should parse and encode entities
 * so that they can be used in the shop's APIs.
 *
 * Serialization must follow the "null" approach in order to enhance response clarity.
 * That means that serializers must provide a null value than an empty string or an omitted node.
 * 
 * @category System
 * @package Extensions
 * @subpackage Serializers
 */
interface SerializerInterface 
{
	public function serialize($object, $encode = true); 
	
	public function deserialize($string, $baseObject = null);
}