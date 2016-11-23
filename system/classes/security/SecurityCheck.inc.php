<?php
/* --------------------------------------------------------------
   SecurityCheck.inc.php 24.08.16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SecurityCheck
 */
class SecurityCheck
{
	/**
	 * @var array
	 */
	protected static $chmodList = array();

	/**
	 * @var array
	 */
	protected static $chmodRecursiveList = array();

	/**
	 * @var array
	 */
	protected static $writable = array();

	/**
	 * @var array
	 */
	protected static $nonWritable = array();


	/**
	 * @var array
	 */
	protected static $ignoredPaths = array(
		'admin/includes/magnalister',
		'shopgate'
	);


	/**
	 * Returns an array which contains wrong permitted file paths as elements.
	 * The array is prepared for the updater logic.
	 *
	 * @return array
	 */
	public static function getWrongPermittedUpdaterFiles()
	{
		$wrongPermittedFiles = self::getWrongPermittedInstallerFiles(true);
		$updaterChmodArray   = array();

		foreach($wrongPermittedFiles as $file)
		{
			$updaterChmodArray[] = array('PATH' => $file, 'IS_DIR' => is_dir($file));
		}

		return $updaterChmodArray;
	}


	/**
	 * Returns an array which contains wrong permitted file paths as elements.
	 * The array us prepared for the installer logic.
	 *
	 * @param bool $ignoreConfigureFiles Ignore the includes/configure.php and includes/configure.org.php files.
	 *
	 * @return array
	 */
	public static function getWrongPermittedInstallerFiles($ignoreConfigureFiles = false)
	{
		self::_prepareChmodLists();
		$completeList        = array_merge(self::$chmodList, self::$chmodRecursiveList);
		$wrongPermittedFiles = array();

		$configure    = 'includes' . DIRECTORY_SEPARATOR . 'configure.php';
		$configureOrg = 'includes' . DIRECTORY_SEPARATOR . 'configure.org.php';

		foreach($completeList as $pathReference)
		{
			$path = DIR_FS_CATALOG . $pathReference;
			if(!self::_endWith($path, '.gitignore'))
			{
				if($ignoreConfigureFiles
				   && (self::_endWith($pathReference, $configure)
				       || self::_endWith($pathReference, $configureOrg))
				)
				{
					continue;
				}

				@chmod($path, 0777);
				if(@!is_writable($path) && (is_file($path) || is_dir($path)))
				{
					$wrongPermittedFiles[] = $path;
				}
			}
		}

		return $wrongPermittedFiles;
	}


	/**
	 * Checks invalid file/directory permissions. Adds a message to the message stack if
	 * the non writable list contains writable files.
	 *
	 * @param messageStack $messageStack
	 */
	public static function checkNonWritableList(messageStack $messageStack)
	{
		self::_prepareInvalidPermissions(true);

		if(count(self::$nonWritable) > 0)
		{
			$message = '<br/>' . implode('<br/>', self::$nonWritable);
			$messageStack->add(TEXT_FILE_WARNING . '<b>' . $message . '</b>', 'error');
		}
	}


	/**
	 * Checks invalid file/directory permissions. Adds a message to the message stack if
	 * the writable list contains non writable files.
	 *
	 * @param messageStack $messageStack
	 */
	public static function checkWritableList(messageStack $messageStack)
	{
		self::_prepareInvalidPermissions(true);

		if(count(self::$writable) > 0)
		{
			$message = '<br/>' . implode('<br/>', self::$writable);
			$messageStack->add(TEXT_FOLDER_WARNING . '<b>' . $message . '</b>', 'error');
		}
	}


	/**
	 * Prepares the chmod lists.
	 *
	 * @param bool $excludeIgnoredPaths If true, paths from the self::$ignoredPaths property will be removed.
	 */
	protected static function _prepareChmodLists($excludeIgnoredPaths = false)
	{
		self::_prepareChmodList();
		self::_prepareChmodRecursiveList($excludeIgnoredPaths);
	}


	/**
	 * Prepares the chmod list, if not already done.
	 */
	protected static function _prepareChmodList()
	{
		if(count(self::$chmodList) === 0)
		{
			self::$chmodList = array_map(array(__CLASS__, '_trimLeftSlash'),
			                             file(DIR_FS_CATALOG . 'version_info/lists/chmod.txt'));
		}
	}


