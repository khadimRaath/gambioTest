<?php
/* --------------------------------------------------------------
   cao_update.php 2014-11-25 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

******************************************************************************************
*                                                                                          *
*  CAO-Faktura für Windows Version 1.2 (http://www.cao-wawi.de)                            *
*  Copyright (C) 2004 Jan Pokrandt / Jan@JP-SOFT.de                                        *
*                                                                                          *
*  This program is free software; you can redistribute it and/or                           *
*  modify it under the terms of the GNU General Public License                             *
*  as published by the Free Software Foundation; either version 2                          *
*  of the License, or any later version.                                                   *
*                                                                                          *
*  This program is distributed in the hope that it will be useful,                         *
*  but WITHOUT ANY WARRANTY; without even the implied warranty of                          *
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                           *
*  GNU General Public License for more details.                                            *
*                                                                                          *
*  You should have received a copy of the GNU General Public License                       *
*  along with this program; if not, write to the Free Software                             *
*  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.             *
*                                                                                          *
*  ******* CAO-Faktura comes with ABSOLUTELY NO WARRANTY ***************                   *
*                                                                                          *
********************************************************************************************
*                                                                                          *
* Eine Entfernung oder Veraenderung dieses Dateiheaders ist nicht zulaessig !!!            *
* Wenn Sie diese Datei veraendern dann fuegen Sie ihre eigenen Copyrightmeldungen          *
* am Ende diese Headers an                                                                 *
*                                                                                          *
********************************************************************************************
*                                                                                          *
*                                                                                          *
*  Programm     : CAO-Faktura                                                              *
*  Modul        : cao_update.php                                                           *
*  Stand        : 09.01.2005                                                               *
*  Version      : 1.38                                                                     *
*  Beschreibung : Script zum Datenaustausch CAO-Faktura <--> osCommerce-Shop               *
*                                                                                          *
*  based on:                                                                               *
*  (c) 2001 - 2003 osCommerce, Open Source E-Commerce Solutions                            *
*  (c) 2003 IN-Solution, Henri Schmidhuber                                                 *
*  (c) 2003 JP-Soft, Jan Pokrandt                                                          *
*  (c) 2003 RV-Design, Raphael Vullriede                                                   *
*                                                                                          *
*  History :                                                                               *
*                                                                                          *
*  - 25.06.2003 Version 0.1 released Jan Pokrandt                                          *
*  - 29.06.2003 order_opdate aus xml_export.php hierher verschoben                         *
*  - 17.07.2003 tep_array_merge durch array_merge ersetzt                                  *
*  - 18.07.2003 Code fuer Image_Upload hinzugefuegt                                        *
*  - 23.08.2003 Code fuer Hersteller-Update hinzugefuegt                                   *
*  - 25.10.2003 Kunden-Update hinzugefügt                                                  *
*  - 01.11.2003 Statusänderung werden wenn möglich in der Bestellsprache ausgeführt        *
*  - 01.12.2003 Code für 3 Produktbilder-Erweiterung hinzugefügt.                          *
*  - 06.06.2004 JP per DEFINE kann jetzt die Option "3 Produktbilder" geschaltet werden    *
*  - 06.06.2004 JP diverse kleine &Auml;nderungen beimKunden-Upload                        *
*  - 05.12.2004 RV automatisch Erkennung für 3 Produktbilder                               *
*  - 07.01.2005 JP Bugfix bei manufacturers_id (gemeldet im CAO-Forum durch r23)           *
*  - 09.01.2005 JP Logger eingebaut                                                        *    
*                                                                                          *
*******************************************************************************************/

$version_nr    = '1.39';
$version_datum = '2005.01.23';

define ('LOGGER',false);  // Um das Loggen einzuschalten false durch true ersetzen.

//define('DREI_PRODUKTBILDER',1); // aktivieren um die Erweiterung fuer 3 Produktbilder zu verwenden


/* Beispiel fuer Useragent 
     if (_SERVER["HTTP_USER_AGENT"]!='CAO-Faktura') exit;
*/
 
