<?php
/* --------------------------------------------------------------
   breadcrumb.php 2014-11-11 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(breadcrumb.php,v 1.3 2003/02/11); www.oscommerce.com
   (c) 2003	 nextcommerce (breadcrumb.php,v 1.5 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: breadcrumb.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------*/

/**
 * Class breadcrumb
 */
class breadcrumb_ORIGIN
{
	public $_trail;

	public function __construct()
	{
		$this->reset();
	}

	
	public function reset()
	{
		$this->_trail = array();
	}

	
	public function add($p_title, $p_link = '')
	{
		$this->_trail[] = array('title' => $p_title, 'link' => $p_link);
	}


	/**
	 * @param string $p_separator
	 *
	 * @return string
	 */
	public function trail($p_separator = ' - ')
	{
		/* @var GoogleRichSnippetContentView $richSnippetView */
		$richSnippetView = MainFactory::create_object('GoogleRichSnippetContentView');
		$richSnippetView->set_breadcrumb_array($this->_trail);
		$richSnippetView->set_breadcrumb_separator($p_separator);
		$trailString = $richSnippetView->get_breadcrumb_snippet();
		
		return $trailString;
	}
}