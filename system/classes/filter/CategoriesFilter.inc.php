<?php
/* --------------------------------------------------------------
   CategoriesFilter 2010-12-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2010 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/


class CategoriesFilter
{
  /*
   * needed class vars
   */
  var $v_categories_id = 0;
  var $v_feature_id = 0;
  var $v_sort_order = 0;
  var $v_selection_preview_mode = '';
  var $v_selection_template = '';
  var $v_value_conjunction = true;


  /*
   * constructor
   */
  function CategoriesFilter()
  {

  }


  /*
   * load_data_object
   * @param object $p_coo_data_object  GmDataObject
   * @return true:ok | false:error
   */
  function load_data_object($p_coo_data_object)
  {
    # basic data
    $t_cat_id  = $p_coo_data_object->get_data_value('categories_id');
    $t_feat_id = $p_coo_data_object->get_data_value('feature_id');
    $this->set_categories_id($t_cat_id);
    $this->set_feature_id($t_feat_id);

    $t_keys_array = array('categories_id' => $t_cat_id,
                          'feature_id' => $t_feat_id,
                          );

    # categories filter
    $coo_data_object_group = MainFactory::create_object('GMDataObjectGroup', array('categories_filter', $t_keys_array));
    $t_data_object_array = $coo_data_object_group->get_data_objects_array();

    foreach($t_data_object_array as $t_data_object_item) {
      $this->set_categories_id( $t_data_object_item->get_data_value('categories_id') );
      $this->set_feature_id( $t_data_object_item->get_data_value('feature_id') );
      $this->set_sort_order( $t_data_object_item->get_data_value('sort_order') );
      $this->set_selection_preview_mode( $t_data_object_item->get_data_value('selection_preview_mode') );
      $this->set_selection_template( $t_data_object_item->get_data_value('selection_template') );
      $this->set_value_conjunction( $t_data_object_item->get_data_value('value_conjunction') );
    }

    $coo_data_object_group = NULL;

    return true;
  }

  /*
   * set_value_conjunction
   * @param bool $p_status  true:use cat_filter with AND | false:use cat_filter with OR
   * @return bool
   */
  function set_value_conjunction($p_status)
  {
    $this->v_value_conjunction = (bool) $p_status;
    return true;
  }

  /*
   * get_value_conjunction
   * @return bool  conjunction value true:AND | false:OR
   */
  function get_value_conjunction()
  {
    return (bool) $this->v_value_conjunction;
  }

  /*
   * set_sort_order
   * @param int $p_sort_order  sort order
   * @return bool
   */
  function set_sort_order($p_sort_order)
  {
    $this->v_sort_order = (int) $p_sort_order;
    return true;
  }

  /*
   * get_sort_order
   * @return int  sort order value
   */
  function get_sort_order()
  {
    return (int) $this->v_sort_order;
  }

  /*
   * set_categories_id
   * @param int $p_categories_id  the given categories id
   * @return bool
   */
  function set_categories_id($p_categories_id)
  {
    $this->v_categories_id = (int) $p_categories_id;
    return true;
  }

  /*
   * get_categories_id
   * @return int  categories id
   */
  function get_categories_id()
  {
    return (int) $this->v_categories_id;
  }

  /*
   * set_feature_id
   * @param int $p_feature_id  the given feature id
   * @return bool
   */
  function set_feature_id($p_feature_id)
  {
    $this->v_feature_id = (int) $p_feature_id;
    return true;
  }

  /*
   * get_feature_id
   * @return int  feature id
   */
  function get_feature_id()
  {
    return (int) $this->v_feature_id;
  }

  /*
   * set_selection_preview_mode
   * @param string $p_mode  the selection preview mode
   * @return bool
   */
  function set_selection_preview_mode($p_mode)
  {
    $this->v_selection_preview_mode = (string) $p_mode;
    return true;
  }

  /*
   * get_selection_preview_mode
   * @return string  selection preview mode
   */
  function get_selection_preview_mode()
  {
    return (string) $this->v_selection_preview_mode;
  }

  /*
   * set_selection_template
   * @param string $p_template  the selection template
   * @return bool
   */
  function set_selection_template($p_template)
  {
    $this->v_selection_template = (string) $p_template;
    return true;
  }

  /*
   * get_selection_template
   * @return string  selection template
   */
  function get_selection_template()
  {
    return (string) $this->v_selection_template;
  }

  /*
   * get_feature_name
   * @param int $p_language_id  actual shop language id
   * @return string  feature name
   */
  function get_feature_name($p_language_id)
  {
    $c_language_id = (int) $p_language_id;

    $t_coo_feature_control = MainFactory::create_object('FeatureControl');

    $t_search_array        = array('feature_id' => $this->v_feature_id);
    $t_feature_array       = $t_coo_feature_control->get_feature_array($t_search_array);

    $t_feature_name        = '';

    foreach ($t_feature_array as $t_key => $t_coo_feature) {
      $t_feature_name = '';
      if (isset($t_coo_feature->v_feature_name_array[$c_language_id])) {
        $t_feature_name = $t_coo_feature->v_feature_name_array[$c_language_id];
      }
    }

    return $t_feature_name;
  }

  /*
   * save
   * @parm bool $p_force_insert  true:INSERT | false:INSERT/UPDATE depending on KEYs
   * @return bool
   */
  function save($p_force_insert = false)
  {
    # insert mode?
    $t_insert_mode = true;
    if ((isset($this->v_categories_id) || !empty($this->v_categories_id)) && !empty($this->v_feature_id)) $t_insert_mode = false;
    if ($p_force_insert) $t_insert_mode = true;

    $coo_feature = MainFactory::create_object('GMDataObject', array('categories_filter'));

    if($t_insert_mode) {
			$coo_feature->set_keys(array('categories_id' => false,
                                   'feature_id' => false));
    } else {
		$coo_feature->set_keys(array('categories_id' => $this->v_categories_id,
                                   'feature_id' => $this->v_feature_id));
		}

    # save data
    $coo_feature->set_data_value('categories_id', $this->v_categories_id);
    $coo_feature->set_data_value('feature_id', $this->v_feature_id);
    $coo_feature->set_data_value('sort_order', $this->v_sort_order);
    $coo_feature->set_data_value('selection_preview_mode', $this->v_selection_preview_mode);
    $coo_feature->set_data_value('selection_template', $this->v_selection_template);
    $coo_feature->set_data_value('value_conjunction', (int) $this->v_value_conjunction);

    $t_feature_id = (int) $coo_feature->save_body_data();

    $coo_feature = NULL;

    return true;
  }

  /*
   * load
   * @param int $p_categories_id  cat-id
   * @param int $p_feature_id  feature-id
   * @return bool true:ok | false:error
   */
  function load($p_categories_id, $p_feature_id)
  {
    $this->reset();

    $c_categories_id = (int) $p_categories_id;
    $c_feature_id = (int) $p_feature_id;

    $t_keys_array = array('categories_id' => $c_categories_id,
                          'feature_id' => $c_feature_id
                          );
    $coo_data_object = MainFactory::create_object('GMDataObject', array('categories_filter', $t_keys_array));
    $this->load_data_object($coo_data_object);

    $coo_data_object = NULL;

    return true;
  }

  /*
   * delete
   * delete category filter for given feature_id and category_id
   * @return bool
   */
  function delete()
  {
    $coo_feature = MainFactory::create_object('GMDataObject', array('categories_filter'));
    $t_keys_array = array('categories_id' => $this->v_categories_id,
                          'feature_id' => $this->v_feature_id
                          );

    $coo_feature->set_keys($t_keys_array);
    $coo_feature->delete();
    $coo_feature = NULL;
  }

  /*
   * reset
   * @return bool
   */
  function reset()
  {
    $this->v_categories_id = 0;
    $this->v_feature_id = 0;
    $this->v_sort_order = 0;
    $this->v_selection_preview_mode = '';
    $this->v_selection_template = '';
    $this->v_value_conjunction = true;
    return true;
  }
}
?>