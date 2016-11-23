<?php
/* --------------------------------------------------------------
   LanguageSwitcher.inc.php 2014-11-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LanguageSwitcher
 */
class LanguageSwitcher
{
	protected $languagesArray = array();
	
	public function __construct()
	{
		$this->setLanguages();
	}

	protected function setLanguages()
	{
		$mysqlResult = xtc_db_query($this->_getQuery());

		$this->languagesArray = $this->_createLanguagesArrayByResultSet($mysqlResult);
	}
	/**
	 * @return languagesArray
	 */
	public function getLanguages()
	{
		return $this->languagesArray;
	}


	/**
	 * @param $p_mysqlResult
	 *
	 * @return languagesArray
	 * @throws UnexpectedValueException
	 */
	protected function _createLanguagesArrayByResultSet($p_mysqlResult)
	{
		$languagesArray = array();
		if($p_mysqlResult !== false)
		{
			while($row = xtc_db_fetch_array($p_mysqlResult))
			{
				$languagesArray[] = $row;
			}
		}
		else
		{
			throw new UnexpectedValueException('$p_mysqlResult is not a valid mysql resource');
		}

		return $languagesArray;
	}


	/**
	 * @return string
	 */
	protected function _getQuery()
	{
		$query = 'SELECT 
						*
					FROM
						languages
					ORDER BY 
						languages_id DESC';

		return $query;
	}
} 