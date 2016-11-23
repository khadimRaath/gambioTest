<?php

/* --------------------------------------------------------------
   AdditionalFieldContentView.inc.php 2015-10-23
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   -------------------------------------------------------------- */


class AdditionalFieldContentView extends ContentView
{
    protected $getArray  = array(); // $_GET
	protected $postArray = array(); // $_POST

	protected $languageId = 0;
	protected $additionalFields = array();

    /**
	 * @constructor
	 */
    public function __construct()
	{
		parent::__construct();
		$this->set_content_template('module/additional_fields.html');
		$this->set_flat_assigns(true);
	}

    protected function set_validation_rules()
	{
		$this->validation_rules_array['languageId']		  = array('type' => 'int');
		$this->validation_rules_array['additionalFields'] = array('type' => 'array');
	}

	public function prepare_data()
	{
		// Result array, which will be passed to the template.
		$result = array();

		foreach ($this->additionalFields as $fieldContainer) {
			// Field names.
			$names 	= $fieldContainer->get_name_array();
			$values = array();

			// Field values.
			$fieldValues = $fieldContainer->get_field_value_array();
			if (!empty($fieldValues))
			{
				$values = $fieldValues[0]->get_value_array();
			}

			// Determines whether the field is a multilanguage field.
			$isMultilingual = (boolean) $fieldContainer->get_multilingual();

			// Push field title and value to result array.
			$result[] = array(
				'title' => $names[$this->languageId],
				'value' => $values[$isMultilingual ? $this->languageId : 0]
			);
		}

        // Set content data array only if there are values.
        if (count($result) > 0) {
            $this->set_content_data('additional_fields_data_array', $result);
        }
	}

    ##### SETTER / GETTER #####

	/**
	 * @return array
	 */
	public function getGetArray()
	{
		return $this->getArray;
	}


	/**
	 * $_GET-Data
	 *
	 * @param array $getArray
	 */
	public function setGetArray(array $getArray)
	{
		$this->getArray = $getArray;
	}

	/**
	 * @return array
	 */
	public function getPostArray()
	{
		return $this->postArray;
	}

	/**
	 * $_POST-Data
	 *
	 * @param array $postArray
	 */
	public function setPostArray(array $postArray)
	{
		$this->postArray = $postArray;
	}

    /**
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->languageId;
	}

	/**
	 * @param int $p_languageId
	 */
	public function setLanguageId($p_languageId)
	{
		$this->languageId = (int)$p_languageId;
	}

    /**
     * @return array
     */
    public function getAdditionalFields()
    {
        return $this->additionalFields;
    }

    /**
     * @param array $additionalFields
     */
    public function setAdditionalFields(array $p_additionalFields)
    {
        $this->additionalFields = $p_additionalFields;
    }
}
