<?php
/* --------------------------------------------------------------
   PopupNotification.inc.php 2014-10-08 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

/**
 * Class PopupNotification
 */
class PopupNotification extends AbstractNotification
{
	protected $id;
	protected $active;
	protected $contentArray;

	/**
	 * @param int    $p_Id
	 * @param bool   $p_active
	 * @param array  $contentArray
	 */
	public function __construct($p_Id = 0, $p_active = false, array $contentArray = array())
	{
		$this->id = $this->setId($p_Id);
		$this->active = $this->setActive($p_active);
		$this->contentArray = $this->setContentArray($contentArray);
	}


	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param int $p_id
	 */
	public function setId($p_id)
	{
		$this->id = (int)$p_id;
	}


	/**
	 * @return bool
	 */
	public function isActive()
	{
		return $this->active;
	}


	/**
	 * @param bool $p_active
	 */
	public function setActive($p_active)
	{
		$this->active = (boolean)$p_active;
	}


	/**
	 * @return array
	 */
	public function getContentArray()
	{
		return $this->contentArray;
	}


	/**
	 * @param int $p_languageId
	 *
	 * @return string|bool
	 */
	public function getContentByLanguageId($p_languageId)
	{
		if(isset($this->contentArray[(int)$p_languageId]))
		{
			return $this->contentArray[(int)$p_languageId];
		}
		
		return false;
	}


	/**
	 * @param array $p_contentArray
	 */
	public function setContentArray(array $p_contentArray)
	{
		$this->contentArray = $p_contentArray;
	}


	/**
	 * @param $p_content
	 * @param $p_languageId
	 */
	public function setContentByLanguageId($p_content, $p_languageId)
	{
		$this->contentArray[(int)$p_languageId] = $p_content;
	}
}