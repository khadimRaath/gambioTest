<?php
/*	--------------------------------------------------------------
	lettr_recipient.class.php
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2010 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------*/

  /**
   * @package Lettr
   * @subpackage REST_Client
   * @access private
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  class Lettr_Recipient extends Lettr_Resource {
    public function __construct(){
      parent::__construct("recipients");
    }
    
    /**
     * Legt einen Empfänger an.
     * 
     * Attribute des Empfänger sind:
     *  - email: E-Mail-Adresse
     *  - firstname (optional): Vorname
     *  - lastname (optional): Nachname
     *  - gender (optional): Geschlecht, kann "f" (female/weiblich) oder "m" (male/männlich) sein
     *  - birthdate (optional): Geburtsdatum
     * 
     * @param array $attributes
     */
    public function create($attributes){
      return parent::create(array("recipient" => $attributes));
    }
    
    /**
     * Löscht einen Empfänger anhand seiner E-Mail-Adresse.
     * 
     * @param string $email E-Mail-Adresse des Empfängers.
     */
    public function delete_by_email($email) {
      return $this->custom("delete", "destroy_by_email", array("email" => $email));
    }
  }