<?php
/* --------------------------------------------------------------
   ColorHelper.php 2016-04-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class ColorHelper
 *
 * @category   System
 * @package    Extensions
 * @subpackage Helpers
 */
class ColorHelper
{
	/**
	 * Retrieves the percentage of the relative luminance (based on the photometric definition of luminance) of a
	 * passed color.
	 *
	 * @param StringType $color Six-digit hexadecimal color definition (e.g: '2196F3').
	 *
	 * @return int Color luminance percentage.
	 * @throws UnexpectedValueException On invalid color definition.
	 */
	public static function getLuminance(StringType $color)
	{
		// Regex to extract the RGB color chunks of a hexadecimal color value.
		$regex = '/^#?([0-9a-fA-F]{2})([0-9a-fA-F]{2})([0-9a-fA-F]{2})$/';

		// Color values chunks (R, G, B) from input argument.
		$chunks = array();

		// Extract color values with regex.
		if(!preg_match($regex, $color->asString(), $chunks))
		{
			throw new UnexpectedValueException('Expected six-digit hexadecimal color definition');
		}

		// Get numeric color values.
		$red   = intval($chunks[1], 16);
		$green = intval($chunks[2], 16);
		$blue  = intval($chunks[3], 16);

		// A reference for the formula can be seen here: http://alienryderflex.com/hsp.html
		$lightness = sqrt(
			pow($red, 2) * .299 +
		    pow($green, 2) * .587 +
		    pow($blue, 2) * .114
		);

		// Return lightness value.
		return $lightness;
	}
}
