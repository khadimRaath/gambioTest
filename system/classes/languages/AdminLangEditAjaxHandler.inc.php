<?php
/* --------------------------------------------------------------
   AdminLangEditAjaxHandler.inc.php 2016-03-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

require_once(DIR_FS_CATALOG . 'gm/inc/gm_get_language.inc.php');
require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

/**
 * Class AdminLangEditAjaxHandler
 */
class AdminLangEditAjaxHandler extends AjaxHandler
{
	/**
	 * @param int|null $p_customers_id
	 *
	 * @return bool
	 */
	public function get_permission_status($p_customers_id = null)
	{
		if($_SESSION['customers_status']['customers_status_id'] === '0')
		{
			#admins only
			return true;
		}

		return false;
	}


	/**
	 * @return bool
	 */
	public function proceed()
	{
		$_SESSION['coo_page_token']->is_valid($_POST['page_token']);
		$needle         = xtc_db_input($this->v_data_array['POST']['needle']);
		$languagesArray = gm_get_language();

		$sourceFilterQuery = '';
		if(isset($this->v_data_array['POST']['only_edited']) && $this->v_data_array['POST']['only_edited'] === 'true')
		{
			$sourceFilterQuery = 'source = "language_phrases_edited" AND ';
		}

		$languagesFilterQuery = '';
		if(isset($this->v_data_array['POST']['languages'])
		   && in_array('false', $this->v_data_array['POST']['languages'])
		)
		{
			$languages = array();
			
			foreach($this->v_data_array['POST']['languages'] as $languageId)
			{
				if($languageId !== 'false')
				{
					$languages[] = (int)$languageId;
				}
			}
			
			if(count($languages))
			{
				$languagesFilterQuery = 'language_id IN (' . implode(',', $languages)
				                        . ') AND ';
			}
		}

		switch($this->v_data_array['POST']['action'])
		{
			case 'search':
				$query = 'SELECT 
								*
							FROM 
								language_phrases_cache pc
							WHERE
								' . $sourceFilterQuery . '
								' . $languagesFilterQuery . '
								(pc.phrase_text LIKE "%' . $needle . '%" OR pc.phrase_name LIKE "%' . $needle
				         . '%" OR pc.section_name LIKE "%' . $needle . '%")';

				$result                = $this->_generateOutput($query, $languagesArray);
				$this->v_output_buffer = $result;
				break;

			case 'searchValue':
				$query = 'SELECT 
								*
							FROM 
								language_phrases_cache pc
							WHERE
								' . $sourceFilterQuery . '
								' . $languagesFilterQuery . '
								pc.phrase_text LIKE "%' . $needle . '%"';

				$result                = $this->_generateOutput($query, $languagesArray);
				$this->v_output_buffer = $result;
				break;

			case 'searchPhrase':
				$query = 'SELECT 
								*
							FROM 
								language_phrases_cache pc
							WHERE
								' . $sourceFilterQuery . '
								' . $languagesFilterQuery . '
								pc.phrase_name LIKE "%' . $needle . '%"';

				$result                = $this->_generateOutput($query, $languagesArray);
				$this->v_output_buffer = $result;
				break;

			case 'searchSection':
				$query = 'SELECT 
								*
							FROM 
								language_phrases_cache pc
							WHERE
								' . $sourceFilterQuery . '
								' . $languagesFilterQuery . '
								pc.section_name LIKE "%' . $needle . '%"';

				$result                = $this->_generateOutput($query, $languagesArray);
				$this->v_output_buffer = $result;
				break;

			case 'save_content':

				$result = $this->_saveContent();

				$this->v_output_buffer = $result;

				break;

			case 'reset_content':

				$result = $this->_resetContent();

				$this->v_output_buffer = $result;

				break;

			default:
		}

		$httpCaching = MainFactory::create_object('HTTPCaching');
		$httpCaching->start_gzip();

		return true;
	}


	protected function _generateOutput($p_query, array $languagesArray)
	{
		$phraseCacheBuilder = MainFactory::create_object('PhraseCacheBuilder');
		$priorityArray      = $phraseCacheBuilder->getPriorityArray();

		$dbResult     = xtc_db_query($p_query);
		$outputObject = array('success' => true, 'msg' => '', 'payload' => array());

		$outputObject['payload']['data'] = array();

		if(xtc_db_num_rows($dbResult) > 0)
		{

			while($row = xtc_db_fetch_array($dbResult))
			{
				$prioritySource = 'language_phrases_edited';
				if(strpos($row['source'], 'user_sections') !== false)
				{
					$prioritySource = 'user_sections';
				}
				elseif(strpos($row['source'], 'original_sections') !== false)
				{
					$prioritySource = 'original_sections';
				}

				$tempObject             = array();
				$tempObject['section']  = $row['section_name'];
				$tempObject['name']     = $row['phrase_name'];
				$tempObject['language'] = $languagesArray[$row['language_id']]['name'];
				$tempObject['langId']   = $row['language_id'];
				$tempObject['value']    = $row['phrase_text'];
				$tempObject['source']   = $row['source'];
				$tempObject['editable'] = (array_search($prioritySource, $priorityArray)
				                           > array_search('language_phrases_edited', $priorityArray)) ? false : true;
				$tempObject['edited']   = ($row['source'] === 'language_phrases_edited') ? true : false;

				$outputObject['payload']['data'][] = $tempObject;
			}
			$outputObject['payload']['success'] = true;
			$outputObject['payload']['msg']     = '';
		}
		else
		{
			$outputObject['payload']['success'] = false;
			$outputObject['payload']['msg']     = GM_MESSAGE_NO_RESULT;
		}

		return $this->_jsonEncode($outputObject);
	}


	protected function _saveContent()
	{
		$languageId  = (int)$this->v_data_array['POST']['langid'];
		$sectionName = xtc_db_input(xtc_db_prepare_input($this->v_data_array['POST']['section']));
		$phraseName  = xtc_db_input(xtc_db_prepare_input($this->v_data_array['POST']['phrase']));
		$phraseText  = xtc_db_input(xtc_db_prepare_input($this->v_data_array['POST']['value']));

		$phraseCacheBuilder = MainFactory::create_object('PhraseCacheBuilder');
		$priorities         = $phraseCacheBuilder->getPriorityArray();
		$section            = null;
		$originalSection    = $phraseCacheBuilder->findOriginalSectionByPhraseName($languageId, $sectionName,
		                                                                           $phraseName);
		$userSection        = $phraseCacheBuilder->findUserSectionByPhraseName($languageId, $sectionName, $phraseName);

		foreach($priorities as $source)
		{
			if($source === 'original_sections')
			{
				if($originalSection !== null)
				{
					$section = $originalSection;
				}
			}
			elseif($source === 'user_sections')
			{
				if($userSection !== null)
				{
					$section = $userSection;
				}
			}
			elseif($source === 'language_phrases_edited')
			{
				break;
			}
		}

		$isOriginalText = false;

		if($section !== null)
		{
			$isOriginalText = $section->findPhraseText($phraseName) === $phraseText;
		}

		if($isOriginalText)
		{
			$source = $section->getSourceFilePath();

			$sql = 'UPDATE language_phrases_cache
					SET	
						phrase_text = "' . $phraseText . '",
						source = "' . xtc_db_input($source) . '"
					WHERE language_id = "' . $languageId . '"
						AND section_name = "' . $sectionName . '"
						AND phrase_name = "' . $phraseName . '"
						AND source = "language_phrases_edited"';
			xtc_db_query($sql);

			$sql = 'DELETE FROM
						language_phrases_edited
					WHERE
						language_id = "' . $languageId . '"
						AND section_name = "' . $sectionName . '"
						AND phrase_name = "' . $phraseName . '"';
			xtc_db_query($sql);
		}
		else
		{
			$source = 'language_phrases_edited';

			$sql = 'REPLACE INTO language_phrases_edited
					SET	language_id = "' . $languageId . '",
						section_name = "' . $sectionName . '",
						phrase_name = "' . $phraseName . '",
						phrase_text = "' . $phraseText . '"';
			xtc_db_query($sql);

			$sql = 'REPLACE INTO language_phrases_cache
					SET	language_id = "' . $languageId . '",
						section_name = "' . $sectionName . '",
						phrase_name = "' . $phraseName . '",
						phrase_text = "' . $phraseText . '",
						source = "language_phrases_edited"';
			xtc_db_query($sql);
		}

		$outputContent = array(
			'success' => true,
			'msg'     => '',
			'source'  => $source,
			'edited'  => !$isOriginalText
		);

		return $this->_jsonEncode($outputContent);
	}


	protected function _resetContent()
	{
		$languageId  = (int)$this->v_data_array['POST']['langid'];
		$sectionName = xtc_db_input(xtc_db_prepare_input($this->v_data_array['POST']['section']));
		$phraseName  = xtc_db_input(xtc_db_prepare_input($this->v_data_array['POST']['phrase']));

		$phraseCacheBuilder = MainFactory::create_object('PhraseCacheBuilder');
		$priorities         = $phraseCacheBuilder->getPriorityArray();
		$section            = null;
		$originalSection    = $phraseCacheBuilder->findOriginalSectionByPhraseName($languageId, $sectionName,
		                                                                           $phraseName);
		$userSection        = $phraseCacheBuilder->findUserSectionByPhraseName($languageId, $sectionName, $phraseName);

		foreach($priorities as $source)
		{
			if($source === 'original_sections')
			{
				if($originalSection !== null)
				{
					$section = $originalSection;
				}
			}
			elseif($source === 'user_sections')
			{
				if($userSection !== null)
				{
					$section = $userSection;
				}
			}
			elseif($source === 'language_phrases_edited')
			{
				break;
			}
		}

		if($section !== null)
		{
			$phraseText = $section->findPhraseText($phraseName);

			$source = $section->getSourceFilePath();

			$sql = 'UPDATE language_phrases_cache
					SET	
						phrase_text = "' . xtc_db_input($phraseText) . '",
						source = "' . xtc_db_input($source) . '"
					WHERE language_id = "' . $languageId . '"
						AND section_name = "' . $sectionName . '"
						AND phrase_name = "' . $phraseName . '"
						AND source = "language_phrases_edited"';
			xtc_db_query($sql);

			$sql = 'DELETE FROM
						language_phrases_edited
					WHERE
						language_id = "' . $languageId . '"
						AND section_name = "' . $sectionName . '"
						AND phrase_name = "' . $phraseName . '"';
			xtc_db_query($sql);

			$outputObject['success'] = true;
			$outputObject['msg']     = '';
			$outputObject['value']   = $phraseText;
			$outputObject['source']  = $source;
		}
		else
		{
			$outputObject['success'] = false;
			$outputObject['msg']     = 'original phrase not found';
		}

		$result = $this->_jsonEncode($outputObject);

		return $result;
	}


	/**
	 * @param mixed $value
	 *
	 * @return string
	 */
	protected function _jsonEncode($value)
	{
		if(function_exists('json_encode'))
		{
			return json_encode($value);
		}

		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);

		return $json->encode($value);
	}
}