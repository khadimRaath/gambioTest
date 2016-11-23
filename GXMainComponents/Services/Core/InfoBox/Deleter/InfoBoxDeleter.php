<?php

/* --------------------------------------------------------------
   InfoBoxDeleter.php 2016-08-29
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class InfoBoxDeleter
 *
 * @category System
 * @package  InfoBox
 */
class InfoBoxDeleter implements InfoBoxDeleterInterface
{

	/**
	 * Database connection.
	 * @var CI_DB_query_builder
	 */
	protected $db;

	/**
	 * Name of the table for the info box messages entries.
	 * @var string
	 */
	protected $infoBoxTable = 'infobox_messages';

	/**
	 * Name of the table for the info box description entries.
	 * @var string
	 */
	protected $infoBoxTableDescription = 'infobox_messages_description';

	/**
	 * Name of the ID column.
	 * @var string
	 */
	protected $id_column = 'infobox_messages_id';
	
	/**
	 * Portal notifications source-type.
	 * @var string
	 */
	protected $portalSourceType = 'portal_info'; 


	/**
	 * InfoBoxDeleter constructor.
	 *
	 * @param CI_DB_query_builder $db Database connection.
	 */
	public function __construct(CI_DB_query_builder $db)
	{
		$this->db = $db;
	}


	/**
	 * Deletes messages by source.
	 *
	 * @param StringType $source Source.
	 *
	 * @NOTE Find a better solution to delete the rows.
	 */
	public function deleteBySource(StringType $source)
	{
		if($source->asString() === $this->portalSourceType) 
		{
			$this->db->update($this->infoBoxTable, ['status' => 'deleted'], ['source' => $source->asString()]); 
		}
		else 
		{
			$query = 'DELETE FROM ' . $this->infoBoxTable . ' , ' . $this->infoBoxTableDescription . ' USING '
			         . $this->infoBoxTable . ',' . $this->infoBoxTableDescription . ' WHERE ' . $this->infoBoxTable . '.'
			         . 'source = "' . $source->asString() . '"' . ' AND ' . $this->infoBoxTable . '.' . $this->id_column
			         . ' = ' . $this->infoBoxTableDescription . '.' . $this->id_column;
			
			$this->db->query($query);		
		}
	}


	/**
	 * Deletes a message by ID.
	 *
	 * @param IdType $id Message ID.
	 */
	public function deleteById(IdType $id)
	{
		$tables = array($this->infoBoxTable, $this->infoBoxTableDescription);
		$where  = array($this->id_column => $id->asInt());
		
		$source = $this->db->get($this->infoBoxTable)->row()->source; 
		
		if($source === $this->portalSourceType) 
		{
			$this->db->update($this->infoBoxTable, ['status' => 'deleted'], $where); 
		}
		else 
		{
			$this->db->delete($tables, $where);
		}
	}


	/**
	 * Deletes a message based  on its identifier
	 *
	 * @param StringType $identifier Message identifier
	 *
	 * @NOTE Find a better solution to delete the rows.
	 */
	public function deleteByIdentifier(StringType $identifier)
	{
		$where = ['identifier' => $identifier->asString()]; 		
		$source = $this->db->get($this->infoBoxTable, $where)->row()->source;
		
		if($source === $this->portalSourceType)
		{
			$this->db->update($this->infoBoxTable, ['status' => 'deleted'], $where);
		}
		else
		{
			$query = 'DELETE FROM ' . $this->infoBoxTable . ' , ' . $this->infoBoxTableDescription . ' USING '
			         . $this->infoBoxTable . ',' . $this->infoBoxTableDescription . ' WHERE ' . $this->infoBoxTable . '.'
			         . 'identifier  = "' . $identifier->asString() . '"' . ' AND ' . $this->infoBoxTable . '.'
			         . $this->id_column . ' = ' . $this->infoBoxTableDescription . '.' . $this->id_column;
			
			$this->db->query($query);
		}
	}
}