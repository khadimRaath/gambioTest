<?php
/* --------------------------------------------------------------
   cao_import.php 2014-11-25 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

(c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: cao_import.php,v 1.2 2004/01/05 00:51:07 fanta2k Exp $)
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce coding standards; www.oscommerce.com
   (c) 2001 - 2003 TheMedia, Dipl.-Ing Thomas Plänkers
   (c) 2003 JP-Soft, Jan Pokrandt
   (c) 2003 IN-Solution, Henri Schmidhuber
   (c) 2003 www.websl.de, Karl Langmann
   (c) 2003 RV-Design Raphael Vullriede

   -----------------------------------------------------------------------------------------
   Released under the GNU General Public License

   Script based on:

   Script zum Datenaustausch CAO-Faktura <--> osCommerce-Shop v 0.8  | 01.11.2003
   CAO-Faktura für Windows Version 1.0
   Copyright (C) 2003 Jan Pokrandt / Jan@JP-SOFT.de

*******************************************************************************************/

/*
  Changes:
  1.1     :	switching POST/GET vars for CAO imageUpload
  1.2     : mulitlang inserts for Categories
  1.3     : xt:C v3.0 update


*/

$version_nr    = '1.3';
$version_datum = '2004.28.09';
define('CHARSET','iso-8859-1');

// falls die MWST vom shop vertauscht wird, hier false setzen.
define('SWITCH_MWST',true);

// Emails beim Kundenanlegen versenden ?
define('SEND_ACCOUNT_MAIL',true);

// Kundengruppen ID für Neukunden (default "neue Kunden einstellungen in XTC")
define('STANDARD_GROUP',DEFAULT_CUSTOMERS_STATUS_ID);

// Default-Sprache
$LangID = 2;
$Lang_folder = 'german';


require('../includes/application_top_export.php');
  include(DIR_FS_DOCUMENT_ROOT.'admin/includes/classes/'.IMAGE_MANIPULATOR);


// bufix for CAO image upload (no GET vars!)
if ($_POST['action']=='manufacturers_image_upload' || $_POST['action']=='categories_image_upload' || $_POST['action']=='products_image_upload') {

if ($_POST['user']) $_GET['user']=$_POST['user'];
if ($_POST['password']) $_GET['password']=$_POST['password'];

}



$user=preg_replace('/[^a-zA-Z0-9_.,*\s@-]/', '', (string)$_GET['user']);
$password=preg_replace('/[^a-zA-Z0-9_.,*\s@-]/', '', (string)$_GET['password']);

if (substr($password,0,2)=='%%') {
 $password=md5(substr($password,2,40));
}

if ($user!='' and $password!='') {

require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
require_once(DIR_FS_INC . 'xtc_redirect.inc.php');
require_once(DIR_FS_INC . 'xtc_rand.inc.php');

class upload {
    var $file, $filename, $destination, $permissions, $extensions, $tmp_filename;

    function upload($file = '', $destination = '', $permissions = '777', $extensions = '') {

      $this->set_file($file);
      $this->set_destination($destination);
      $this->set_permissions($permissions);
      $this->set_extensions($extensions);

      if (xtc_not_null($this->file) && xtc_not_null($this->destination)) {
        if ( ($this->parse() == true) && ($this->save() == true) ) {
          return true;
        } else {
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
      } elseif (isset($_FILES[$this->file])) {

        $file = array('name' => $_FILES[$this->file]['name'],
                      'type' => $_FILES[$this->file]['type'],
                      'size' => $_FILES[$this->file]['size'],
                      'tmp_name' => $_FILES[$this->file]['tmp_name']);
      } else {
        $file = array('name' => $GLOBALS[$this->file . '_name'],
                      'type' => $GLOBALS[$this->file . '_type'],
                      'size' => $GLOBALS[$this->file . '_size'],
                      'tmp_name' => $GLOBALS[$this->file]);
      }

      if ( xtc_not_null($file['tmp_name']) && ($file['tmp_name'] != 'none') && is_uploaded_file($file['tmp_name']) ) {
        if (sizeof($this->extensions) > 0) {
          if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $this->extensions)) {
  //          $messageStack->add_session(ERROR_FILETYPE_NOT_ALLOWED, 'error');

            return false;
          }
        }

        $this->set_file($file);
        $this->set_filename($file['name']);
        $this->set_tmp_filename($file['tmp_name']);

        return $this->check_destination();
      } else {

   //     if ($file['tmp_name']=='none') $messageStack->add_session(WARNING_NO_FILE_UPLOADED, 'warning');

        return false;
      }
    }

    function save() {
      global $messageStack;

      if (substr($this->destination, -1) != '/') $this->destination .= '/';

      // GDlib check
      if (!function_exists(imagecreatefromgif)) {

        // check if uploaded file = gif
        if ($this->destination==DIR_FS_CATALOG_ORIGINAL_IMAGES) {
            // check if merge image is defined .gif
            if (strstr(PRODUCT_IMAGE_THUMBNAIL_MERGE,'.gif') ||
                strstr(PRODUCT_IMAGE_INFO_MERGE,'.gif') ||
                strstr(PRODUCT_IMAGE_POPUP_MERGE,'.gif')) {

      //          $messageStack->add_session(ERROR_GIF_MERGE, 'error');
                return false;

            }
            // check if uploaded image = .gif
            if (strstr($this->filename,'.gif')) {
      //       $messageStack->add_session(ERROR_GIF_UPLOAD, 'error');
             return false;
            }

        }

      }



      if (move_uploaded_file($this->file['tmp_name'], $this->destination . $this->filename)) {
        chmod($this->destination . $this->filename, $this->permissions);

    //    $messageStack->add_session(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');

        return true;
      } else {
    //    $messageStack->add_session(ERROR_FILE_NOT_SAVED, 'error');

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
      if (xtc_not_null($extensions)) {
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
    //      $messageStack->add_session(sprintf(ERROR_DESTINATION_NOT_WRITEABLE, $this->destination), 'error');
        } else {
    //      $messageStack->add_session(sprintf(ERROR_DESTINATION_DOES_NOT_EXIST, $this->destination), 'error');
        }

        return false;
      } else {
        return true;
      }
    }

  }




  function xtc_try_upload($file = '', $destination = '', $permissions = '777', $extensions = ''){
      $file_object = new upload($file, $destination, $permissions, $extensions);
      if ($file_object->filename != '') return $file_object; else return false;
  }


