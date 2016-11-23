<?php
/* --------------------------------------------------------------
   page_track.php 2010-11-16 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class PageTrack
{
  /*
   * history stack for URLs
   */
  var $v_history_array = array();


  /*
   * constructor
   */
  function PageTrack()
  {
    $this->reset();
  }

  function &get_instance()
  {
	  static $s_instance;

	  if($s_instance === NULL)   {
		  $s_instance = new PageTrack();
	  }
	  return $s_instance;
  }


  /*
   * add new url to stack
   * @param string $p_url  new url to be added to history stack
   * @return bool true:ok | false:error
   */
  function add_page($p_url)
  {
//    if (empty($p_url) || !preg_match("/^((https?|ftp)\:\/\/)?$/", $url)) {
//      return false;
//    }
    array_push($this->v_history_array, $p_url);
    return true;
  }

  /*
   * get current url from history stack
   * @return string  url
   */
  function get_current_page()
  {
    return end($this->v_history_array);
  }

  /*
   * get last url from history stack
   * @return string  url
   */
  function get_prev_page()
  {
    end($this->v_history_array);
    return prev($this->v_history_array);
  }

  /*
   * remove latest url from history stack
   * @return bool true
   */
  function move_back()
  {
    array_pop($this->v_history_array);
    return true;
  }

  /*
   * empty url history stack
   * @return bool true
   */
  function reset()
  {
    $this->v_history_array = array();
    return true;
  }
}
?>