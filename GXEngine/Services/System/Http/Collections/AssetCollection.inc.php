<?php
/* --------------------------------------------------------------
   AssetCollection.inc.php 2016-07-27
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractCollection');
MainFactory::load_class('AssetCollectionInterface');

/**
 * Class AssetCollection
 *
 * Handles Asset objects (JavaScript and CSS). Use the getHtml() method
 * to get the HTML output for the collection. The elements will be outputted
 * in the same order they were added in the collection.
 *
 * @category   System
 * @package    Http
 * @subpackage Collections
 */
class AssetCollection extends AbstractCollection implements AssetCollectionInterface
{
	/**
	 * Adds a new asset to the collection.
	 *
	 * @param AssetInterface $asset
	 */
	public function add(AssetInterface $asset)
	{
		$this->_add($asset);
	}
	
	
	/**
	 * Prints the HTML markup for the assets.
	 *
	 * @param StringType $type (optional) You can provide either Asset::JAVASCRIPT, Asset::CSS or Asset::TRANSLATION in
	 *                         order to get the HTML only for a specific type of assets.
	 *
	 * @return string Returns the HTML markup of the assets.
	 */
	public function getHtml(StringType $type = null)
	{
		$html = '';
		foreach($this->collectionContentArray as $asset)
		{
			if(!empty($type) && $asset->getType() !== $type->asString())
			{
				continue;
			}
			$html .= (string)$asset . PHP_EOL;
		}
		
		return $html;
	}
	
	
	/**
	 * Get all the translation assets as one array. 
	 * 
	 * This array can then be used in frontend from JavaScript (JS Engine or plain scripts).  
	 * 
	 * @return array
	 */
	public function getTranslations()
	{
		$translations = array(); 
		foreach($this->collectionContentArray as $asset)
		{
			if ($asset->getType() !== Asset::TRANSLATION)
			{
				continue; 
			}
			
			$section                = basename($asset->getPath());
			$section                = substr($section, 0, strpos($section, '.'));
			$languageTextManager    = MainFactory::create('LanguageTextManager', $section, $_SESSION['languages_id']);
			$translations[$section] = $languageTextManager->get_section_array($section);
		}
		return $translations;
	}
	
	
	/**
	 * Returns the type of the collection items.
	 *
	 * @return string
	 */
	protected function _getValidType()
	{
		return 'AssetInterface';
	}
}