// security  1.check if admin user with this mailadress exits, and got access to xml-export
//           2.check if pasword = true

    $check_customer_query=xtc_db_query("select customers_id,
                           customers_status,
                           customers_password
                           from " . TABLE_CUSTOMERS . " where
                           customers_email_address = '" . $user . "'");


    if (!xtc_db_num_rows($check_customer_query)) {

  header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer geändert
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header ("Content-type: text/xml");

  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
               '<STATUS>
               <STATUS_DATA>
               <CODE>105</CODE>
               <MESSAGE>WRONG LOGIN</MESSAGE>
               </STATUS_DATA>
               </STATUS>';

 echo $schema;


          } else {
      $check_customer = xtc_db_fetch_array($check_customer_query);
      // check if customer is Admin
      if ($check_customer['customers_status']!='0') {

  header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer geändert
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header ("Content-type: text/xml");

  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
               '<STATUS>
               <STATUS_DATA>
               <CODE>105</CODE>
               <MESSAGE>WRONG LOGIN</MESSAGE>
               </STATUS_DATA>
               </STATUS>';

  echo $schema;

      }

      // check if Admin is allowed to access xml_export
      $access_query=xtc_db_query("SELECT
                                  xml_export
                                  from admin_access
                                  WHERE customers_id='".$check_customer['customers_id']."'");
      $access_data = xtc_db_fetch_array($access_query);
      if ($access_data['xml_export']!=1) {

  header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer geändert
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header ("Content-type: text/xml");

  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
               '<STATUS>
               <STATUS_DATA>
               <CODE>105</CODE>
               <MESSAGE>WRONG LOGIN</MESSAGE>
               </STATUS_DATA>
               </STATUS>';

 echo $schema;

      }

      if ($check_customer['customers_password'] != $password) {


  header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer geändert
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header ("Content-type: text/xml");

  $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
               '<STATUS>
               <STATUS_DATA>
               <CODE>105</CODE>
               <MESSAGE>WRONG PASSWORD</MESSAGE>
               </STATUS_DATA>
               </STATUS>';

 echo $schema;

      } else {
      }



  // include needed Classes for Upload and Image resize

  require_once(DIR_FS_INC .'xtc_not_null.inc.php');


function clear_string($value) {
  $string=str_replace("'",'',$value);
  $string=str_replace(')','',$string);
  $string=str_replace('(','',$string);
  $array=explode(',',$string);
  return $array;

}

function xtc_RandomString($length) {
       $chars = array( 'a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J',  'k', 'K', 'l', 'L', 'm', 'M', 'n','N', 'o', 'O', 'p', 'P', 'q', 'Q', 'r', 'R', 's', 'S', 't', 'T',  'u', 'U', 'v','V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');

       $max_chars = count($chars) - 1;
       srand( (double) microtime()*1000000);

       $rand_str = '';
       for($i=0;$i<$length;$i++)
       {
         $rand_str = ( $i == 0 ) ? $chars[rand(0, $max_chars)] : $rand_str . $chars[rand(0, $max_chars)];
       }

         return $rand_str;
}

function xtc_create_password($pass) {

      return md5($pass);
}

function xtc_remove_product($product_id) {
    $product_image_query = xtc_db_query("select products_image from " . TABLE_PRODUCTS . " where products_id = '" . xtc_db_input($product_id) . "'");
    $product_image = xtc_db_fetch_array($product_image_query);

    $duplicate_image_query = xtc_db_query("select count(*) as total from " . TABLE_PRODUCTS . " where products_image = '" . xtc_db_input($product_image['products_image']) . "'");
    $duplicate_image = xtc_db_fetch_array($duplicate_image_query);

    if ($duplicate_image['total'] < 2) {
      if (file_exists(DIR_FS_CATALOG_POPUP_IMAGES . $product_image['products_image'])) {
        @unlink(DIR_FS_CATALOG_POPUP_IMAGES . $product_image['products_image']);
      }
// START CHANGES
      $image_subdir = BIG_IMAGE_SUBDIR;
      if (substr($image_subdir, -1) != '/') $image_subdir .= '/';
      if (file_exists(DIR_FS_CATALOG_IMAGES . $image_subdir . $product_image['products_image'])) {
        @unlink(DIR_FS_CATALOG_IMAGES . $image_subdir . $product_image['products_image']);
      }
// END CHANGES
    }

    xtc_db_query("delete from " . TABLE_SPECIALS . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where products_id = '" . xtc_db_input($product_id) . "'");
    xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where products_id = '" . xtc_db_input($product_id) . "'");


    // get statuses
    $customers_statuses_array = array(array());

     $customers_statuses_query = xtc_db_query("select * from " . TABLE_CUSTOMERS_STATUS . " where language_id = '".$LangID."' order by customers_status_id");

     while ($customers_statuses = xtc_db_fetch_array($customers_statuses_query)) {
       $customers_statuses_array[] = array('id' => $customers_statuses['customers_status_id'],
                                           'text' => $customers_statuses['customers_status_name']);

      }

    for ($i=0,$n=sizeof($customers_status_array);$i<$n;$i++) {
     xtc_db_query("delete from personal_offers_by_customers_status_" . $i . " where products_id = '" . xtc_db_input($product_id) . "'");

    }

    $product_reviews_query = xtc_db_query("select reviews_id from " . TABLE_REVIEWS . " where products_id = '" . xtc_db_input($product_id) . "'");
    while ($product_reviews = xtc_db_fetch_array($product_reviews_query)) {
      xtc_db_query("delete from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . $product_reviews['reviews_id'] . "'");
    }
    xtc_db_query("delete from " . TABLE_REVIEWS . " where products_id = '" . xtc_db_input($product_id) . "'");
}



  header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer geändert
  header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
  header ("Pragma: no-cache"); // HTTP/1.0
  header ("Content-type: text/xml");


  if (($_POST['action']) && ($_SERVER['REQUEST_METHOD']=='POST'))
  {
    switch ($_POST['action'])
    {

      case 'manufacturers_image_upload':

        if ($manufacturers_image = &xtc_try_upload('manufacturers_image',DIR_FS_CATALOG.DIR_WS_IMAGES,'777', '', true)) {

        echo xtc_db_input($manufacturers_image->filename);

        }

        exit;


      case 'categories_image_upload':

      if ( $categories_image = &xtc_try_upload('categories_image',DIR_FS_CATALOG.DIR_WS_IMAGES.'categories/','777', '', true)) {

        echo xtc_db_input($categories_image->filename);
       }

        exit;

      case 'products_image_upload':

        if ($products_image = &xtc_try_upload('products_image',DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES,'777', '', true)) {

            $products_image_name = $products_image->filename;
        	// rewrite values to use resample classes
            define('DIR_FS_CATALOG_ORIGINAL_IMAGES',DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES);
            define('DIR_FS_CATALOG_INFO_IMAGES',DIR_FS_CATALOG.DIR_WS_INFO_IMAGES);
            define('DIR_FS_CATALOG_POPUP_IMAGES',DIR_FS_CATALOG.DIR_WS_POPUP_IMAGES);
            define('DIR_FS_CATALOG_THUMBNAIL_IMAGES',DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES);
            define('DIR_FS_CATALOG_IMAGES',DIR_FS_CATALOG.DIR_WS_IMAGES);

            // generate resampled images
          require(DIR_FS_DOCUMENT_ROOT.'admin/includes/product_thumbnail_images.php');
          require(DIR_FS_DOCUMENT_ROOT.'admin/includes/product_info_images.php');
          require(DIR_FS_DOCUMENT_ROOT.'admin/includes/product_popup_images.php');

            echo $products_image_name;
        }


        exit;

      case 'manufacturers_update':

        $manufacturers_id = xtc_db_prepare_input($_POST['mID']);

        if (isset($manufacturers_id))
        {

	        // Hersteller laden
	        $count_query = xtc_db_query("select manufacturers_id,
                                                manufacturers_name,
                                                manufacturers_image,
                                                date_added,
                                                last_modified from " . TABLE_MANUFACTURERS . "
                                                where manufacturers_id='" . $manufacturers_id . "'");

	        if ($manufacturer = xtc_db_fetch_array($count_query))
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
	        if (isset($_POST['manufacturers_name'])) $manufacturers_name = xtc_db_prepare_input($_POST['manufacturers_name']);
	        if (isset($_POST['manufacturers_image'])) $manufacturers_image = xtc_db_prepare_input($_POST['manufacturers_image']);

	        $sql_data_array = array('manufacturers_id' => $manufacturers_id,
	                                'manufacturers_name' => $manufacturers_name,
	                                'manufacturers_image' => $manufacturers_image);

	        if ($exists==0) // Neuanlage (ID wird von CAO virgegeben !!!)
	        {
	          $mode='APPEND';
	          $insert_sql_data = array('date_added' => 'now()');
	          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

	          xtc_db_perform(TABLE_MANUFACTURERS, $sql_data_array);
	          $products_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
	        }
	        elseif ($exists==1) //Update
	        {
	          $mode='UPDATE';
	          $update_sql_data = array('last_modified' => 'now()');
	          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

	          xtc_db_perform(TABLE_MANUFACTURERS, $sql_data_array, 'update', 'manufacturers_id = \'' . xtc_db_input($manufacturers_id) . '\'');
	        }
                $languages_query = xtc_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
    while ($languages = xtc_db_fetch_array($languages_query)) {
      $languages_array[] = array('id' => $languages['languages_id'],
                                 'name' => $languages['name'],
                                 'code' => $languages['code'],
                                 'image' => $languages['image'],
                                 'directory' => $languages['directory']
                                );
    }
	        $languages = $languages_array;
	        for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
	        {
	          $language_id = $languages[$i]['id'];

	          // Bestehende Daten laden
	          $desc_query = xtc_db_query("select manufacturers_id,languages_id,manufacturers_url,url_clicked,date_last_click from " .
	                                    TABLE_MANUFACTURERS_INFO . " where manufacturers_id='" . $manufacturers_id . "' and languages_id='" . $language_id . "'");
	          if ($desc = xtc_db_fetch_array($desc_query))
	          {
	            $manufacturers_url = $desc['manufacturers_url'];
	            $url_clicked       = $desc['url_clicked'];
	            $date_last_click   = $desc['date_last_click'];
	          }

	          // uebergebene Daten einsetzen
	          if (isset($_POST['manufacturers_url'][$language_id])) $manufacturers_url=xtc_db_prepare_input($_POST['manufacturers_url'][$language_id]);
	          if (isset($_POST['url_clicked'][$language_id]))       $url_clicked=xtc_db_prepare_input($_POST['url_clicked'][$language_id]);
	          if (isset($_POST['date_last_click'][$language_id]))   $date_last_click=xtc_db_prepare_input($_POST['date_last_click'][$language_id]);


	          $sql_data_array = array('manufacturers_url' => $manufacturers_url);

	          if ($exists==0) // Insert
	          {
	            $insert_sql_data = array('manufacturers_id' => $products_id,
	                                     'languages_id' => $language_id);
	            $sql_data_array = /*xtc_*/array_merge($sql_data_array, $insert_sql_data);
	            xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array);
	          }
	          elseif ($exists==1) // Update
	          {
	            xtc_db_perform(TABLE_MANUFACTURERS_INFO, $sql_data_array, 'update', 'manufacturers_id = \'' . xtc_db_input($manufacturers_id) . '\' and languages_id = \'' . $language_id . '\'');
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

        $ManID  = xtc_db_prepare_input($_POST['mID']);

		  if (isset($ManID))
		  {
          // Hersteller loeschen
          xtc_db_query("delete from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$ManID . "'");
          xtc_db_query("delete from " . TABLE_MANUFACTURERS_INFO . " where manufacturers_id = '" . (int)$ManID . "'");
          // Herstellerverweis in den Artikeln loeschen
          xtc_db_query("update " . TABLE_PRODUCTS . " set manufacturers_id = '' where manufacturers_id = '" . (int)$ManID . "'");

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

    $languages_query = xtc_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
    while ($languages = xtc_db_fetch_array($languages_query)) {
      $languages_array[] = array('id' => $languages['languages_id'],
                                 'name' => $languages['name'],
                                 'code' => $languages['code'],
                                 'image' => $languages['image'],
                                 'directory' => $languages['directory']
                                );
    }


        $products_id = xtc_db_prepare_input($_POST['pID']);

        // product laden
        $count_query = xtc_db_query("select products_quantity,
                                     products_model,
                                     products_image,
                                     products_price,
                                     products_date_available,
                                     products_weight,
                                     products_status,
                                     products_tax_class_id,
                                     manufacturers_id from " . TABLE_PRODUCTS . "
                                     where products_id='" . $products_id . "'");

        if ($product = xtc_db_fetch_array($count_query))
        {
           $exists = 1;
           // aktuelle Produktdaten laden
           $products_quantity = $product['products_quantity'];
           $products_model = $product['products_model'];
           $products_image = $product['products_image'];
           $products_price = $product['products_price'];
           $products_date_available = $product['products_date_available'];
           $products_weight = $product['products_weight'];
           $products_status = $product['products_status'];
           $products_tax_class_id = $product['products_tax_class_id'];
           $manufacturers_id = $product['manufacturers_id'];
           if (SWITCH_MWST=='true') {
               // switch IDs
               if ($products_tax_class_id==1) $products_tax_class_id=2;
               if ($products_tax_class_id==2) $products_tax_class_id=1;
           }
        }
        else $exists = 0;

        // Variablen nur ueberschreiben wenn als Parameter vorhanden !!!
        if (isset($_POST['products_quantity'])) $products_quantity = xtc_db_prepare_input($_POST['products_quantity']);
        if (isset($_POST['products_model'])) $products_model = xtc_db_prepare_input($_POST['products_model']);
        if (isset($_POST['products_image'])) $products_image = xtc_db_prepare_input($_POST['products_image']);
        if (isset($_POST['products_price'])) $products_price = xtc_db_prepare_input($_POST['products_price']);
        if (isset($_POST['products_date_available'])) $products_date_available = xtc_db_prepare_input($_POST['products_date_available']);
        if (isset($_POST['products_weight'])) $products_weight = xtc_db_prepare_input($_POST['products_weight']);
        if (isset($_POST['products_status'])) $products_status = xtc_db_prepare_input($_POST['products_status']);

           if (SWITCH_MWST=='true') {
               // switch IDs
               if ($_POST['products_tax_class_id']==1) $_POST['products_tax_class_id']=2;
               if ($_POST['products_tax_class_id']==2) $_POST['products_tax_class_id']=1;
           }

        if (isset($_POST['products_tax_class_id'])) $products_tax_class_id = xtc_db_prepare_input($_POST['products_tax_class_id']);
        if (isset($_POST['manufacturers_id'])) $manufacturers_id = xtc_db_prepare_input($_POST['manufacturers_id']);

        $products_date_available = (date('Y-m-d') < $products_date_available) ? $products_date_available : 'null';



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


        if ($exists==0) // Neuanlage (ID wird an CAO zurueckgegeben !!!)
        {

        // set groupaccees
           $customers_statuses_array = array(array());
       $customers_statuses_query = xtc_db_query("select customers_status_id,
                                               customers_status_name
                                               from " . TABLE_CUSTOMERS_STATUS . "
                                               where language_id = '".$LangID."' order by
                                               customers_status_id");
     $i=1;        // this is changed from 0 to 1 in cs v1.2
     while ($customers_statuses = xtc_db_fetch_array($customers_statuses_query)) {
       $i=$customers_statuses['customers_status_id'];
       $customers_statuses_array[$i] = array('id' => $customers_statuses['customers_status_id'],
                                           'text' => $customers_statuses['customers_status_name']);
     }

		$group_ids='c_all_group,';
        for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
               $group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
        }

          $mode='APPEND';

          $insert_sql_data = array('products_date_added' => 'now()',
                                    'products_shippingtime'=>1,
                                    'group_ids'=>$group_ids);
          $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

          // insert data
          xtc_db_perform(TABLE_PRODUCTS, $sql_data_array);

          $products_id = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);


        }
        elseif ($exists==1) //Update
        {
          $mode='UPDATE';
          $update_sql_data = array('products_last_modified' => 'now()');
          $sql_data_array = array_merge($sql_data_array, $update_sql_data);

          // update data
          xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', 'products_id = \'' . xtc_db_input($products_id) . '\'');
        }

        $languages = $languages_array;
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
        {
          $language_id = $languages[$i]['id'];

          // Bestehende Daten laden
          $desc_query = xtc_db_query("select
                                      products_id,
                                      products_name,
                                      products_description,
                                      products_short_description,
                                      products_url,
                                      products_viewed,
                                      language_id
                                      from " .
                                    TABLE_PRODUCTS_DESCRIPTION . "
                                    where products_id='" . $products_id . "'
                                    and language_id='" . $language_id . "'");

          if ($desc = xtc_db_fetch_array($desc_query))
          {
            $products_name = $desc['products_name'];
            $products_description = $desc['products_description'];
            $products_url = $desc['products_url'];
          }

          // uebergebene Daten einsetzen
          if (isset($_POST['products_name'][$LangID]))        $products_name=xtc_db_prepare_input($_POST['products_name'][$LangID]);
          if (isset($_POST['products_description'][$LangID])) $products_description=xtc_db_prepare_input($_POST['products_description'][$LangID]);
          if (isset($_POST['products_short_description'][$LangID]))    $products_short_description=xtc_db_prepare_input($_POST['products_short_description'][$LangID]);
          if (isset($_POST['products_url'][$LangID]))         $products_url=xtc_db_prepare_input($_POST['products_url'][$LangID]);


          $sql_data_array = array('products_name' => $products_name,
                                  'products_description' => $products_description,
                                  'products_short_description' => $products_short_description,
                                  'products_url' => $products_url);

          if ($exists==0) // Insert
          {
            $insert_sql_data = array('products_id' => $products_id,
                                     'language_id' => $language_id);

            // get customers groups

            xtc_db_query("INSERT INTO ".TABLE_PRODUCTS_DESCRIPTION."
                          (products_name,
                          products_description,
                          products_short_description,
                          products_url,
                          products_id,
                          language_id) VALUES
                          ('".$products_name."',
                          '". nl2br($products_description)."',
                          '".$products_short_description."',
                          '".$products_url."',
                          '".$products_id."',
                          '".$language_id."')");
          }
          elseif ($exists==1) // Update
          {
            xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', 'products_id = \'' . xtc_db_input($products_id) . '\' and language_id = \'' . $language_id . '\'');
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

        $ProdID  = xtc_db_prepare_input($_POST['prodid']);

		  if (isset($ProdID))
		  {

           // ProductsToCategieries loeschen bei denen die products_id = ... ist
           $res1 = xtc_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id='" . $ProdID . "'");

           // Product loeschen
           xtc_remove_product($ProdID);

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


                      $languages_query = xtc_db_query("select languages_id, name, code, image, directory from " . TABLE_LANGUAGES . " order by sort_order");
                                 while ($languages = xtc_db_fetch_array($languages_query)) {
                                         $languages_array[] = array('id' => $languages['languages_id'],
                                                              'name' => $languages['name'],
                                                               'code' => $languages['code'],
                                                               'image' => $languages['image'],
                                                               'directory' => $languages['directory']
                                                               );
                                                                }

		  $CatID    = xtc_db_prepare_input($_POST['catid']);
		  $ParentID = xtc_db_prepare_input($_POST['parentid']);
		  $Sort     = xtc_db_prepare_input($_POST['sort']);
		  $Image    = xtc_db_prepare_input($_POST['image']);
		  $Name     = xtc_db_prepare_input(UrlDecode($_POST['name']));


		  if (isset($ParentID) && isset($CatID))
		  {

           // Testen ob Eintrag existiert
           $count_query = xtc_db_query("select COUNT(*) as ANZ from " . TABLE_CATEGORIES . " where categories_id='" . $CatID . "'");
           if (xtc_db_num_rows($count_query))
           {
              $count = xtc_db_fetch_array($count_query);

              $exists = $count['ANZ'];
           }
           else $exists = 0;

           if ($exists==1)
           {
             $mode='UPDATE';

             $values  = "parent_id='" . $ParentID . "', last_modified=now()";

             if (isset($Sort)) $values .= ", sort_order='" . $Sort . "'";
             if (isset($Image)) $values .= ", categories_image='" . $Image . "'";

             $res1 = xtc_db_query("update " . TABLE_CATEGORIES . " SET " . $values . " where categories_id='" . $CatID . "'");
           }
             else
           {

                   // set groupaccees
           $customers_statuses_array = array(array());
           $customers_statuses_query = xtc_db_query("select customers_status_id,
                                               customers_status_name
                                               from " . TABLE_CUSTOMERS_STATUS . "
                                               where language_id = '".$LangID."' order by
                                               customers_status_id");
          $i=1;        // this is changed from 0 to 1 in cs v1.2
           while ($customers_statuses = xtc_db_fetch_array($customers_statuses_query)) {
           $i=$customers_statuses['customers_status_id'];
            $customers_statuses_array[$i] = array('id' => $customers_statuses['customers_status_id'],
                                           'text' => $customers_statuses['customers_status_name']);
           }

        	$group_ids='c_all_group,';
            for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
               $group_ids .='c_'.$customers_statuses_array[$i]['id'].'_group,';
            }

             $mode='APPEND';

             if (!isset($Sort)) $Sort=0;

             $felder  = "(categories_id, parent_id, date_added, sort_order,group_ids";
             if (isset($Image)) $felder .= ", categories_image";
             $felder .= ")";

             $values  = "Values(" . "'" . $CatID . "', '" . $ParentID . "', now(), '" . $Sort . "','" . $group_ids . "'";
             if (isset($Image)) $values .= ", '" . $Image . "'";
             $values .= ")";

             $res1 = xtc_db_query("insert into " . TABLE_CATEGORIES . " " . $felder . $values);



           }

           // Namen setzen
           if (isset($Name))
           {
           // added multilang support for categories

            $languages = $languages_array;

            for ($i = 0, $n = sizeof($languages); $i < $n; $i++)
                {
                         $language_id = $languages[$i]['id'];
                         $res2 = xtc_db_query("replace into " . TABLE_CATEGORIES_DESCRIPTION . " (categories_id, language_id, categories_name) Values ('" . $CatID ."', '" . $language_id . "', '" . $Name . "')");
               }
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

		  $CatID  = xtc_db_prepare_input($_POST['catid']);

		  if (isset($CatID))
		  {

           // Categorie loeschen
           $res1 = xtc_db_query("delete from " . TABLE_CATEGORIES . " where categories_id='" . $CatID . "'");

           // ProductsToCategieries loeschen bei denen die Categorie = ... ist
           $res2 = xtc_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id='" . $CatID . "'");

           // CategieriesDescription loeschenm bei denen die Categorie = ... ist
           $res3 = xtc_db_query("delete from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id='" . $CatID . "'");

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

		  $ProdID = xtc_db_prepare_input($_POST['prodid']);
		  $CatID  = xtc_db_prepare_input($_POST['catid']);

		  if (isset($ProdID) && isset($CatID))
		  {

           $res = xtc_db_query("replace into " . TABLE_PRODUCTS_TO_CATEGORIES . " (products_id, categories_id) Values ('" . $ProdID ."', '" . $CatID . "')");

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

		  $ProdID = xtc_db_prepare_input($_POST['prodid']);
		  $CatID  = xtc_db_prepare_input($_POST['catid']);

		  if (isset($ProdID) && isset($CatID))
		  {

           $res = xtc_db_query("delete from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id='" . $ProdID ."' and categories_id='" . $CatID . "'");

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
          $comments = xtc_db_prepare_input($_POST['comments']);



          //Status überprüfen
          $check_status_query = xtc_db_query("select * from " . TABLE_ORDERS . " where orders_id = '" . xtc_db_input($oID) . "'");
          if ($check_status = xtc_db_fetch_array($check_status_query))
          {
            if ($check_status['orders_status'] != $status || $comments != '')
            {
              xtc_db_query("update " . TABLE_ORDERS . " set orders_status = '" . xtc_db_input($status) . "', last_modified = now() where orders_id = '" . xtc_db_input($oID) . "'");
              $customer_notified = '0';
              if ($_POST['notify'] == 'on')
              {
                // Falls eine Sprach ID zur Order existiert die Emailbestätigung in dieser Sprache ausführen
                if (isset($check_status['orders_language_id']) && $check_status['orders_language_id'] > 0 ) {
                  $orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $check_status['orders_language_id'] . "'");
                  if (xtc_db_num_rows($orders_status_query) == 0) {
                    $orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
                  }
                } else {
                  $orders_status_query = xtc_db_query("select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $languages_id . "'");
                }

                $orders_statuses = array();
                $orders_status_array = array();
                while ($orders_status = xtc_db_fetch_array($orders_status_query)) {
                  $orders_statuses[] = array('id' => $orders_status['orders_status_id'],
                                             'text' => $orders_status['orders_status_name']);
                  $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
                }
                // status query
                $orders_status_query = xtc_db_query("select orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = '" . $LangID . "' and orders_status_id='".$status."'");
                $o_status=xtc_db_fetch_array($orders_status_query);
                $o_status=$o_status['orders_status_name'];

                //ok lets generate the html/txt mail from Template
                if ($_POST['notify_comments'] == 'on')
                {
                  $notify_comments = sprintf(EMAIL_TEXT_COMMENTS_UPDATE, $comments) . "\n\n";
                } else {
                $comments='';
                }

                // require functionblock for mails
                require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
                require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
                require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
                require_once(DIR_FS_INC . 'changedataout.inc.php');
                require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
                require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
                require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
                $smarty = new Smarty;

                $smarty->assign('language', $check_status['language']);
                $smarty->caching = false;


                $smarty->template_dir=DIR_FS_CATALOG.'templates';
                $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
                $smarty->config_dir=DIR_FS_CATALOG.'lang';


                $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
                $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');


                $smarty->assign('NAME',$check_status['customers_name']);
                $smarty->assign('ORDER_NR',$oID);
                $smarty->assign('ORDER_LINK',xtc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $oID, 'SSL'));
                $smarty->assign('ORDER_DATE',xtc_date_long($check_status['date_purchased']));
                $smarty->assign('NOTIFY_COMMENTS',$comments);
                $smarty->assign('ORDER_STATUS',$o_status);

				if(defined('EMAIL_SIGNATURE')) {
					$smarty->assign('EMAIL_SIGNATURE_HTML', nl2br(EMAIL_SIGNATURE));
					$smarty->assign('EMAIL_SIGNATURE_TEXT', EMAIL_SIGNATURE);
				}

                $html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$check_status['language'].'/change_order_mail.html');
                $txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$check_status['language'].'/change_order_mail.txt');

                // send mail with html/txt template
                  xtc_php_mail(EMAIL_BILLING_ADDRESS,
                             EMAIL_BILLING_NAME ,
                             $check_status['customers_email_address'],
                             $check_status['customers_name'],
                             '',
                             EMAIL_BILLING_REPLY_ADDRESS,
                             EMAIL_BILLING_REPLY_ADDRESS_NAME,
                             '',
                             '',
                             EMAIL_BILLING_SUBJECT,
                             $html_mail ,
                             $txt_mail);

                $customer_notified = '1';
              }


              xtc_db_query("insert into " . TABLE_ORDERS_STATUS_HISTORY . " (orders_id, orders_status_id, date_added, customer_notified, comments) values ('" . xtc_db_input($oID) . "', '" . xtc_db_input($status) . "', now(), '" . $customer_notified . "', '" . xtc_db_input($comments)  . "')");
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


    //--  Raphael Vullriede
    //-- add actions for customers
    case 'customers_update':
        $customers_id = -1;
        // include PW function
        require_once(DIR_FS_INC . 'xtc_encrypt_password.inc.php');

        if (isset($_POST['cID'])) $customers_id = xtc_db_prepare_input($_POST['cID']);

        // security check, if user = admin, dont allow to perform changes
        if ($customers_id!=-1) {
        $sec_query=xtc_db_query("SELECT customers_status FROM ".TABLE_CUSTOMERS." where customers_id='".$customers_id."'");
        $sec_data=xtc_db_fetch_array($sec_query);
        if ($sec_data['customers_status']==0) {

        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                    '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
                    '<CODE>' . '0' . '</CODE>' . "\n" .
                    '<MESSAGE>' . 'CANT CHANGE ADMIN USER!' . '</MESSAGE>' . "\n" .
                    '<MODE>' . $mode . '</MODE>' . "\n" .
                    '<CUSTOMERS_ID>' . $customers_id . '</CUSTOMERS_ID>' . "\n" .
                '</STATUS_DATA>' . "\n" .
            '</STATUS>' . "\n\n";

        echo $schema;
        exit;

        }
        }
        $sql_customers_data_array = array();
        if (isset($_POST['customers_name3'])) $sql_customers_data_array['customers_firstname'] = $_POST['customers_name3'];
        if (isset($_POST['customers_name2'])) $sql_customers_data_array['customers_lastname'] = $_POST['customers_name2'];
        if (isset($_POST['customers_dob'])) $sql_customers_data_array['customers_dob'] = $_POST['customers_dob'];
        if (isset($_POST['customers_email'])) $sql_customers_data_array['customers_email_address'] = $_POST['customers_email'];
        if (isset($_POST['customers_tele'])) $sql_customers_data_array['customers_telephone'] = $_POST['customers_tele'];
        if (isset($_POST['customers_fax'])) $sql_customers_data_array['customers_fax'] = $_POST['customers_fax'];
        if (isset($_POST['customers_password'])) {
        $sql_customers_data_array['customers_password'] = xtc_encrypt_password($_POST['customers_password']);
        } else {
        // generate PW if empty


         $pw=xtc_RandomString(8);
         $sql_customers_data_array['customers_password']=xtc_create_password($pw);

        }

        $sql_address_data_array =array();
        if (isset($_POST['customers_name3'])) $sql_address_data_array['entry_firstname'] = $_POST['customers_name3'];
        if (isset($_POST['customers_name2'])) $sql_address_data_array['entry_lastname'] = $_POST['customers_name2'];
        if (isset($_POST['customers_name1'])) $sql_address_data_array['entry_company'] = $_POST['customers_name1'];
        if (isset($_POST['customers_street'])) $sql_address_data_array['entry_street_address'] = $_POST['customers_street'];
        if (isset($_POST['customers_city'])) $sql_address_data_array['entry_city'] = $_POST['customers_city'];
        if (isset($_POST['customers_postcode'])) $sql_address_data_array['entry_postcode'] = $_POST['customers_postcode'];


        if (isset($_POST['customers_country_id'])) $country_code = $_POST['customers_country_id'];
        $country_query = "SELECT countries_id FROM ".TABLE_COUNTRIES." WHERE countries_iso_code_2 = '".$country_code ."' LIMIT 1";
        $country_result = xtc_db_query($country_query);
        $row = xtc_db_fetch_array($country_result);
        $sql_address_data_array['entry_country_id'] = $row['countries_id'];


        $count_query = xtc_db_query("SELECT count(*) as count FROM " . TABLE_CUSTOMERS . " WHERE customers_id='" . (int)$customers_id . "' LIMIT 1");
        $check = xtc_db_fetch_array($count_query);

        if ($check['count'] > 0) {
            $mode = 'UPDATE';
            $address_book_result = xtc_db_query("SELECT customers_default_address_id FROM ".TABLE_CUSTOMERS." WHERE customers_id = '". (int)$customers_id ."' LIMIT 1");
            $customer = xtc_db_fetch_array($address_book_result);
            xtc_db_perform(TABLE_CUSTOMERS, $sql_customers_data_array, 'update', "customers_id = '" . xtc_db_input($customers_id) . "' LIMIT 1");
            xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_address_data_array, 'update', "customers_id = '" . xtc_db_input($customers_id) . "' AND address_book_id = '".$customer['customers_default_address_id']."' LIMIT 1");
            xtc_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_account_last_modified = now() where customers_info_id = '" . (int)$customers_id . "'  LIMIT 1");

        }  else {
            $mode= 'APPEND';

            xtc_db_perform(TABLE_CUSTOMERS, $sql_customers_data_array);
            $customers_id = xtc_db_insert_id();
            $sql_address_data_array['customers_id'] = $customers_id;
            xtc_db_perform(TABLE_ADDRESS_BOOK, $sql_address_data_array);
            $address_id = xtc_db_insert_id();
            xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . (int)$address_id . "' where customers_id = '" . (int)$customers_id . "'");
            xtc_db_query("update " . TABLE_CUSTOMERS . " set customers_status = '" . STANDARD_GROUP . "' where customers_id = '" . (int)$customers_id . "'");
            xtc_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created) values ('" . (int)$customers_id . "', '0', now())");

        }

         if (SEND_ACCOUNT_MAIL==true && $mode=='APPEND' && $sql_customers_data_array['customers_email_address']!='') {
                 // generate mail for customer if customer=new
                require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
                require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
                require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
                require_once(DIR_FS_INC . 'changedataout.inc.php');
                require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
                require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
                require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
                $smarty = new Smarty;

                $smarty->assign('language', $check_status['language']);
                $smarty->caching = false;


                $smarty->template_dir=DIR_FS_CATALOG.'templates';
                $smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
                $smarty->config_dir=DIR_FS_CATALOG.'lang';


                $smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
                $smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');



                  $smarty->assign('NAME',$sql_customers_data_array['customers_lastname'] . ' ' . $sql_customers_data_array['customers_firstname']);
                $smarty->assign('EMAIL',$sql_customers_data_array['customers_email_address']);

                $smarty->assign('PASSWORD',$pw);

                $smarty->assign('language', $Lang_folder);
                $smarty->assign('content', $module_content);
                $smarty->caching = false;

                $html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Lang_folder.'/create_account_mail.html');
                $txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Lang_folder.'/create_account_mail.txt');

                // send mail with html/txt template
                  xtc_php_mail(EMAIL_SUPPORT_ADDRESS,
                             EMAIL_SUPPORT_NAME ,
                             $sql_customers_data_array['customers_email_address'],
                             $sql_customers_data_array['customers_lastname'] . ' ' . $sql_customers_data_array['customers_firstname'],
                             '',
                             EMAIL_SUPPORT_REPLY_ADDRESS,
                             EMAIL_SUPPORT_REPLY_ADDRESS_NAME,
                             '',
                             '',
                             EMAIL_SUPPORT_SUBJECT,
                             $html_mail ,
                             $txt_mail);
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
        $cID  = xtc_db_prepare_input($_POST['cID']);


        $sec_query=xtc_db_query("SELECT customers_status FROM ".TABLE_CUSTOMERS." where customers_id='".$cID."'");
        $sec_data=xtc_db_fetch_array($sec_query);
        if ($sec_data['customers_status']==0) {

        $schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
            '<STATUS>' . "\n" .
                '<STATUS_DATA>' . "\n" .
                    '<ACTION>' . $_POST['action'] . '</ACTION>' . "\n" .
                    '<CODE>' . '0' . '</CODE>' . "\n" .
                    '<MESSAGE>' . 'CANT CHANGE ADMIN USER!' . '</MESSAGE>' . "\n" .
                    '<MODE>' . $mode . '</MODE>' . "\n" .
                    '<CUSTOMERS_ID>' . $cID . '</CUSTOMERS_ID>' . "\n" .
                '</STATUS_DATA>' . "\n" .
            '</STATUS>' . "\n\n";

        echo $schema;
        exit;

        }



        if (isset($cID)) {
            xtc_db_query("update " . TABLE_REVIEWS . " set customers_id = null where customers_id = '" .  $cID . "'");
            xtc_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . $cID . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" .$cID . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . $cID. "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . $cID . "'");
            xtc_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . $cID . "'");
            xtc_db_query("delete from " . TABLE_WHOS_ONLINE . " where customer_id = '" . $cID . "'");

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

    if (($_GET['action']) && ($_SERVER['REQUEST_METHOD']=='GET') && ($_GET['action']))
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

    }


  }