	/**
	 * Checks if the passed argument is in the ignored paths property.
	 *
	 * @param $element
	 *
	 * @return bool
	 */
	protected static function _isPathIgnored($element)
	{
		return !in_array($element, self::$ignoredPaths);
	}


	/**
	 * Prepares the chmod recursive list, if not already done.
	 * Scans the directories which are listed recursively.
	 *
	 * @param bool $excludeIgnoredPaths If true, paths from the self::$ignoredPaths property will be removed.
	 */
	protected static function _prepareChmodRecursiveList($excludeIgnoredPaths = false)
	{
		if(count(self::$chmodRecursiveList) === 0)
		{
			$recursivePath = DIR_FS_CATALOG . 'version_info/lists/chmod_all.txt';
			$recursiveList = array_map(array(__CLASS__, '_trimLeftSlash'), file($recursivePath));

			$recursiveList = array_filter($recursiveList, array(__CLASS__, '_isPathIgnored'));
			foreach($recursiveList as $listItem)
			{
				if(is_dir(DIR_FS_CATALOG . $listItem) || is_file(DIR_FS_CATALOG . $listItem))
				{
					$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(DIR_FS_CATALOG
					                                                                         . $listItem));

					foreach($iterator as $path)
					{
						if($path->isDir() && $path->getFilename() !== '.' && $path->getFilename() !== '..')
						{
							self::$chmodRecursiveList[] = str_replace(DIR_FS_CATALOG, '', (string)$path);
						}
					}
				}
			}

			// handle of excluded paths
			if(!$excludeIgnoredPaths)
			{
				foreach(self::$ignoredPaths as $ignoredPath)
				{
					$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(DIR_FS_CATALOG
					                                                                         . $ignoredPath));

					foreach($iterator as $path)
					{
						/** @var SplFileInfo $path */
						if($path->getFilename() !== '.' && $path->getFilename() !== '..')
						{
							self::$chmodRecursiveList[] = str_replace(DIR_FS_CATALOG, '', (string)$path);
						}
					}
				}
			}
		}
	}


	/**
	 * Prepares the lists which contains information about invalid file permissions.
	 *
	 * @param bool $excludeIgnoredPaths If true, paths from the self::$ignoredPaths property will be removed.
	 */
	protected static function _prepareInvalidPermissions($excludeIgnoredPaths = false)
	{
		self::_prepareChmodLists($excludeIgnoredPaths);

		if(count(self::$writable) === 0 || count(self::$nonWritable) === 0)
		{
			self::$writable    = array();
			self::$nonWritable = array();

			$configure    = 'includes' . DIRECTORY_SEPARATOR . 'configure.php';
			$configureOrg = 'includes' . DIRECTORY_SEPARATOR . 'configure.org.php';

			// handle chmod.txt
			foreach(self::$chmodList as $item)
			{
				$path = DIR_FS_CATALOG . $item;

				// configure files files must be non writable
				if((self::_endWith($item, $configure) || self::_endWith($item, $configureOrg))
				   && is_writable($path)
				)
				{
					self::$nonWritable[] = $path;
				}
				elseif(!self::_endWith($item, $configure) && !self::_endWith($item, $configureOrg)
				       && (is_dir($path)
				           || is_file($path))
				       && !is_writable($path)
				)
				{

					self::$writable[] = $path;
				}
			}

			// handle chmod_all.txt
			foreach(self::$chmodRecursiveList as $item)
			{
				$path = DIR_FS_CATALOG . $item;

				if(!is_writable($path))
				{
					self::$writable[] = $path;
				}
			}
		}
	}


	/**
	 * Checks if the haystack string ends with needle.
	 *
	 * @param string $haystack Input string.
	 * @param string $needle   Expected end of string.
	 *
	 * @return bool
	 */
	protected static function _endWith($haystack, $needle)
	{
		return substr($haystack, -strlen($needle)) === $needle;
	}


	protected static function _trimLeftSlash($element)
	{
		return ltrim(trim($element), '/');
	}
}