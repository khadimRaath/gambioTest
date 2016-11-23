<?php
/*
   --------------------------------------------------------------
   object_info.php 2014-07-17 gm
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
   $Id: object_info.php 950 2005-05-14 16:45:21Z mz $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(object_info.php,v 1.5 2002/01/30); www.oscommerce.com 
   (c) 2003	 nextcommerce (object_info.php,v 1.5 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );
  class objectInfo_ORIGIN {

    // class constructor
    function __construct($object_array) {
      reset($object_array);
      while (list($key, $value) = each($object_array)) {
        $this->$key = xtc_db_prepare_input($value);
      }
    }
  }

MainFactory::load_origin_class('objectInfo');
