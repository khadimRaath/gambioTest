<?php

/* --------------------------------------------------------------
   LanguageProvider.inc.php 2016-05-31
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LanguageProvider
 *
 * @category System
 * @package  Shared
 */
class LanguageProvider implements LanguageProviderInterface
{
	
	/**
	 * Database connection.
	 * @var CI_DB_query_builder
	 */
	protected $db;
	
	
	/**
	 * LanguageProvider constructor.
	 *
	 * @param CI_DB_query_builder $db Database connection.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}
	
	
	/**
	 * Returns the language IDs.
	 *
	 * @throws UnexpectedValueException If no ID has been found.
	 * @throws InvalidArgumentException If ID is not valid.
	 *
	 * @return IdCollection
	 */
	public function getIds()
	{
		// Database query.
		$query = $this->db->select('languages_id')->from('languages');
		
		// Array in which the fetched languages IDs will be pushed as IdType to.
		$fetchedIds = array();
		
		// Iterate over each found row and push ID as IdType to array.
		foreach($query->get()->result_array() as $row)
		{
			$id           = (integer)$row['languages_id'];
			$fetchedIds[] = new IdType($id);
		}
		
		// Throw exception if no ID has been found.
		if(empty($fetchedIds))
		{
			throw new UnexpectedValueException('No language IDs were found in the database');
		}
		
		return MainFactory::create('IdCollection', $fetchedIds);
	}
	
	
	/**
	 * Returns the language codes.
	 *
	 * @throws UnexpectedValueException If no code has been found.
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return KeyValueCollection
	 */
	public function getCodes()
	{
		// Database query.
		$query = $this->db->select('code')->from('languages');
		
		// Array in which the fetched languages codes will be pushed as StringType to.
		$fetchedCodes = array();
		
		// Iterate over each found row and push code as StringType to array.
		foreach($query->get()->result_array() as $row)
		{
			$code           = $row['code'];
			$fetchedCodes[] = new LanguageCode(new StringType($code));
		}
		
		// Throw exception if no code has been found.
		if(empty($fetchedCodes))
		{
			throw new UnexpectedValueException('No language codes were found in the database');
		}
		
		return MainFactory::create('KeyValueCollection', $fetchedCodes);
	}
	
	
	/**
	 * Returns the language code from a specific language, selected by the language ID.
	 *
	 * @param IdType $id Language ID.
	 *
	 * @throws UnexpectedValueException If no code has been found.
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return LanguageCode
	 */
	public function getCodeById(IdType $id)
	{
		// Database query.
		$this->db->select('code')->from('languages')->where('languages_id', $id->asInt());
		
		// Fetch data from database and save.
		$data = $this->db->get()->row_array();
		
		// Throw error if no code has been found.
		if($data === null)
		{
			throw new UnexpectedValueException('No language code has been found');
		}
		
		return new LanguageCode(new StringType($data['code']));
	}
	
	
	/**
	 * Returns the directory from the a specific language, selected by the language ID.
	 *
	 * @param IdType $id Language ID.
	 *
	 * @throws UnexpectedValueException If no directory has been found.
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return string
	 */
	public function getDirectoryById(IdType $id)
	{
		// Database query.
		$this->db->select('directory')->from('languages')->where('languages_id', $id->asInt());
		
		// Fetch data from database and save.
		$data = $this->db->get()->row_array();
		
		// Throw error if no value has been found.
		if($data === null)
		{
			throw new UnexpectedValueException('No language directory has been found');
		}
		
		// Return language directory.
		$directory = $data['directory'];
		
		return $directory;
	}
	
	
	/**
	 * Returns the charset from the a specific language, selected by the language ID.
	 *
	 * @param IdType $id Language ID.
	 *
	 * @throws UnexpectedValueException If no charset has been found.
	 *
	 * @return string
	 */
	public function getCharsetById(IdType $id)
	{
		// Database query.
		$this->db->select('language_charset')->from('languages')->where('languages_id', $id->asInt());
		
		// Fetch data from database and save.
		$data = $this->db->get()->row_array();
		
		// Throw error if no value has been found.
		if($data === null)
		{
			throw new UnexpectedValueException('No language charset has been found');
		}
		
		// Return language charset.
		$charset = $data['language_charset'];
		
		return $charset;
	}
	
	
	/**
	 * Returns the ID from the a specific language, selected by the language code.
	 *
	 * @param LanguageCode $code Language code.
	 *
	 * @throws UnexpectedValueException If no ID has been found.
	 *
	 * @return int
	 */
	public function getIdByCode(LanguageCode $code)
	{
		// Database query.
		$this->db->select('languages_id')->from('languages')->where('code', $code->asString());
		
		// Fetch data from database and save.
		$data = $this->db->get()->row_array();
		
		// Throw error if no value has been found.
		if($data === null)
		{
			throw new UnexpectedValueException('No language ID has been found');
		}
		
		// Return language ID.
		$id = (int)$data['languages_id'];
		
		return $id;
	}
	
	
	/**
	 * Returns the directory from the a specific language, selected by the language code.
	 *
	 * @param LanguageCode $code Language code.
	 *
	 * @throws UnexpectedValueException If no directory has been found.
	 *
	 * @return string
	 */
	public function getDirectoryByCode(LanguageCode $code)
	{
		// Database query.
		$this->db->select('directory')->from('languages')->where('code', $code->asString());
		
		// Fetch data from database and save.
		$data = $this->db->get()->row_array();
		
		// Throw error if no value has been found.
		if($data === null)
		{
			throw new UnexpectedValueException('No language directory has been found');
		}
		
		// Return language directory.
		$directory = $data['directory'];
		
		return $directory;
	}
	
	
	/**
	 * Returns the charset from the a specific language, selected by the language code.
	 *
	 * @param LanguageCode $code Language code.
	 *
	 * @throws UnexpectedValueException If no directory has been found.
	 *
	 * @return string
	 */
	public function getCharsetByCode(LanguageCode $code)
	{
		// Database query.
		$this->db->select('language_charset')->from('languages')->where('code', $code->asString());
		
		// Fetch data from database and save.
		$data = $this->db->get()->row_array();
		
		// Throw error if no value has been found.
		if($data === null)
		{
			throw new UnexpectedValueException('No language charset has been found');
		}
		
		// Return language directory.
		$charset = $data['language_charset'];
		
		return $charset;
	}
	
	
	/**
	 * Returns the active language codes.
	 * 
	 * @throws InvalidArgumentException If code is not valid.
	 *
	 * @return KeyValueCollection
	 */
	public function getActiveCodes()
	{
		// Database query.
		$query = $this->db->select('code')->from('languages')->where('status', 1);
		
		// Array in which the fetched languages codes will be pushed as StringType to.
		$fetchedCodes = array();
		
		// Iterate over each found row and push code as StringType to array.
		foreach($query->get()->result_array() as $row)
		{
			$code           = $row['code'];
			$fetchedCodes[] = new LanguageCode(new StringType($code));
		}
		
		// Throw exception if no active code has been found.
		if(empty($fetchedCodes))
		{
			throw new UnexpectedValueException('No active language codes were found in the database');
		}
		
		return MainFactory::create('KeyValueCollection', $fetchedCodes);
	}


