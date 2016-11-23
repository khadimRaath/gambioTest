<?php
/* --------------------------------------------------------------
  GxmlHelper.inc.php 2015-02-23 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2015 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

/**
 * Class GxmlHelper
 * 
 * This class contains helper methods that are used by other classes of the 
 * Gambio API. 
 * 
 * Refactored by A.Tselegidis
 * 
 * @category System
 * @package GambioAPI
 * @version 1.0
 */
class GxmlHelper {
	/**
	 * Get an empty GambioXML document. 
	 * 
	 * @return string
	 */
	public function getBlankXml()
	{
		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><GambioXML/>');
		return $xml->asXML();
	}


	/**
	 * Get sample Gambio API response XML. 
	 * 
	 * @return string
	 */
	public function getXmlSample()
	{		
		$xmlDocument = '<?xml version="1.0"?>
			<GambioXML> 
				<general>
					<function>upload_categories</function>
					<session_key>abcdef0123456789</session_key> 
				</general>
				<categories> 
					<category> 
						<external_categories_id>bw0039</external_categories_id> 
						<categories_id>10</categories_id> 
						<parent_id>38</parent_id> 
						<categories_status>1</categories_status> 

						<categories_name language_id="1">Testcat.3e</categories_name> 
						<categories_heading_title language_id="1">Test Category 3</categories_heading_title> 
						<categories_description language_id="1" type="2">Sub-Category</categories_description> 
						<categories_meta_keywords language_id="1" type="0">My Keywords</categories_meta_keywords> 

						<categories_name language_id="2">Testktg3e</categories_name> 
						<categories_heading_title language_id="2">Testkategorie 3</categories_heading_title> 
						<categories_description language_id="2" type="2">Unterkategorie</categories_description> 
						<categories_meta_keywords language_id="2" type="0">Keywords</categories_meta_keywords> 
					</category>
					
					<category> 
						<external_categories_id>bw0040</external_categories_id> 
						<categories_id></categories_id> 
						<parent_id>0</parent_id> 
						<categories_status>1</categories_status> 

						<categories_name language_id="1">Testcat.4</categories_name> 
						<categories_heading_title language_id="1">Test Category 4</categories_heading_title> 
						<categories_description language_id="1" type="2">Sub-Category</categories_description> 
						<categories_meta_keywords language_id="1" type="0">My Keywords</categories_meta_keywords> 

						<categories_name language_id="2">Testktg4</categories_name> 
						<categories_heading_title language_id="2">Testkategorie 4</categories_heading_title> 
						<categories_description language_id="2" type="2">Unterkategorie</categories_description> 
						<categories_meta_keywords language_id="2" type="0">Keywords</categories_meta_keywords> 
					</category>
					
				</categories> 
			</GambioXML> 			
		';
		
		return $xmlDocument;
	}


	/**
	 * Add a languages child node to an existing XML node.
	 * 
	 * @param SimpleXMLElement $existingNode (ByRef) The existing node to be edited.
	 * @param string $p_newName New child node name. 
	 * @param string $p_newValue New child node value. 
	 * @param numeric $p_newLanguageId Language id will be set as an attribute.
	 */
	public function addLanguageChild(SimpleXMLElement &$existingNode, $p_newName, $p_newValue, $p_newLanguageId)
	{
		$node = $existingNode->addChild($p_newName, $p_newValue);
		$node->addAttribute('language_id', $p_newLanguageId);
		$node = NULL;
	}
}