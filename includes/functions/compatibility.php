<?php
/* --------------------------------------------------------------
   compatibility.php 2012-03-30 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(default.php,v 1.84 2003/05/07); www.oscommerce.com
   (c) 2003	 nextcommerce (default.php,v 1.13 2003/08/17); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: compatibility.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   Modified by Marco Canini, <m.canini@libero.it>
   Fixed a bug with arrays in $HTTP_xxx_VARS
   ---------------------------------------------------------------------------------------*/

  if (!function_exists('http_build_query')) {
    function http_build_query($formdata, $numeric_prefix = '', $arg_separator = null)
    {
        if(!is_array($formdata) && !is_object($formdata))
            return false;
                elseif(empty($formdata))
                    return '';

        if(is_null($arg_separator))
        {
            $arg_separator = @ini_get('arg_separator.output');

            if(!$arg_separator)
                $arg_separator = '&';
        }

        $builderCore = '$ret = \'\';

        foreach ($array as $k => $v)
        {
            if (is_scalar($v))
                $ret .= $name.urlencode(\'[\'.$k.\']\').\'=\'.urlencode($v).$sep;
            else
            {
                $builder = create_function(\' $name, $array, $sep, $builderCore\', $builderCore);
                $ret .= $builder( $name.urlencode(\'[\'.$k.\']\'), $v, $sep, $builderCore).$sep;
            }
        }
        return substr( $ret, 0, -strlen($sep));';

        $builder = create_function(' $name, $array, $sep, $builderCore', $builderCore);
        $ret = '';

        foreach($formdata as $k => $v)
        {
            if (is_integer($k))
                $k = $numeric_prefix.$k;

            if(is_scalar($v))
            {
                $ret .= urlencode($k).'='.urlencode($v).$arg_separator;
                continue;
            }

            $ret .= $builder( urlencode($k), $v, $arg_separator, $builderCore).$arg_separator;
        }
        return substr( $ret, 0, -strlen($arg_separator));
    }
  }

  ////
  // Recursively handle magic_quotes_gpc turned off.
  // This is due to the possibility of have an array in
  // $HTTP_xxx_VARS
  // Ie, products attributes
  function do_magic_quotes_gpc(&$ar) {
    if (!is_array($ar)) return false;

    while (list($key, $value) = each($ar)) {
      if (is_array($value)) {
        do_magic_quotes_gpc($value);
      } else {
        $ar[$key] = addslashes($value);
      }
    }
  }

  // $HTTP_xxx_VARS are always set on php4
  if (!is_array($_GET)) $_GET = array();
  if (!is_array($_POST)) $_POST = array();
  if (!is_array($_COOKIE)) $_COOKIE = array();

  // handle magic_quotes_gpc turned off.
  if (!get_magic_quotes_gpc()) {
    do_magic_quotes_gpc($_GET);
    do_magic_quotes_gpc($_POST);
    do_magic_quotes_gpc($_COOKIE);
  }

  if (!function_exists('array_splice')) {
    function array_splice(&$array, $maximum) {
      if (sizeof($array) >= $maximum) {
        for ($i=0; $i<$maximum; $i++) {
          $new_array[$i] = $array[$i];
        }
        $array = $new_array;
      }
    }
  }

  if (!function_exists('in_array')) {
    function in_array($lookup_value, $lookup_array) {
      reset($lookup_array);
      while (list($key, $value) = each($lookup_array)) {
        if ($value == $lookup_value) return true;
      }

      return false;
    }
  }

  if (!function_exists('array_reverse')) {
    function array_reverse($array) {
      for ($i=0, $n=sizeof($array); $i<$n; $i++) $array_reversed[$i] = $array[($n-$i-1)];

      return $array_reversed;
    }
  }

  if (!function_exists('constant')) {
    function constant($constant) {
      eval("\$temp=$constant;");

      return $temp;
    }
  }

  if (!function_exists('is_null')) {
    function is_null($value) {
      if (is_array($value)) {
        if (sizeof($value) > 0) {
          return false;
        } else {
          return true;
        }
      } else {
        if (($value != '') && ($value != 'NULL') && (strlen(trim($value)) > 0)) {
          return false;
        } else {
          return true;
        }
      }
    }
  }

  if (!function_exists('array_merge')) {
    function array_merge($array1, $array2, $array3 = '') {
      if (empty($array3) && !is_array($array3)) $array3 = array();
      while (list($key, $val) = each($array1)) $array_merged[$key] = $val;
      while (list($key, $val) = each($array2)) $array_merged[$key] = $val;
      if (sizeof($array3) > 0) while (list($key, $val) = each($array3)) $array_merged[$key] = $val;

      return (array) $array_merged;
    }
  }

  if (!function_exists('is_numeric')) {
    function is_numeric($param) {
      return preg_match('/^[0-9]{1,50}.?[0-9]{0,50}$/', $param);
    }
  }

  if (!function_exists('array_slice')) {
    function array_slice($array, $offset, $length = 0) {
      if ($offset < 0 ) {
        $offset = sizeof($array) + $offset;
      }
      $length = ((!$length) ? sizeof($array) : (($length < 0) ? sizeof($array) - $length : $length + $offset));
      for ($i = $offset; $i<$length; $i++) {
        $tmp[] = $array[$i];
      }

      return $tmp;
    }
  }

  if (!function_exists('array_map')) {
    function array_map($callback, $array) {
      if (is_array($array)) {
        $_new_array = array();
        reset($array);
        while (list($key, $value) = each($array)) {
          $_new_array[$key] = array_map($callback, $array[$key]);
        }
        return $_new_array;
      } else {
        return $callback($array);
      }
    }
  }

  if (!function_exists('str_repeat')) {
    function str_repeat($string, $number) {
      $repeat = '';

      for ($i=0; $i<$number; $i++) {
        $repeat .= $string;
      }

      return $repeat;
    }
  }

  if (!function_exists('checkdnsrr')) {
    function checkdnsrr($host, $type) {
      if(xtc_not_null($host) && xtc_not_null($type)) {
        @exec("nslookup -type=$type $host", $output);
        while(list($k, $line) = each($output)) {
          if(preg_match("/^$host/i", $line)) {
            return true;
          }
        }
      }
      return false;
    }
  }

# In PHP 5.2 or higher we don't need to bring this in
if(!function_exists('json_encode'))
{
	require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

	function json_encode($p_arg)
	{
		$coo_services_json = new Services_JSON();

		return $coo_services_json->encodeUnsafe($p_arg);
	}
}

# In PHP 5.2 or higher we don't need to bring this in
if(!function_exists('json_decode'))
{
	require_once(DIR_FS_CATALOG . 'gm/classes/JSON.php');

	function json_decode($p_arg, $p_as_array = false)
	{
		$coo_services_json = new Services_JSON($p_as_array ? SERVICES_JSON_LOOSE_TYPE : 0);

		return $coo_services_json->decode($p_arg);
	}
}
?>