	/**
	 * Returns the icon for a specific language by a given language code.
	 * 
	 * @param LanguageCode $code The given language code
	 * 
	 * @throws UnexpectedValueException If no icon has been found.
	 * 
	 * @return string
	 */
	public function getIconFilenameByCode(LanguageCode $code)
	{
		// Database query.
		$this->db->select('image')->from('languages')->where('code', $code->asString());

		// Fetch data from database and save.
		$data = $this->db->get()->row_array();

		// Throw error if no value has been found.
		if($data === null)
		{
			throw new UnexpectedValueException('No language icon has been found');
		}

		// Return language icon filename.
		$icon = $data['image'];

		return $icon;
	}
	
	
	/**
	 * Returns the default language code.
	 *
	 * @throws InvalidArgumentException If no default code exists.
	 * 
	 * @return string
	 */
	public function getDefaultLanguageCode()
	{
		$result = $this->db->select('configuration_value')
		                   ->from('configuration')
		                   ->where('configuration_key', 'DEFAULT_LANGUAGE')
		                   ->get()
		                   ->row_array();
		
		if($result === null)
		{
			throw new UnexpectedValueException('No default language has been found');
		}
		
		return $result['configuration_value'];
	}
	
	
	/**
	 * Returns the default language ID.
	 *
	 * @throws InvalidArgumentException If no default code exists.
	 *
	 * @return int
	 */
	public function getDefaultLanguageId()
	{
		$result = $this->db->select('languages_id')
		                   ->from('languages')
		                   ->join('configuration', 'languages.code = configuration.configuration_value')
		                   ->where('configuration.configuration_key', 'DEFAULT_LANGUAGE')
		                   ->get()
		                   ->row_array();
		
		if($result === null)
		{
			throw new UnexpectedValueException('No default language has been found');
		}
		
		return (int)$result['languages_id'];
	}
}