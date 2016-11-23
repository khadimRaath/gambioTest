<?php

/* --------------------------------------------------------------
   SecondContentView.inc.php 2016-06-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SecondContentView
 *
 * This example overload class will append the frontend template footer with a custom link that points to Facebook.
 */
class SecondContentView extends SecondContentView_parent
{
	/**
	 * Overloaded "get_html" method.
	 *
	 * Extends the html with a link to facebook.
	 *
	 * @return string
	 */
	public function get_html()
	{
		$html = parent::get_html() . '<br/>';
		
		return $html . $this->_wrapInATag('Facebook', 'http://www.facebook.de');
	}
	
	
	/**
	 * Overloaded "build_html" method.
	 *
	 * Extends the html with a link to facebook.
	 *
	 * @return string
	 */
	public function build_html()
	{
		$html = parent::build_html() . '<br/>';
		
		return $html . $this->_wrapInATag('Facebook', 'http://www.facebook.de');
	}
	
	
	/**
	 * Wraps the argument in an a-html tag.
	 *
	 * @param string $value Inner html value.
	 * @param string $link  Href link.
	 *
	 * @return string
	 */
	private function _wrapInATag($value, $link)
	{
		return '<a href="' . $link . '">' . $value . '</a>';
	}
}
