<?php
/*	--------------------------------------------------------------
	lettr_delivery.class.php
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
class Lettr_Delivery extends Lettr_Resource {
	public function __construct(){
	  parent::__construct("api_mailings");
	}

	/**
	 * Verschickt eine Freitext-E-Mail ohne Template
	 * 
	 * Attribute der E-Mail sind:
	 *  array(
	 *    "delivery[recipient]"   => "E-Mail-Adresse des Empfängers",
	 *    "delivery[subject]"     => "Betreff der E-Mail",
	 *    "delivery[text]"        => "Freitext der E-Mail (Body) als text/plain",
	 *    "delivery[html]"        => "Freitext der E-Mail (Body) als text/html"
	 *    "files[dateiname.pdf]"  => "@/tmp/pfad_zur_datei_die_als_attachment_verschickt_werden_soll.pdf"
	 *  )
	 * 
	 * Wenn delivery[text] nicht gesetzt wird, dann muss delivery[html] gesetzt
	 * sein und umgekehrt. Bei Bedarf können beide angegeben werden.
	 * 
	 * @param array $attributes
	 */
	public function deliver_without_template($attributes){
	  Lettr_Validation::presence_of('attributes', Lettr_Client::serialized_params($attributes), array("delivery[recipient]", "delivery[subject]"), array("delivery[text]", "delivery[html]", "files\[(.*)\]"));
	  $clean_identifier = $attributes["delivery"]["subject"];
	  if(!empty($attributes["delivery"]["sender_address"])){
		$clean_identifier .= $attributes["delivery"]["sender_address"];
	  }
	  $identifier = md5($clean_identifier);
	  return $this->customId('post', $identifier, "deliver_by_identifier", $attributes);
	}

	/**
	 * Verschickt eine Freitext-E-Mail ohne Template
	 * 
	 * Attribute der E-Mail sind:
	 *  array(
	 *    "delivery[recipient]"   => "E-Mail-Adresse des Empfängers",
	 *    "delivery[subject]"     => "Betreff der E-Mail",
	 *    [... Weitere Attribute sind die Werte der Platzhalter im Verwendeten Template des Mailing ...]
	 *  )
	 * 
	 * @param integer $mailing_id ID des zu verwendenden Mailing
	 * @param array $attributes Attribute der E-Mail
	 */
	public function deliver_with_template($mailing_id, $attributes){
	  Lettr_Validation::presence_of('attributes', Lettr_Client::serialized_params($attributes), array("delivery[recipient]", "delivery[subject]"));
	  return $this->customId('post', $mailing_id, "deliveries", $attributes);
	}
}