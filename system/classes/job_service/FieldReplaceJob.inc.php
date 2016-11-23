<?php
/* --------------------------------------------------------------
   FieldReplaceJob.inc.php 2014-10-16 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

MainFactory::load_class('AbstractJob');

/**
 * Class FieldReplaceJob
 */
class FieldReplaceJob extends AbstractJob
{
	protected $fieldReplaceJobId;

	protected $tableName;
	protected $fieldName;

	protected $oldValue;
	protected $newValue;
	
	protected $hidden;

	/**
	 * @param string|null $p_table
	 * @param string|null $p_field
	 * @param string|null $p_oldValue
	 * @param string|null $p_newValue
	 * @param int|null    $p_fieldReplaceJobId
	 * @param bool|null   $p_hidden
	 */
	public function __construct($p_table = null, $p_field = null, $p_oldValue = null, $p_newValue = null,
								$p_fieldReplaceJobId = null, $p_hidden = null)
	{
		$this->fieldReplaceJobId = $p_fieldReplaceJobId;
		$this->waitingNumber     = null;

		$this->tableName = $p_table;
		$this->fieldName = $p_field;

		$this->oldValue = $p_oldValue;
		$this->newValue = $p_newValue;
		
		$this->hidden = $p_hidden;
	}


	/**
	 * @param int $p_fieldReplaceJobId
	 */
	public function setFieldReplaceJobId($p_fieldReplaceJobId)
	{
		$this->fieldReplaceJobId = (int)$p_fieldReplaceJobId;
	}


	/**
	 * @return int
	 */
	public function getFieldReplaceJobId()
	{
		return $this->fieldReplaceJobId;
	}


	/**
	 * @param string $p_table
	 */
	public function setTableName($p_table)
	{
		$this->tableName = (string)$p_table;
	}


	/**
	 * @return string
	 */
	public function getTableName()
	{
		return $this->tableName;
	}


	/**
	 * @param string $p_field
	 */
	public function setFieldName($p_field)
	{
		$this->fieldName = (string)$p_field;
	}


	/**
	 * @return string
	 */
	public function getFieldName()
	{
		return $this->fieldName;
	}


	/**
	 * @param string $p_oldValue
	 */
	public function setOldValue($p_oldValue)
	{
		$this->oldValue = (string)$p_oldValue;
	}


	/**
	 * @return string
	 */
	public function getOldValue()
	{
		return $this->oldValue;
	}


	/**
	 * @param string $p_newValue
	 */
	public function setNewValue($p_newValue)
	{
		$this->newValue = (string)$p_newValue;
	}


	/**
	 * @return string
	 */
	public function getNewValue()
	{
		return $this->newValue;
	}


	/**
	 * @return bool
	 */
	public function getHidden()
	{
		return $this->hidden;
	}


	/**
	 * @param bool $p_hidden
	 */
	public function setHidden($p_hidden)
	{
		$this->hidden = (bool)$p_hidden;
	}


	/**
	 * @return bool
	 */
	public function execute()
	{
		$c_table    = xtc_db_input($this->getTableName());
		$c_field    = xtc_db_input($this->getFieldName());
		$c_oldValue = xtc_db_input($this->getOldValue());
		$c_newValue = xtc_db_input($this->getNewValue());

		$sql = '
			UPDATE ' . $c_table . '
			SET ' . $c_field . ' = "' . $c_newValue . '"
			WHERE
				' . $c_field . ' = "' . $c_oldValue . '"
		';
		mysqli_query($GLOBALS["___mysqli_ston"], $sql);

		return true;
	}
}
