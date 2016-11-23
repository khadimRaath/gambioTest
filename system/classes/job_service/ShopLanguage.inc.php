<?php

/* --------------------------------------------------------------
   ShopLanguage.inc.php 2014-10-01 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ShopLanguage
{
	protected $languageId;
	protected $languageName;
	protected $languageCode;
	protected $image;
	protected $directory;
	protected $sortOrder;
	protected $charset;
	protected $status;


	public function setLanguageId($p_languageId)
	{
		$c_languageId = (int)$p_languageId;
		if($c_languageId < 1)
		{
			throw new Exception('Invalid Id: ' . $p_languageId);
		}
		$this->languageId = $c_languageId;
	}


	public function getLanguageId()
	{
		return $this->languageId;
	}


	public function setLanguageCode($p_languageCode)
	{
		$this->languageCode = $p_languageCode;
	}


	public function getLanguageCode()
	{
		return $this->languageCode;
	}


	public function setLanguageName($p_languageName)
	{
		$this->languageName = $p_languageName;
	}


	public function getLanguageName()
	{
		return $this->languageName;
	}

} 