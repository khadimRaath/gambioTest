<?php
/*
   --------------------------------------------------------------
   upload.php 2014-07-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.		
   --------------------------------------------------------------

  --------------------------------------------------------------
   $Id: upload.php 950 2005-05-14 16:45:21Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(upload.php,v 1.1 2003/03/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (upload.php,v 1.7 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
  class upload_ORIGIN {
    var $file, $filename, $destination, $permissions, $extensions, $tmp_filename;

    function __construct($file = '', $destination = '', $permissions = '777', $extensions = '') {

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
      } else {
        $file = array('name' => $GLOBALS[$this->file . '_name'],
                      'type' => $GLOBALS[$this->file . '_type'],
                      'size' => $GLOBALS[$this->file . '_size'],
                      'tmp_name' => $GLOBALS[$this->file]);
      }

      if ( xtc_not_null($file['tmp_name']) && ($file['tmp_name'] != 'none') && (is_uploaded_file($file['tmp_name']) || defined( 'SUPPRESS_UPLOAD_CHECKS' ) ) ) {
        if (sizeof($this->extensions) > 0) {
          if (!in_array(strtolower(substr($file['name'], strrpos($file['name'], '.')+1)), $this->extensions)) {
            $messageStack->add_session(ERROR_FILETYPE_NOT_ALLOWED, 'error');

            return false;
          }
        }

        $this->set_file($file);
        $this->set_filename($file['name']);
        $this->set_tmp_filename($file['tmp_name']);

        return $this->check_destination();
      } else {

        if ($file['tmp_name']=='none') $messageStack->add_session(WARNING_NO_FILE_UPLOADED, 'warning');

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

                $messageStack->add_session(ERROR_GIF_MERGE, 'error');
                return false;

            }
            // check if uploaded image = .gif
            if (strstr($this->filename,'.gif')) {
             $messageStack->add_session(ERROR_GIF_UPLOAD, 'error');
             return false;
            }

        }

      }


      if ( defined( 'SUPPRESS_UPLOAD_CHECKS' ) && rename($this->file['tmp_name'], $this->destination . $this->filename)) {
        chmod($this->destination . $this->filename, $this->permissions);

        $messageStack->add_session(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');

        return true;
      } elseif (move_uploaded_file($this->file['tmp_name'], $this->destination . $this->filename)) {
        chmod($this->destination . $this->filename, $this->permissions);

        $messageStack->add_session(SUCCESS_FILE_SAVED_SUCCESSFULLY, 'success');

        return true;
      } else {
        $messageStack->add_session(ERROR_FILE_NOT_SAVED, 'error');

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
          $messageStack->add_session(sprintf(ERROR_DESTINATION_NOT_WRITEABLE, $this->destination), 'error');
        } else {
          $messageStack->add_session(sprintf(ERROR_DESTINATION_DOES_NOT_EXIST, $this->destination), 'error');
        }

        return false;
      } else {
        return true;
      }
    }

  }

MainFactory::load_origin_class('upload');
