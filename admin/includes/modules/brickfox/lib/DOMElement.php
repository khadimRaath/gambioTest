<?php

/* --------------------------------------------------------------
  $Id: DOMElement.php 0.1 2011-07-16 $

  brickfox Multichannel eCommerce
  http://www.brickfox.de

  Copyright (c) 2011 brickfox by NETFORMIC GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  -------------------------------------------------------------- */

class Brickfox_Lib_DOMElement extends DOMElement
{
	/**
	 * __construct()
	 *
	 * Does nothing but constructing DOMElement
	 *
	 * @param String $name
	 * @param String $value
	 * @param String $uri
	 */
	public function __construct($name, $value='', $uri=null)
	{
		parent::__construct($name, $value, $uri);
	}
}
?>