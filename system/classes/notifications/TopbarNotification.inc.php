<?php
/* --------------------------------------------------------------
   TopbarNotification.inc.php 2014-10-06 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------*/

/**
 * Class TopbarNotification
 */
class TopbarNotification extends AbstractNotification
{
	protected $id;
	protected $active;
	protected $contentArray;
	protected $color;
	protected $mode;


	/**
	 * @param int    $p_Id
	 * @param bool   $p_active
	 * @param array  $contentArray
	 * @param string $p_color
	 * @param string $p_mode
	 */
	public function __construct($p_Id = 0, $p_active = false, array $contentArray = array(), $p_color = 'transparent', $p_mode = '')
	{
		$this->id = $this->setId($p_Id);
		$this->active = $this->setActive($p_active);
		$this->contentArray = $this->setContentArray($contentArray);
		$this->color = $this->setColor($p_color);
		$this->mode = $this->setMode($p_mode);
	}	
	
	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param $p_Id
	 */
	public function setId($p_Id)
	{
		$this->id = (int)$p_Id;
	}


	/**
	 * @return bool
	 */
	public function isActive()
	{
		return $this->active;
	}


	/**
	 * @param $p_active
	 */
	public function setActive($p_active)
	{
		$this->active = (bool)$p_active;
	}


	/**
	 * @return array
	 */
	public function getContentArray()
	{
		return $this->contentArray;
	}


	/**
	 * @param array $content_array
	 */
	public function setContentArray(array $content_array)
	{
		$this->contentArray = $content_array;
	}


	/**
	 * @param $p_languageId
	 *
	 * @return string|bool
	 */
	public function getContentByLanguageId($p_languageId)
	{
		if(isset($this->contentArray[$p_languageId]))
		{
			return $this->contentArray[$p_languageId];
		}
		
		return false;
	}


	/**
	 * @param $p_content
	 * @param $p_languageId
	 */
	public function setContentByLanguageId($p_content, $p_languageId)
	{
		$this->contentArray[(int)$p_languageId] = (string)$p_content;
	}


	/**
	 * @return string
	 */
	public function getColor()
	{
		return $this->color;
	}


	/**
	 * @param $p_color
	 */
	public function setColor($p_color)
	{
		$this->color = (string)$p_color;
	}


	/**
	 * @return string
	 */
	public function getMode()
	{
		return $this->mode;
	}


	/**
	 * @param $p_mode
	 */
	public function setMode($p_mode)
	{
		$this->mode = (string)$p_mode;
	}
}