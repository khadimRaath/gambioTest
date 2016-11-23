<?php
/*	--------------------------------------------------------------
	lettr_validation.class.php
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2010 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------*/

  /**
   * @package Lettr
   * @subpackage Validation
   * @access private
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  class Lettr_Validation {
    // $one_attribute
    public static function presence_of($array_name, $array, $attribute_list, $either_attributes = null){
      if(!is_array($array)){
        throw new Lettr_IllegalArgumentException('Argument ist kein Array');
      }
      foreach($attribute_list as $attr){
        if(empty($array[$attr])){
          throw new Lettr_IllegalArgumentException('$'.$array_name.'["'.$attr.'"] ist leer oder nicht definiert.');
        }
      }
      // Optionale Attribute, von denen mindestens eins vorhanden sein muss. Mit RegExp!
      if ($either_attributes) {
        $found = false;
        foreach ($either_attributes as $attr) {
          if (!empty($array[$attr])) {
            $found = true;
          }
        }
        if (!$found) {
          foreach ($array as $key => $val) {
            foreach ($either_attributes as $attr) {
              if (preg_match("/" .$attr . "/i", $key, $matches)) {
                if (!empty($array[$matches[0]])){
                  $found = true;
                }
              }
            }
          }
        }
        if (!$found){ throw new Lettr_IllegalArgumentException('Es muss eines der folgenden Attribute vorhanden sein: ' . json_encode($either_attributes)); }
      }
    }
  }