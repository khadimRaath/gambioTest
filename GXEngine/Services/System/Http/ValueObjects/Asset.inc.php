<?php
/* --------------------------------------------------------------
   Asset.inc.php 2016-04-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AssetInterface');

/**
 * Class Asset
 *
 * @category   System
 * @package    Http
 * @subpackage ValueObjects
 */
class Asset implements AssetInterface
{
	/**
	 * JavaScript Asset Type
	 */
	const JAVASCRIPT = 'javascript';
	
	/**
	 * CSS Asset Type
	 */
	const CSS = 'css';
	
	/**
	 * JavaScript Translation
	 */
	const TRANSLATION = 'translation';
	
	/**
	 * @var string Asset's relative path.
	 */
	protected $path;
	
	/**
	 * @var string Asset's type (defined by the file extension).
	 */
	protected $type;
	
	
	/**
	 * Initializes the asset.
	 *
	 * @throws InvalidArgumentException
	 *
	 * @param string $path Relative path to the asset file (relative to the "src" directory). Provide only the filename
	 *                     for translations (no path is required - e.g. general.lang.inc.php).
	 */
	public function __construct($path)
	{
		if(!is_string($path) || empty($path))
		{
			throw new InvalidArgumentException('Invalid argument $p_path provided (relative asset path - string expected): '
			                                   . print_r($path, true));
		}
		
		$this->path = (string)$path;
		
		if(substr($this->path, -3) === '.js')
		{
			$this->type = self::JAVASCRIPT;
		}
		else if(substr($this->path, -4) === '.css')
		{
			$this->type = self::CSS;
		}
		else if(substr(basename($this->path), -13) === '.lang.inc.php')
		{
			$this->type = self::TRANSLATION;
		}
		else
		{
			throw new InvalidArgumentException('Provided asset is not supported, provide JavaScript(.js) and CSS (.css) assets.');
		}
	}
	
	
	/**
	 * Get asset HTML markup.
	 *
	 * @return string Returns the HTML markup that will load the file when the page is loaded.
	 */
	public function __toString()
	{
		switch($this->type)
		{
			case self::JAVASCRIPT:
				return '<script type="text/javascript" src="' . $this->path . '"></script>';
				break;
			case self::CSS:
				return '<link rel="stylesheet" type="text/css" href="' . $this->path . '" />';
				break;
			case self::TRANSLATION:
				$section             = str_replace('.lang.inc.php', '', basename($this->path));
				$languageTextManager = MainFactory::create('LanguageTextManager', $section, $_SESSION['languages_id']);
				
				return json_encode($languageTextManager->get_section_array($section));
				break;
			default:
				return ''; // Just in case the asset type was not set correctly.
		}
	}
	
	
	/**
	 * Get the path of the asset.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}
	
	
	/**
	 * Get the type of the asset.
	 *
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}