<?php

/* --------------------------------------------------------------
   ContentNavigationCollection.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('KeyValueCollection');
MainFactory::load_class('ContentNavigationCollectionInterface');

/**
 * Class ContentNavigationCollection
 *
 * This class extends the KeyValueCollection where the key is the display name of the navigation and the value
 * the URL of the page. If you use the constructor to set the content navigation links make sure that you provide
 * an empty URL for the current page so that it's marked as active in frontend.
 *
 * @category   System
 * @package    Http
 * @subpackage Collections
 */
class ContentNavigationCollection extends KeyValueCollection implements ContentNavigationCollectionInterface
{
	/**
	 * Adds a new page to the collection.
	 *
	 * @param StringType $name    The page name to be displayed must be already translated.
	 * @param StringType $url     The page url to be displayed.
	 * @param BoolType   $current Whether the provided page is the one currently displayed.
	 */
	public function add(StringType $name, StringType $url, BoolType $current)
	{
		$this->collectionContentArray[$name->asString()] = (!$current->asBool()) ? $url->asString() : '';
	}
}