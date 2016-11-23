<?php

/* --------------------------------------------------------------
   ParcelService.inc.php 2014-10-06 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class ParcelService
{
	protected $id;

	protected $name;
	protected $default;

	protected $urlArray = array();
	protected $commentArray = array();


	public function __construct($p_id = 0, $p_name = '', array $p_urlArray = array(), array $p_commentArray = array(),
								$p_default = 0)
	{
		$this->setId($p_id);
		$this->setDefault($p_default);
		$this->setName($p_name);
		$this->setCommentArray($p_commentArray);
		$this->setUrlArray($p_urlArray);
	}


	/**
	 * @param array $urlArray
	 */
	public function setUrlArray(array $urlArray)
	{
		$this->urlArray = $urlArray;
	}


	/**
	 * @param mixed $p_languageId
	 * @param mixed $p_url
	 */
	public function setUrlByLanguageId($p_languageId, $p_url)
	{
		$this->urlArray[(int)$p_languageId] = $p_url;
	}


	/**
	 * @return array
	 */
	public function getUrlArray()
	{
		return $this->urlArray;
	}


	/**
	 * @param int $p_languageId
	 *
	 * @return mixed
	 */
	public function getUrlByLanguageId($p_languageId)
	{
		return $this->urlArray[(int)$p_languageId];
	}


	/**
	 * @param mixed $default
	 */
	public function setDefault($default)
	{
		$this->default = (int)$default;
	}


	/**
	 * @return mixed
	 */
	public function getDefault()
	{
		return $this->default;
	}


	/**
	 * @param mixed $id
	 */
	public function setId($id)
	{
		$this->id = (int)$id;
	}


	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}


	/**
	 * @param mixed $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}


	/**
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}


	/**
	 * @param array $commentArray
	 */
	public function setCommentArray(array $commentArray)
	{
		$this->commentArray = $commentArray;
	}

	
	/**
	 * @param mixed $p_languageId
	 * @param mixed $p_comment
	 */
	public function setCommentByLanguageId($p_languageId, $p_comment)
	{
		$this->commentArray[(int)$p_languageId] = $p_comment;
	}


	/**
	 * @return array
	 */
	public function getCommentArray()
	{
		return $this->commentArray;
	}


	/**
	 * @param int $p_languageId
	 *
	 * @return mixed
	 */
	public function getCommentByLanguageId($p_languageId)
	{
		return $this->commentArray[(int)$p_languageId];
	}
}