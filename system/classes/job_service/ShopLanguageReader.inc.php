<?php
/* --------------------------------------------------------------
   ShopLanguageReader.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShopLanguageReader
{
	public function getAll()
	{
		$sql               = '
			SELECT *
			FROM languages
		';
		$shopLanguageArray = $this->getArrayBySql($sql);

		return $shopLanguageArray;
	}

	public function getAllActive()
	{
		$sql               = '
			SELECT *
			FROM languages
			WHERE
				status = 1
		';
		$shopLanguageArray = $this->getArrayBySql($sql);

		return $shopLanguageArray;
	}


	protected function getArrayBySql($p_sql)
	{
		$shopLanguageArray = array();
		$result            = xtc_db_query($p_sql);

		while(($row = xtc_db_fetch_array($result)))
		{
			$shopLanguage = MainFactory::create_object('ShopLanguage');
			$shopLanguage->setLanguageId($row['languages_id']);
			$shopLanguage->setLanguageCode($row['code']);
			$shopLanguage->setLanguageName($row['name']);

			$shopLanguageArray[] = $shopLanguage;
		}

		return $shopLanguageArray;
	}
} 