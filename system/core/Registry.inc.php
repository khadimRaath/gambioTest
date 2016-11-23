<?php
/* --------------------------------------------------------------
   Registry.inc.php 2010-11-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class Registry
{
  /*
   * holding class-file-data (key[classname]=>value[path])
   */
  var $v_values_array = array();


  /*
   * constructor
   */
  function Registry()
  {

  }

  /*
   * set new entry for class name (key) with path (value)
   * @param string $p_name  class name
   * @param string $p_value  path
   * @return bool true:ok | false:error
   */
  function set($p_name, $p_value)
  {
    $this->v_values_array[$p_name] = $p_value;
    return true;
  }

  /*
   * get path (value) for given name (key)
   * @param string $p_name  class name
   * @return mixed false:error | string:path for class
   */
  function get($p_name)
  {
    if (!empty($this->v_values_array[$p_name])) {
      return $this->v_values_array[$p_name];
    }
    return NULL;
  }

  /*
   * get the whole array with name and path informations
   * @return array  all data from path scan
   */
  function get_all_data()
  {
    return $this->v_values_array;
  }
}
?>