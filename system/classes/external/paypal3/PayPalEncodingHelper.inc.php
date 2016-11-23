<?php
/* --------------------------------------------------------------
	PayPalEncodingHelper.inc.php 2015-04-27
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * wrapper class to handle differences in encoding between PayPal and shop system.
 * Only really necessary for backwards compatibility with pre-UTF-8 versions (GX 2.0.x).
 */
class PayPalEncodingHelper
{
	/**
	 * @var bool $transcodingRequired flag indicating whether conversion is necessary
	 */
	protected $transcodingRequired;

	/**
	 * constructor; determines necessity of transcoding from session charset
	 */
	public function __construct()
	{
		if(strpos($_SESSION['language_charset'], 'iso-8859') !== false)
		{
			$this->transcodingRequired = true;
		}
		else
		{
			$this->transcodingRequired = false;
		}
	}

	/**
	 * transcodes a string for transmission to PayPal.
	 * @param string $string string to be converted
	 */
	public function transcodeOutbound($string)
	{
		if(!$this->transcodingRequired)
		{
			$output = $string;
		}
		else
		{
			$output = utf8_encode($string);
			$output = html_entity_decode($output, ENT_COMPAT|ENT_HTML401, 'UTF-8');
		}
		return $output;
	}

	/**
	 * transcodes a string from PayPal into shop system's encoding
	 * @param string $string string to be converted
	 */
	public function transcodeInbound($string)
	{
		if(!$this->transcodingRequired)
		{
			$output = $string;
		}
		else
		{
			$output = utf8_decode($string);
		}
		return $output;
	}
}
