<?php
/*
   --------------------------------------------------------------
   split_page_results.php 2014-07-17 gm
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
   $Id: split_page_results.php 950 2005-05-14 16:45:21Z mz $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(split_page_results.php,v 1.13 2003/05/05); www.oscommerce.com
   (c) 2003     nextcommerce (split_page_results.php,v 1.6 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License
   --------------------------------------------------------------*/
defined( '_VALID_XTC' ) or die( 'Direct Access to this location is not allowed.' );

class splitPageResults_ORIGIN {

    /**
     * Constructor
     */
    function __construct(&$current_page_number, $max_rows_per_page, &$sql_query, &$query_num_rows) {
        if (empty($current_page_number)) $current_page_number = 1;

        $pos_to = strlen($sql_query);
        $pos_from = strpos_wrapper(strtolower($sql_query), ' from', 0);

        $pos_group_by = strpos_wrapper($sql_query, ' group by', $pos_from);
        if (($pos_group_by < $pos_to) && ($pos_group_by != false)) $pos_to = $pos_group_by;

        $pos_having = strpos_wrapper($sql_query, ' having', $pos_from);
        if (($pos_having < $pos_to) && ($pos_having != false)) $pos_to = $pos_having;

        $pos_order_by = strpos_wrapper($sql_query, ' order by', $pos_from);
        if (($pos_order_by < $pos_to) && ($pos_order_by != false)) $pos_to = $pos_order_by;

        $reviews_count_query = xtc_db_query("select count(*) as total " . substr_wrapper($sql_query, $pos_from, ($pos_to - $pos_from)));
        $reviews_count = xtc_db_fetch_array($reviews_count_query);
        $query_num_rows = $reviews_count['total'];

        $num_pages = ceil($query_num_rows / $max_rows_per_page);
        if ($current_page_number > $num_pages) {
        $current_page_number = $num_pages;
        }
        $offset = ($max_rows_per_page * ($current_page_number - 1));
        if ($offset < 0) $offset=0;
        $sql_query .= " limit " . $offset . ", " . $max_rows_per_page;
    }

    /**
     * Displays pagination links
     */
    function display_links($query_numrows, $max_rows_per_page, $max_page_links, $current_page_number, $parameters = '', $page_name = 'page') {

        if ( xtc_not_null($parameters) && (substr_wrapper($parameters, -1) != '&') ) $parameters .= '&';

        // calculate number of pages needing links
        $num_pages = ceil($query_numrows / $max_rows_per_page);

        $pages_array = array();
        for ($i=1; $i<=$num_pages; $i++) {
            $pages_array[] = array('id' => $i, 'text' => $i);
        }

        if ($num_pages > 1) {

            // Open form
            $display_links = xtc_draw_form('pages', basename($_SERVER['PHP_SELF']), '', 'get');

            // Go to preview page button
            $prevButtonActivated = ($current_page_number > 1);
            $prevPageLink = xtc_href_link(basename($_SERVER['PHP_SELF']), $parameters . $page_name . '=' . ($current_page_number - 1), 'NONSSL');
            $display_links .= '<button type="button" data-link="' . $prevPageLink . '" ' . ($prevButtonActivated ? '' : 'disabled') . ' class="pagination-navigation-left" onclick="window.open($(this).data(\'link\'), \'_self\');"><i class="fa fa-chevron-left"></i></button>';

            // Page dropdown
            $display_links .= '&nbsp;&nbsp;';
            $display_links .= sprintf(TEXT_RESULT_PAGE, xtc_draw_pull_down_menu($page_name, $pages_array, $current_page_number, 'onChange="this.form.submit();"'), $num_pages);
            $display_links .= '&nbsp;&nbsp;';

            // Go to next page button
            $nextButtonActivated = (($current_page_number < $num_pages) && ($num_pages != 1));
            $nextPageLink = xtc_href_link(basename($_SERVER['PHP_SELF']), $parameters . $page_name . '=' . ($current_page_number + 1), 'NONSSL');
            $display_links .= '<button type="button" data-link="' . $nextPageLink . '" ' . ($nextButtonActivated ? '' : 'disabled') . ' class="pagination-navigation-right" onclick="window.open($(this).data(\'link\'), \'_self\');"><i class="fa fa-chevron-right"></i></button>';

            if ($parameters != '') {
              if (substr_wrapper($parameters, -1) == '&') $parameters = substr_wrapper($parameters, 0, -1);
              $pairs = explode('&', $parameters);
              while (list(, $pair) = each($pairs)) {
                list($key,$value) = explode('=', $pair);
                $display_links .= xtc_draw_hidden_field(rawurldecode($key), rawurldecode($value));
              }
            }

            if (SID) $display_links .= xtc_draw_hidden_field(session_name(), session_id());

            $display_links .= '</form>';
        } else {
            $display_links = sprintf(TEXT_RESULT_PAGE, $num_pages, $num_pages);
        }

        return $display_links;
    }

    /**
     * Displays row count
     */
    function display_count($query_numrows, $max_rows_per_page, $current_page_number, $text_output) {
        $to_num = ($max_rows_per_page * $current_page_number);
        if ($to_num > $query_numrows) $to_num = $query_numrows;
        $from_num = ($max_rows_per_page * ($current_page_number - 1));
        if ($to_num == 0) {
        $from_num = 0;
        } else {
        $from_num++;
        }

        return sprintf($text_output, $from_num, $to_num, $query_numrows);
    }

}

MainFactory::load_origin_class('splitPageResults');