require('includes/application_top.php');
require(DIR_WS_FUNCTIONS . 'password_funcs.php');
//require('../includes/functions/password_funcs.php');


 if (LOGGER==true) 
 {
	// log data into db.

	$pdata ='';
	while (list($key, $value) = each($_POST))
	{
   	if (is_array($value))
   	{
   	  while (list($key1, $value1) = each($value))
        {
   	    $pdata .= addslashes($key)."[" . addslashes($key1)."] => ".addslashes($value1)."\\r\\n";    	
   	  }
   	} 
   	  else
   	{
   	  $pdata .= addslashes($key)." => ".addslashes($value)."\\r\\n";
   	}
	} 

	$gdata ='';
	while (list($key, $value) = each($_GET))
	{
   	$gdata .= addslashes($key)." => ".addslashes($value)."\\r\\n";
	} 

	tep_db_query("INSERT INTO cao_log
               (date,user,pw,method,action,post_data,get_data) VALUES
               (NOW(),'".$user."','".$password."','".$REQUEST_METHOD."','".$_POST['action']."','".$pdata."','".$gdata."')");
}


  
// Definition der Class UPLOAD, wenn diese nicht existiert (osc MS 1.1)
if (!class_exists(upload)) {  
  class upload {
    var $file, $filename, $destination, $permissions, $extensions, $tmp_filename, $message_location;

    function upload($file = '', $destination = '', $permissions = '777', $extensions = '') {
      $this->set_file($file);
      $this->set_destination($destination);
      $this->set_permissions($permissions);
      $this->set_extensions($extensions);

      $this->set_output_messages('direct');

      if (tep_not_null($this->file) && tep_not_null($this->destination)) {
        $this->set_output_messages('session');

        if ( ($this->parse() == true) && ($this->save() == true) ) {
          return true;
        } else {
// self destruct
          $this = null;

          return false;
        }
      }
    }

    function parse() {
      global $messageStack;

      if (isset($_FILES[$this->file])) {
        $file = array('name' => $_FILES[$this->file]['name'],
                      'type' => $_FILES[$this->file]['type'],
                      'size' => $_FILES[$this->file]['size'],
                      'tmp_name' => $_FILES[$this->file]['tmp_name']);
      } elseif (isset($GLOBALS['HTTP_POST_FILES'][$this->file])) {
        global $HTTP_POST_FILES;

        $file = array('name' => $HTTP_POST_FILES[$this->file]['name'],
                      'type' => $HTTP_POST_FILES[$this->file]['type'],
                      'size' => $HTTP_POST_FILES[$this->file]['size'],
                      'tmp_name' => $HTTP_POST_FILES[$this->file]['tmp_name']);
      } else {
        $file = array('name' => (isset($GLOBALS[$this->file . '_name']) ? $GLOBALS[$this->file . '_name'] : ''),
                      'type' => (isset($GLOBALS[$this->file . '_type']) ? $GLOBALS[$this->file . '_type'] : ''),
                      'size' => (isset($GLOBALS[$this->file . '_size']) ? $GLOBALS[$this->file . '_size'] : ''),
                      'tmp_name' => (isset($GLOBALS[$this->file]) ? $GLOBALS[$this->file] : ''));
      }

      if ( tep_not_null($file['tmp_name']) && ($file['tmp_name'] != 'none') && is_uploaded_file($file['tmp_name']) ) {
        if (sizeof($this->extensions) > 0) {
          if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $this->extensions)) {
            if ($this->message_location == 'direct') {
              $messageStack->add(ERROR_FILETYPE_NOT_ALLOWED, 'error');
            } else {
              $messageStack->add_session(ERROR_FILETYPE_NOT_ALLOWED, 'error');
            }

            return false;
          }
        }

        $this->set_file($file);
        $this->set_filename($file['name']);
        $this->set_tmp_filename($file['tmp_name']);

        return $this->check_destination();
      } else {
        if ($this->message_location == 'direct') {
          $messageStack->add(WARNING_NO_FILE_UPLOADED, 'warning');
        } else {
          $messageStack->add_session(WARNING_NO_FILE_UPLOADED, 'warning');
        }

        return false;
      }
    }

    function save() {
      global $messageStack;

      if (substr($this->destination, -1) != '/') $this->destination .= '/';

      if (move_uploaded_file($this->file['tmp_name'], $this->destination . $this->filename)) {
        chmod($this->destination . $this->filename, $this->permissions);

        if ($this->message_location == 'direct') {
          $messageStack->add(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');
        } else {
          $messageStack->add_session(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');
        }

        return true;
      } else {
        if ($this->message_location == 'direct') {
          $messageStack->add(ERROR_FILE_NOT_SAVED, 'error');
        } else {
          $messageStack->add_session(ERROR_FILE_NOT_SAVED, 'error');
        }

        return false;
      }
    }

    function set_file($file) {
      $this->file = $file;
    }

    function set_destination($destination) {
      $this->destination = $destination;
    }

    function set_permissions($permissions) {
      $this->permissions = octdec($permissions);
    }

    function set_filename($filename) {
      $this->filename = $filename;
    }

    function set_tmp_filename($filename) {
      $this->tmp_filename = $filename;
    }

    function set_extensions($extensions) {
      if (tep_not_null($extensions)) {
        if (is_array($extensions)) {
          $this->extensions = $extensions;
        } else {
          $this->extensions = array($extensions);
        }
      } else {
        $this->extensions = array();
      }
    }

    function check_destination() {
      global $messageStack;

      if (!is_writeable($this->destination)) {
        if (is_dir($this->destination)) {
          if ($this->message_location == 'direct') {
            $messageStack->add(sprintf(ERROR_DESTINATION_NOT_WRITEABLE, $this->destination), 'error');
          } else {
            $messageStack->add_session(sprintf(ERROR_DESTINATION_NOT_WRITEABLE, $this->destination), 'error');
          }
        } else {
          if ($this->message_location == 'direct') {
            $messageStack->add(sprintf(ERROR_DESTINATION_DOES_NOT_EXIST, $this->destination), 'error');
          } else {
            $messageStack->add_session(sprintf(ERROR_DESTINATION_DOES_NOT_EXIST, $this->destination), 'error');
          }
        }

        return false;
      } else {
        return true;
      }
    }

    function set_output_messages($location) {
      switch ($location) {
        case 'session':
          $this->message_location = 'session';
          break;
        case 'direct':
        default:
          $this->message_location = 'direct';
          break;
      }
    }
  }
}  



  header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer geändert
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header ("Content-type: text/xml");
  
  // Default-Sprache
  $LangID = 2;
  
  $table_has_products_image_medium = false;
  $table_has_products_image_large = false;
  
  $images_query = tep_db_query(' SHOW COLUMNS FROM '.TABLE_PRODUCTS);
  while($column = tep_db_fetch_array($images_query)) {
        if ($column['Field'] == 'products_image_medium') {
          $table_has_products_image_medium = true;
        }
        if ($column['Field'] == 'products_image_large') {
          $table_has_products_image_large = true;
        }
  }
  if ($table_has_products_image_medium && $table_has_products_image_large) {
      define('DREI_PRODUKTBILDER', true);
  } else {
      define('DREI_PRODUKTBILDER', false);
  }

/*
  $HTTP_POST_VARS = $HTTP_GET_VARS;
  $REQUEST_METHOD='POST';
*/


//  if (($HTTP_POST_VARS['action']) && ($REQUEST_METHOD=='POST'))
  if (($_POST['action']) && ($_SERVER['REQUEST_METHOD']=='POST'))
  {
    switch ($_POST['action']) 
    {
      case 'manufacturers_image_upload':
        
        if ($manufacturers_image = new upload('manufacturers_image', DIR_FS_CATALOG_IMAGES)) {
            $code = 0;
            $message = 'OK';
        } else {
            $code = -1;
            $message = 'UPLOAD FAILED';
        }
        
        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<STATUS>' . "\n" .
            '<STATUS_DATA>' . "\n" .
            '<CODE>' . $code . '</CODE>' . "\n" .
            '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
            '<MESSAGE>' . $message . '</MESSAGE>' . "\n" . 
            '<FILE_NAME>'.$manufacturers_image->filename.'</FILE_NAME>'."\n".
            '</STATUS_DATA>' . "\n" .
            '</STATUS>' . "\n\n";
            
        echo $schema;
        exit;
      
      case 'categories_image_upload':
        
        if ($categories_image = new upload('categories_image', DIR_FS_CATALOG_IMAGES)) {
            $code = 0;
            $message = 'OK';
        } else {
            $code = -1;
            $message = 'UPLOAD FAILED';
        }        
        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<STATUS>' . "\n" .
            '<STATUS_DATA>' . "\n" .
            '<CODE>' . $code . '</CODE>' . "\n" .
            '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
            '<MESSAGE>' . $message . '</MESSAGE>' . "\n" . 
            '<FILE_NAME>'.$categories_image->filename.'</FILE_NAME>'."\n".
            '</STATUS_DATA>' . "\n" .
            '</STATUS>' . "\n\n";
            
        echo $schema;
        exit;
        
      case 'products_image_upload':
        
        $products_image = new upload('products_image');
        $products_image->set_destination(DIR_FS_CATALOG_IMAGES);
        if ($products_image->parse() && $products_image->save())  {
            $code = 0;
            $message = 'OK';
        } else {
            $code = -1;
            $message = 'UPLOAD FAILED';
        }        
        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
             '<STATUS>' . "\n" .
            '<STATUS_DATA>' . "\n" .
            '<CODE>' . $code . '</CODE>' . "\n" .
            '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
            '<MESSAGE>' . $message . '</MESSAGE>' . "\n" . 
            '<FILE_NAME>'.$products_image->filename.'</FILE_NAME>'."\n".
            '</STATUS_DATA>' . "\n" .
            '</STATUS>' . "\n\n";
            
        echo $schema;
        exit;
        
      case 'products_image_upload_med':
        if (DREI_PRODUKTBILDER == true) {
            $products_image = new upload('products_image');
            $products_image->set_destination(DIR_FS_CATALOG_IMAGES_MEDIUM);
            if ($products_image->parse() && $products_image->save())  {
                $code = 0;
                $message = 'OK';
            } else {
                $code = -1;
                $message = 'UPLOAD FAILED';
            }        
            $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                 '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                '<CODE>' . $code . '</CODE>' . "\n" .
                '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                '<MESSAGE>' . $message . '</MESSAGE>' . "\n" . 
                '<FILE_NAME>'.$products_image->filename.'</FILE_NAME>'."\n".
                '</STATUS_DATA>' . "\n" .
                '</STATUS>' . "\n\n";
                
            echo $schema;
        } else {
          $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                '<CODE>' . '-1' . '</CODE>' . "\n" .
                '<MESSAGE>' . 'MULTIPLE IMAGES NOT INSTALLED' . '</MESSAGE>' . "\n" . 
                '</STATUS_DATA>' . "\n" .
                '</STATUS>' . "\n\n";
          echo $schema;
        }
		  exit;

      case 'products_image_upload_large':
        if (DREI_PRODUKTBILDER == true) {

            $products_image = new upload('products_image');
            $products_image->set_destination(DIR_FS_CATALOG_IMAGES_LARGE);
            if ($products_image->parse() && $products_image->save())  {
                $code = 0;
                $message = 'OK';
            } else {
                $code = -1;
                $message = 'UPLOAD FAILED';
            }        
            $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                 '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                '<CODE>' . $code . '</CODE>' . "\n" .
                '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                '<MESSAGE>' . $message . '</MESSAGE>' . "\n" . 
                '<FILE_NAME>'.$products_image->filename.'</FILE_NAME>'."\n".
                '</STATUS_DATA>' . "\n" .
                '</STATUS>' . "\n\n";
                
            echo $schema;
        } else {
          $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                '<CODE>' . '-1' . '</CODE>' . "\n" .
                '<MESSAGE>' . 'MULTIPLE IMAGES NOT INSTALLED' . '</MESSAGE>' . "\n" . 
                '</STATUS_DATA>' . "\n" .
                '</STATUS>' . "\n\n";
          echo $schema;
        }
        exit;   
      
      
      case 'manufacturers_update':
      
        $manufacturers_id = tep_db_prepare_input($_POST['mID']);
        
        if (isset($manufacturers_id))
        {
        
	        // Hersteller laden
	        $count_query = tep_db_query("select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified from " . TABLE_MANUFACTURERS . " where manufacturers_id='" . $manufacturers_id . "'");
	        if ($manufacturer = tep_db_fetch_array($count_query))
	        {
	           $exists = 1;
	           // aktuelle Herstellerdaten laden
	           $manufacturers_name  = $manufacturer['manufacturers_name'];
	           $manufacturers_image = $manufacturer['manufacturers_image'];
	           $date_added          = $manufacturer['date_added'];
	           $last_modified       = $manufacturer['last_modified'];
	        } 
	        else $exists = 0; 
	        
	        // Variablen nur ueberschreiben wenn als Parameter vorhanden !!!
	        if (isset($_POST['manufacturers_name'])) $manufacturers_name = tep_db_prepare_input($_POST['manufacturers_name']);
	        if (isset($_POST['manufacturers_image'])) $manufacturers_image = tep_db_prepare_input($_POST['manufacturers_image']);
	        
	        $sql_data_array = array('manufacturers_id' => $manufacturers_id,
	                                'manufacturers_name' => $manufacturers_name,
	                                'manufacturers_image' => $manufacturers_image);
	                                
	        if ($exists==0) // Neuanlage (ID wird von CAO virgegeben !!!)
	        {
	          $mode='APPEND';
	          $insert_sql_data = array('date_added' => 'now()');
	          $sql_data_array = /*tep_*/array_merge($sql_data_array, $insert_sql_data);
	          /*
	          echo "SQL_DATA:<br><br>";
	          print_r ($sql_data_array);
	          echo "<br><br>Insert-Data<br>";
	          print_r ($insert_sql_data);
	          */
	          tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
	          $products_id = tep_db_insert_id();
	        } 
	        elseif ($exists==1) //Update
	        {
	          $mode='UPDATE';
	          $update_sql_data = array('last_modified' => 'now()');
	          $sql_data_array = /*tep_*/array_merge($sql_data_array, $update_sql_data);
	          /*
	          echo "SQL_DATA:<br><br>";
	          print_r ($sql_data_array);
	          echo "<br><br>Update-Data<br>";
	          print_r ($update_sql_data);*/
	          
	          tep_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', 'manufacturers_id = \'' . tep_db_input($manufacturers_id) . '\'');
	        }
	        
	        $languages = tep_get_languages();
	        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
	        {
	          $language_id = $languages[$i]['id'];
	          
	          // Bestehende Daten laden
	          $desc_query = tep_db_query("select manufacturers_id,languages_id,manufacturers_url,url_clicked,date_last_click from " . 
	                                    TABLE_MANUFACTURERS_INFO . " where manufacturers_id='" . $manufacturers_id . "' and languages_id='" . $language_id . "'");
	          if ($desc = tep_db_fetch_array($desc_query))
	          {
	            $manufacturers_url = $desc['manufacturers_url'];
	            $url_clicked       = $desc['url_clicked'];
	            $date_last_click   = $desc['date_last_click'];
	          }
	          
	          // uebergebene Daten einsetzen
	          if (isset($_POST['manufacturers_url'][$language_id])) $manufacturers_url=tep_db_prepare_input($_POST['manufacturers_url'][$language_id]);
	          if (isset($_POST['url_clicked'][$language_id]))       $url_clicked=tep_db_prepare_input($_POST['url_clicked'][$language_id]);
	          if (isset($_POST['date_last_click'][$language_id]))   $date_last_click=tep_db_prepare_input($_POST['date_last_click'][$language_id]);
	              
	          
	          $sql_data_array = array('manufacturers_url' => $manufacturers_url);
	          
	          if ($exists==0) // Insert
	          {
	            $insert_sql_data = array('manufacturers_id' => $manufacturers_id,
	                                     'languages_id' => $language_id);
	            $sql_data_array = /*tep_*/array_merge($sql_data_array, $insert_sql_data);
	            tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
	          } 
	          elseif ($exists==1) // Update
	          {
	            tep_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', 'manufacturers_id = \'' . tep_db_input($manufacturers_id) . '\' and languages_id = \'' . $language_id . '\'');
	          }
	        }
	         
	        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		               '<STATUS>' . "\n" .
		               '<STATUS_DATA>' . "\n" .
		          	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
		           	   '<CODE>' . '0' . '</CODE>' . "\n" .
		               '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
		               '<MODE>' . $mode . '</MODE>' . "\n" .
		               '<MANUFACTURERS_ID>' . $mID . '</MANUFACTURERS_ID>' . "\n" .
		               '</STATUS_DATA>' . "\n" .
		               '</STATUS>' . "\n\n";
		                  
		     echo $schema;
			  
			  exit;
		  }
		  	else
		  {
			  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  }
		  
		case 'manufacturers_erase':
		
        $ManID  = tep_db_prepare_input($_POST['mID']);
		  
		  if (isset($ManID))
		  {
          // Hersteller loeschen
          tep_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$ManID . "'");
          tep_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$ManID . "'");
          // Herstellerverweis in den Artikeln loeschen
          tep_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . (int)$ManID . "'");
           
          $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                 '<STATUS>' . "\n" .
	                 '<STATUS_DATA>' . "\n" .
	             	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	             	   '<CODE>' . '0' . '</CODE>' . "\n" .
	                 '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
	                 '</STATUS_DATA>' . "\n" .
	                 '</STATUS>' . "\n\n";
	                  
	       echo $schema;
		  }
		  	else
		  {
			  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  }
           
		  exit;
        
        
      case 'products_update':
          
        $products_id = tep_db_prepare_input($_POST['pID']);
        
        // product laden
        
        if (DREI_PRODUKTBILDER == true)
        {
          $count_query = tep_db_query("select products_quantity,products_model,products_image, products_image_medium, products_image_large, products_price,products_date_available,products_weight,products_status,products_tax_class_id,manufacturers_id from " . TABLE_PRODUCTS . " where products_id='" . $products_id . "'");
        }
        	 else
        {
          $count_query = tep_db_query("select products_quantity,products_model,products_image,products_price,products_date_available,products_weight,products_status,products_tax_class_id,manufacturers_id from " . TABLE_PRODUCTS . " where products_id='" . $products_id . "'");
        }
        
        if ($product = tep_db_fetch_array($count_query))
        {
           $exists = 1;
           // aktuelle Produktdaten laden
           $products_quantity = $product['products_quantity'];
           $products_model = $product['products_model'];
           $products_image = $product['products_image'];
           
           if (DREI_PRODUKTBILDER == true)
           {
             $products_image_med = $product['products_image_medium'];
             $products_image_large = $product['products_image_large'];
           }
           
           $products_price = $product['products_price'];
           $products_date_available = $product['products_date_available'];
           $products_weight = $product['products_weight'];
           $products_status = $product['products_status'];
           $products_tax_class_id = $product['products_tax_class_id'];
           $manufacturers_id = $product['manufacturers_id'];
        } 
        else $exists = 0; 
        
        // Variablen nur ueberschreiben wenn als Parameter vorhanden !!!
        if (isset($_POST['products_quantity'])) $products_quantity = tep_db_prepare_input($_POST['products_quantity']);
        if (isset($_POST['products_model'])) $products_model = tep_db_prepare_input($_POST['products_model']);
        if (isset($_POST['products_image'])) $products_image = tep_db_prepare_input($_POST['products_image']);
        
        if (DREI_PRODUKTBILDER == true)
        {
          if (isset($_POST['products_image_med'])) $products_image_med = tep_db_prepare_input($_POST['products_image_med']);
          if (isset($_POST['products_image_large'])) $products_image_large = tep_db_prepare_input($_POST['products_image_large']);
        }
        
        if (isset($_POST['products_price'])) $products_price = tep_db_prepare_input($_POST['products_price']);
        if (isset($_POST['products_date_available'])) $products_date_available = tep_db_prepare_input($_POST['products_date_available']);
        if (isset($_POST['products_weight'])) $products_weight = tep_db_prepare_input($_POST['products_weight']);
        if (isset($_POST['products_status'])) $products_status = tep_db_prepare_input($_POST['products_status']);
        if (isset($_POST['products_tax_class_id'])) $products_tax_class_id = tep_db_prepare_input($_POST['products_tax_class_id']);
        if (isset($_POST['manufacturers_id'])) $manufacturers_id = tep_db_prepare_input($_POST['manufacturers_id']);
        
        $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';
          
        if (DREI_PRODUKTBILDER == true)
        {
          $sql_data_array = array('products_id' => $products_id,
                                 'products_quantity' => $products_quantity,
                                 'products_model' => $products_model,
                                 'products_image' => ($products_image == 'none') ? '' : $products_image,
				                     'products_image_medium' => ($products_image_med == 'none') ? '' : $products_image_med,
				                     'products_image_large' => ($products_image_large == 'none') ? '' : $products_image_large,
                                 'products_price' => $products_price,
                                 'products_date_available' => $products_date_available,
                                 'products_weight' => $products_weight,
                                 'products_status' => $products_status,
                                 'products_tax_class_id' => $products_tax_class_id,
                                 'manufacturers_id' => $manufacturers_id);
        }
        	else
        {
          
          $sql_data_array = array('products_id' => $products_id,
                                  'products_quantity' => $products_quantity,
                                  'products_model' => $products_model,
                                  'products_image' => ($products_image == 'none') ? '' : $products_image,
                                  'products_price' => $products_price,
                                  'products_date_available' => $products_date_available,
                                  'products_weight' => $products_weight,
                                  'products_status' => $products_status,
                                  'products_tax_class_id' => $products_tax_class_id,
                                  'manufacturers_id' => $manufacturers_id);
        }
          
        if ($exists==0) // Neuanlage (ID wird an CAO zurueckgegeben !!!)
        {
          $mode='APPEND';
          $insert_sql_data = array('products_date_added' => 'now()');
          $sql_data_array = /*tep_*/array_merge($sql_data_array, $insert_sql_data);
          /*
          echo "SQL_DATA:<br><br>";
          print_r ($sql_data_array);
          echo "<br><br>Insert-Data<br>";
          print_r ($insert_sql_data);
          */
          tep_db_perform(TABLE_PRODUCTS, $sql_data_array);
          $products_id = tep_db_insert_id();
        } 
        elseif ($exists==1) //Update
        {
          $mode='UPDATE';
          $update_sql_data = array('products_last_modified' => 'now()');
          $sql_data_array = /*tep_*/array_merge($sql_data_array, $update_sql_data);
          /*
          echo "SQL_DATA:<br><br>";
          print_r ($sql_data_array);
          echo "<br><br>Update-Data<br>";
          print_r ($update_sql_data);*/
          
          tep_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\'');
        }
        
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) 
        {
          $language_id = $languages[$i]['id'];
          
          // Bestehende Daten laden
          $desc_query = tep_db_query("select products_id,products_name,products_description,products_url,products_viewed,language_id from " . 
                                    TABLE_PRODUCTS_DESCRIPTION . " where products_id='" . $products_id . "' and language_id='" . $language_id . "'");
          if ($desc = tep_db_fetch_array($desc_query))
          {
            $products_name = $desc['products_name'];
            $products_description = $desc['products_description'];
            $products_url = $desc['products_url'];
          }
          
          // uebergebene Daten einsetzen
          if (isset($_POST['products_name'][$language_id]))        $products_name=tep_db_prepare_input($_POST['products_name'][$language_id]);
          if (isset($_POST['products_description'][$language_id])) $products_description=tep_db_prepare_input($_POST['products_description'][$language_id]);
          if (isset($_POST['products_url'][$language_id]))         $products_url=tep_db_prepare_input($_POST['products_url'][$language_id]);
              
          
          $sql_data_array = array('products_name' => $products_name,
                                  'products_description' => $products_description,
                                  'products_url' => $products_url);
          
          if ($exists==0) // Insert
          {
            $insert_sql_data = array('products_id' => $products_id,
                                     'language_id' => $language_id);
            $sql_data_array = /*tep_*/array_merge($sql_data_array, $insert_sql_data);
            tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
          } 
          elseif ($exists==1) // Update
          {
            tep_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . tep_db_input($products_id) . '\' and language_id = \'' . $language_id . '\'');
          }
        }
         
        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	               '<STATUS>' . "\n" .
	               '<STATUS_DATA>' . "\n" .
	          	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	           	   '<CODE>' . '0' . '</CODE>' . "\n" .
	               '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
	               '<MODE>' . $mode . '</MODE>' . "\n" .
	               '<PRODUCTS_ID>' . $products_id . '</PRODUCTS_ID>' . "\n" .
	               '</STATUS_DATA>' . "\n" .
	               '</STATUS>' . "\n\n";
	                  
	     echo $schema;
		  
		  exit;
		case 'products_erase':
		
        $ProdID  = tep_db_prepare_input($_POST['prodid']);
		  
		  if (isset($ProdID))
		  {
           
           // ProductsToCategieries loeschen bei denen die products_id = ... ist
           $res1 = tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id='" . $ProdID . "'");
           
           // Product loeschen
           tep_remove_product($ProdID);
           
           $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '0' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
	                  '<SQL_RES1>' . $res1 . '</SQL_RES1>' . "\n" .
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  
		  }
		  	else
		  {
			  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  }
           
		  exit;
		case 'categories_update':
		
		  
		  $CatID    = tep_db_prepare_input($_POST['catid']);
		  $ParentID = tep_db_prepare_input($_POST['parentid']);
		  $Sort     = tep_db_prepare_input($_POST['sort']);
		  $Image    = tep_db_prepare_input($_POST['image']);
		  $Name     = tep_db_prepare_input(UrlDecode($_POST['name']));
		  
		  
		  if (isset($ParentID) && isset($CatID))
		  {
           
           // Testen ob Eintrag existiert
           $count_query = tep_db_query("select COUNT(*) as ANZ from " . TABLE_CATEGORIES . " where categories_id='" . $CatID . "'");
           if (tep_db_num_rows($count_query)) 
           {
              $count = tep_db_fetch_array($count_query);
              
              $exists = $count['ANZ'];
           } 
           else $exists = 0; 
           
           if ($exists==1)
           {          
             $mode='UPDATE';
             
             $values  = "parent_id='" . $ParentID . "', last_modified=now()";
             
             if (isset($Sort)) $values .= ", sort_order='" . $Sort . "'";
             if (isset($Image)) $values .= ", categories_image='" . $Image . "'";
             
             $res1 = tep_db_query("update " . TABLE_CATEGORIES . " SET " . $values . " where categories_id='" . $CatID . "'");	
           }
             else
           {
             $mode='APPEND';
             
             if (!isset($Sort)) $Sort=0;
             
             $felder  = "(categories_id, parent_id, date_added, sort_order";
             if (isset($Image)) $felder .= ", categories_image";
             $felder .= ")";
           
             $values  = "Values(" . "'" . $CatID . "', '" . $ParentID . "', now(), '" . $Sort . "'";
             if (isset($Image)) $values .= ", '" . $Image . "'";
             $values .= ")";
             
             $res1 = tep_db_query("insert into " . TABLE_CATEGORIES . " " . $felder . $values);           
           }
           
           // Namen setzen
           if (isset($Name))
           {
             $res2 = tep_db_query("replace into " . TABLE_CATEGORIES_DESCRIPTION . " (categories_id, language_id, categories_name) Values ('" . $CatID ."', '" . $LangID . "', '" . $Name . "')");
           }
           
           $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '0' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
	                  '<MODE>' . $mode . '</MODE>' . "\n" .
	                  '<SQL_RES1>' . $res1 . '</SQL_RES1>' . "\n" .
	                  '<SQL_RES2>' . $res2 . '</SQL_RES2>' . "\n" .
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  
		  }
		  	else
		  {
			  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  }
		  
		  exit;
		  
		case 'categories_erase':
		
		  $CatID  = tep_db_prepare_input($_POST['catid']);
		  
		  if (isset($CatID))
		  {
           
           // Categorie loeschen
           $res1 = tep_db_query("delete from " . TABLE_CATEGORIES . " where categories_id='" . $CatID . "'");
           
           // ProductsToCategieries loeschen bei denen die Categorie = ... ist
           $res2 = tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id='" . $CatID . "'");
           
           // CategieriesDescription loeschenm bei denen die Categorie = ... ist
           $res3 = tep_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id='" . $CatID . "'");
           
           $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '0' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
	                  '<SQL_RES1>' . $res1 . '</SQL_RES1>' . "\n" .
	                  '<SQL_RES2>' . $res2 . '</SQL_RES2>' . "\n" .
	                  '<SQL_RES2>' . $res3 . '</SQL_RES2>' . "\n" .
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  
		  }
		  	else
		  {
			  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  }
		  
		  exit;
		  
		case 'prod2cat_update':
		
		  $ProdID = tep_db_prepare_input($_POST['prodid']);
		  $CatID  = tep_db_prepare_input($_POST['catid']);
		  
		  if (isset($ProdID) && isset($CatID))
		  {
           
           $res = tep_db_query("replace into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) Values ('" . $ProdID ."', '" . $CatID . "')");
           
           $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '0' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
	                  '<SQL_RES>' . $res . '</SQL_RES>' . "\n" .
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  
		  }
		  	else
		  {
			  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  }
		  
		  exit;
		case 'prod2cat_erase':
		
		  $ProdID = tep_db_prepare_input($_POST['prodid']);
		  $CatID  = tep_db_prepare_input($_POST['catid']);
		  
		  if (isset($ProdID) && isset($CatID))
		  {
           
           $res = tep_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id='" . $ProdID ."' and categories_id='" . $CatID . "'");
           
           $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '0' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
	                  '<SQL_RES>' . $res . '</SQL_RES>' . "\n" .
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  
		  }
		  	else
		  {
			  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
	                  
	        echo $schema;
		  
		  }
		  
		  exit; 
		  
		case 'order_update':
        
        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" . "\n";
        
        if ((isset($_POST['order_id'])) && (isset($_POST['status']))) 
        {
          // Per Post übergebene Variablen
          $oID = $_POST['order_id'];
          $status = $_POST['status'];
          $comments = tep_db_prepare_input($_POST['comments']);
          $cao_language = $_POST['cao_language'];  // german, english, espanol...
          // Weiter wird per POST übergeben:
          // $_POST['notify'] == 'on' für Emailversand
          // $_POST['notify_comments'] == 'on'  für Versand des Kommentars
          
          
            
          //Status überprüfen
          $check_status_query = tep_db_query("select * from " . TABLE_ORDERS . " where orders_id = '" . tep_db_input($oID) . "'");
          if ($check_status = tep_db_fetch_array($check_status_query)) 
          {
            if ($check_status['orders_status'] != $status || $comments != '') 
            {
              tep_db_query("update " . TABLE_ORDERS . " set orders_status = '" . tep_db_input($status) . "', last_modified = now() where orders_id = '" . tep_db_input($oID) . "'");
              $customer_notified = '0';
              if ($_POST['notify'] == 'on') 
              {
                // Falls eine Sprach ID zur Order existiert die Emailbestätigung in dieser Sprache ausführen
                if (isset($check_status['orders_language_id']) && $check_status['orders_language_id'] > 0 ) {
                  $osc_language_query = tep_db_query("select directory from " . TABLE_LANGUAGES . " where languages_id = '" . $check_status['orders_language_id'] . "'");
                  $osc_language = tep_db_fetch_array($osc_language_query);
                  if ( strlen($osc_language['directory']) > 0 ) {
                    $cao_language = $osc_language['directory'];
                  }
                  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $check_status['orders_language_id'] . "'");
                  if (tep_db_num_rows($orders_status_query) == 0) {
                    $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
                  }
                } else {
                  $orders_status_query = tep_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
                }

                $orders_statuses = array();
                $orders_status_array = array();
                while ($orders_status = tep_db_fetch_array($orders_status_query)) {
                  $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                                             'text' => $orders_status['orders_status_name']);
                  $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
                }
                
                // Wir nehmen die Orginal Emailbestätigung des OSC Admins
                if (file_exists(DIR_WS_LANGUAGES . $cao_language . '/' . FILENAME_ORDERS)) 
                {
                  include(DIR_WS_LANGUAGES . $cao_language . '/' . FILENAME_ORDERS);
                  $language = $cao_language;
                } else if (file_exists(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ORDERS)) 
                {
                  include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ORDERS);
                }               

                if ($_POST['notify_comments'] == 'on') 
                {
                  $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
                }
                $email = STORE_NAME . "\n" . EMAIL_SEPARATOR . "\n" . EMAIL_TEXT_ORDER_NUMBER . ' ' . $oID . "\n" . EMAIL_TEXT_INVOICE_URL . ' ' . tep_catalog_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL') . "\n" . EMAIL_TEXT_DATE_ORDERED . ' ' . tep_date_long($check_status['date_purchased']) . "\n\n" . $notify_comments . sprintf(EMAIL_TEXT_STATUS_UPDATE, $orders_status_array[$status]);
                tep_mail($check_status['customers_name'], $check_status['customers_email_address'], EMAIL_TEXT_SUBJECT, nl2br($email), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
                $customer_notified = '1';
              }
              tep_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . tep_db_input($oID) . "', '" . tep_db_input($status) . "', now(), '" . $customer_notified . "', '" . tep_db_input($comments)  . "')");
              $schema .= '<STATUS>' . "\n" .
                         '<STATUS_DATA>' . "\n" .
                         '<ORDER_ID>' . $oID . '</ORDER_ID>' . "\n" .
                         '<ORDER_STATUS>' . $status . '</ORDER_STATUS>' . "\n" .
                         '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
                         '<CODE>' . '0' . '</CODE>' . "\n" .
	                      '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
                         '</STATUS_DATA>' . "\n" .
                         '</STATUS>' . "\n";
            } 
            else if ($check_status['orders_status'] == $status) 
            {
              // Status ist bereits gesetzt
              $schema .= '<STATUS>' . "\n" .
                         '<STATUS_DATA>' . "\n" .
                         '<ORDER_ID>' . $oID . '</ORDER_ID>' . "\n" .
                         '<ORDER_STATUS>' . $status . '</ORDER_STATUS>' . "\n" .
                         '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
                         '<CODE>' . '1' . '</CODE>' . "\n" .
	                      '<MESSAGE>' . 'NO STATUS CHANGE' . '</MESSAGE>' . "\n" . 
                         '</STATUS_DATA>' . "\n" .
                         '</STATUS>' . "\n";
            }
          } 
            else           
          {
            // Fehler Order existiert nicht
            $schema .= '<STATUS>' . "\n" .
                       '<STATUS_DATA>' . "\n" .
                       '<ORDER_ID>' . $oID . '</ORDER_ID>' . "\n" .
                       '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	     '<CODE>' . '2' . '</CODE>' . "\n" .
	                    '<MESSAGE>' . 'ORDER_ID NOT FOUND OR SET' . '</MESSAGE>' . "\n" . 
                       '</STATUS_DATA>' . "\n" .
                       '</STATUS>' . "\n";
          }
        } 
          else 
        {
          $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
	                  '<STATUS>' . "\n" .
	                  '<STATUS_DATA>' . "\n" .
	              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
	              	   '<CODE>' . '99' . '</CODE>' . "\n" .
	                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
	                  '</STATUS_DATA>' . "\n" .
	                  '</STATUS>' . "\n\n";
        }
        echo $schema;
        exit;
        
      //-- Raphael Vullriede
		//-- add actions for customers
		case 'customers_update':
			$customers_id = -1;
			if (isset($_POST['cID'])) $customers_id = tep_db_prepare_input($_POST['cID']);
			
			$sql_customers_data_array = array();
			if (isset($_POST['customers_firstname'])) $sql_customers_data_array['customers_firstname'] = $_POST['customers_firstname'];
			if (isset($_POST['customers_lastname'])) $sql_customers_data_array['customers_lastname'] = $_POST['customers_lastname'];
