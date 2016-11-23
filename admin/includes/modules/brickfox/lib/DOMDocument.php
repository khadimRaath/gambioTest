<?php

/* --------------------------------------------------------------
  $Id: DOMDocument.php 0.1 2011-07-16 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2011 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class Brickfox_Lib_DOMDocument extends DOMDocument
{
	/**
	 * createElement()
	 *
	 * Create a DOMNode
	 * Encode to UTF-8
	 * Replace XML-invalid chars
	 * Check for invalid strings
	 *
	 * @param String $name Name of the Node
	 * @param String $value Value to attach to the Node
	 * @return DOMNode
	 */
	public function createElement($name, $value = null)
	{
		if ($value) {
			$value = utf8_encode($value);
			$value = str_replace('&', '&amp;', $value);
			$value = str_replace('<', '&lt;', $value);
			$value = str_replace('>', '&gt;', $value);
			if (!@simplexml_load_string('<data>' . $value . '</data>')) {
				$value = '';
			}
		}
		$domElement = new Brickfox_Lib_DOMElement($name, $value);
		$documentFragment = $this->createDocumentFragment();
		$documentFragment->appendChild($domElement);
		return $documentFragment->removeChild($domElement);
	}

	/**
	 * createElement()
	 *
	 * Create a DOMCDATASection
	 * Encode to UTF-8
	 *
	 * @param String $name Name of the Node
	 * @return DOMCDATASection
	 */
	public function createCDATASection($data)
	{
		$data = utf8_encode($data);
		return parent::createCDATASection($data);
	}
}

?>