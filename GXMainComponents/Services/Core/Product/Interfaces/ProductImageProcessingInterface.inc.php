<?php

/* --------------------------------------------------------------
   ProductImageProcessingInterface.inc.php 2015-12-09
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface ProductImageProcessingInterface
 *
 * @category   System
 * @package    Product
 * @subpackage Interfaces
 */
interface ProductImageProcessingInterface
{
	/**
	 * Proceed Image
	 *
	 * Processes and image for the front end.
	 *
	 * @param FilenameStringType $image Image to proceed.
	 *
	 * @return ProductImageProcessingInterface Same instance for chained method calls.
	 */
	public function proceedImage(FilenameStringType $image);
}