//			if (isset($_POST['customers_company'])) $sql_customers_data_array['customers_company'] = $_POST['customers_company'];
			if (isset($_POST['customers_dob'])) $sql_customers_data_array['customers_dob'] = $_POST['customers_dob'];
			if (isset($_POST['customers_email'])) $sql_customers_data_array['customers_email_address'] = $_POST['customers_email'];
			if (isset($_POST['customers_tele'])) $sql_customers_data_array['customers_telephone'] = $_POST['customers_tele'];
			if (isset($_POST['customers_fax'])) $sql_customers_data_array['customers_fax'] = $_POST['customers_fax'];
			if (isset($_POST['customers_password'])) $sql_customers_data_array['customers_password'] = tep_encrypt_password($_POST['customers_password']);
			$sql_customers_data_array['customers_newsletter'] = "0"; // JAN ADDED		
			if (isset($_POST['customers_gender'])) $sql_customers_data_array['customers_gender'] = $_POST['customers_gender'];
			
			$sql_address_data_array =array();
			if (isset($_POST['customers_firstname'])) $sql_address_data_array['entry_firstname'] = $_POST['customers_firstname'];
			if (isset($_POST['customers_lastname'])) $sql_address_data_array['entry_lastname'] = $_POST['customers_lastname'];
			if (isset($_POST['customers_company'])) $sql_address_data_array['entry_company'] = $_POST['customers_company'];
			if (isset($_POST['customers_street'])) $sql_address_data_array['entry_street_address'] = $_POST['customers_street'];
			if (isset($_POST['customers_city'])) $sql_address_data_array['entry_city'] = $_POST['customers_city'];
			if (isset($_POST['customers_postcode'])) $sql_address_data_array['entry_postcode'] = $_POST['customers_postcode'];
			
			
			
			if (isset($_POST['customers_country_id'])) $country_code = $_POST['customers_country_id'];
			$country_query = "SELECT countries_id FROM ".TABLE_COUNTRIES." WHERE countries_iso_code_2 = '".$country_code ."' LIMIT 1";
			$country_result = tep_db_query($country_query);
			$row = tep_db_fetch_array($country_result);
			$sql_address_data_array['entry_country_id'] = $row['countries_id'];
			
				      
			$count_query = tep_db_query("SELECT count(*) as count FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . (int)$customers_id . "' LIMIT 1");
			$check = tep_db_fetch_array($count_query);
			
			if ($check['count'] > 0) {
				$mode = 'UPDATE';
				$address_book_result = tep_db_query("SELECT customers_default_address_id FROM ".TABLE_CUSTOMERS." WHERE customers_id = '". (int)$customers_id ."' LIMIT 1");
				$customer = tep_db_fetch_array($address_book_result);
				tep_db_perform(TABLE_CUSTOMERS, $sql_customers_data_array, 'update', "customers_id = '" . tep_db_input($customers_id) . "' LIMIT 1");
				tep_db_perform(TABLE_ADDRESS_BOOK, $sql_address_data_array, 'update', "customers_id = '" . tep_db_input($customers_id) . "' AND address_book_id = '".$customer['customers_default_address_id']."' LIMIT 1");
				tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$customers_id . "'  LIMIT 1");

			}  else {
				$mode= 'APPEND';
				tep_db_perform(TABLE_CUSTOMERS, $sql_customers_data_array);
				$customers_id = tep_db_insert_id();
				$sql_address_data_array['customers_id'] = $customers_id;
				tep_db_perform(TABLE_ADDRESS_BOOK, $sql_address_data_array);
				$address_id = tep_db_insert_id();
				tep_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . (int)$address_id . "' where customers_id = '" . (int)$customers_id . "'");
        
				tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int)$customers_id . "', '0', now())");
				
			}
	
			$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
				'<STATUS>' . "\n" .
					'<STATUS_DATA>' . "\n" .
						'<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
						'<CODE>' . '0' . '</CODE>' . "\n" .
						'<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
						'<MODE>' . $mode . '</MODE>' . "\n" .
						'<CUSTOMERS_ID>' . $customers_id . '</CUSTOMERS_ID>' . "\n" .
					'</STATUS_DATA>' . "\n" .
				'</STATUS>' . "\n\n";
		                  
			echo $schema;
			exit;
			
			
		case 'customers_erase':
			$cID  = tep_db_prepare_input($_POST['cID']);
			  
			if (isset($cID)) {
				tep_db_query("update " . TABLE_REVIEWS . " set customers_id = null where customers_id = '" .  $cID . "'");
				tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $cID . "'");
				tep_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" .$cID . "'");
				tep_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $cID. "'");
				tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $cID . "'");
				tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . $cID . "'");
				tep_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . $cID . "'");
	           
				$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
							  '<STATUS>' . "\n" .
							  '<STATUS_DATA>' . "\n" .
							   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
							   '<CODE>' . '0' . '</CODE>' . "\n" .
							  '<MESSAGE>' . 'OK' . '</MESSAGE>' . "\n" . 
							  '<SQL_RES1>' . $res1 . '</SQL_RES1>' . "\n" .
							  '</STATUS_DATA>' . "\n" .
							  '</STATUS>' . "\n\n";
							  
				echo $schema;
			} else {
				$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		                  '<STATUS>' . "\n" .
		                  '<STATUS_DATA>' . "\n" .
		              	   '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
		              	   '<CODE>' . '99' . '</CODE>' . "\n" .
		                  '<MESSAGE>' . 'PARAMETER ERROR' . '</MESSAGE>' . "\n" . 
		                  '</STATUS_DATA>' . "\n" .
		                  '</STATUS>' . "\n\n";
		                  
				echo $schema;
			}
			
			exit;
			//-- end actions for customers
	

		case 'version':
        
        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                  '<STATUS>' . "\n" .
                  '<STATUS_DATA>' . "\n" .
                  '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
                  '<CODE>' . '111' . '</CODE>' . "\n" .              
                  '<SCRIPT_VER>' . $version_nr . '</SCRIPT_VER>' . "\n" . 
                  '<SCRIPT_DATE>' . $version_datum . '</SCRIPT_DATE>' . "\n" . 
                  '</STATUS_DATA>' . "\n" .
                  '</STATUS>' . "\n\n";
        echo $schema;
        exit;      
		  
      default:
		  
		  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                  '<STATUS>' . "\n" .
                  '<STATUS_DATA>' . "\n" .
              	   '<CODE>' . '100' . '</CODE>' . "\n" .
                  '<MESSAGE>' . 'UNKNOWN ACTION PARAMETER' . '</MESSAGE>' . "\n" . 
                  '</STATUS_DATA>' . "\n" .
                  '</STATUS>' . "\n\n";
                  
        echo $schema;
        exit;
		  		  
    } // switch
  }
  	else
  {
   
    if (($_GET['action']=='version') && ($_SERVER['REQUEST_METHOD']=='GET'))
    {
      $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                '<ACTION>' . $_GET['action'] . '</ACTION>' . "\n" .
                '<CODE>' . '111' . '</CODE>' . "\n" .              
                '<SCRIPT_VER>' . $version_nr . '</SCRIPT_VER>' . "\n" . 
                  '<SCRIPT_DATE>' . $version_datum . '</SCRIPT_DATE>' . "\n" . 
                  '</STATUS_DATA>' . "\n" .
                  '</STATUS>' . "\n\n";
      echo $schema;  
    }
    	else
    {
      $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
                '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                '<CODE>' . '101' . '</CODE>' . "\n" .
                '<MESSAGE>' . 'METHOD NOT POST OR UNKNOWN PARAMETER' . '</MESSAGE>' . "\n" . 
                '</STATUS_DATA>' . "\n" .
                '</STATUS>' . "\n\n";
                  
      echo $schema;
    }
  }  