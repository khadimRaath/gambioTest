<?php
/* --------------------------------------------------------------
   GMTabTokenizer.php 2015-05-20 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(boxes.php,v 1.32 2003/05/27); www.oscommerce.com 
   (c) 2003	 nextcommerce (boxes.php,v 1.11 2003/08/13); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: boxes.php 899 2005-04-29 02:40:57Z hhgag $)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

class GMTabTokenizer_ORIGIN
{
    var $tabs_delimiter = "/\\[TAB:(.*?)\\]/";
    var $tabs_p_cleaner = "/<p[^>]*>(\\[TAB:(.*?)\\])<\\/p>/";

    var $input_content = '';

    var $head_content = '';
    var $tab_content = array();
    var $panel_content = array();


    function __construct($content)
    {
        $this->input_content = $content;
        $this->tokenize();
    }

    function tokenize()
    {
        $content = preg_replace($this->tabs_p_cleaner, '$1', $this->input_content);

        $result = preg_split($this->tabs_delimiter, $content, null, PREG_SPLIT_DELIM_CAPTURE);

        $this->head_content = $result[0];

        $get_tab = true;
    	for($i=1; $i<sizeof($result); $i++) 
    	{
            if($get_tab) {
                $this->tab_content[] = $result[$i];
                $get_tab = false;
            } else {
                $this->panel_content[] = $result[$i];
                $get_tab = true;
            }
        }
        return sizeof($result);
    }

    function get_tabs_count()
    {
        return sizeof($this->tab_content);
    }
    

    function get_prepared_output()
    {
        $out = $this->head_content;

        if(sizeof($this->tab_content) > 0) {
            $out .= '<div id="tabbed_description_part">' . "\n";
            $out .= '<ul style="overflow:hidden" class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">' . "\n";

			$t_link = GM_HTTP_SERVER . gm_get_env_info('REQUEST_URI');

            for($i = 0; $i < sizeof($this->tab_content); $i++) {
                if($i == 0) $class = ' class="ui-state-default ui-corner-top ui-state-active"'; else $class = ' class="ui-state-default ui-corner-top"';
                if($i == 0) $class = ' class="ui-state-default ui-corner-top ui-state-active"'; else $class = ' class="ui-state-default ui-corner-top"';
                $out .= '	<li' . $class . '><a href="' . $t_link . '#tab_fragment_' . $i . '" onclick="return false;"><span>' . $this->tab_content[$i] . '</span></a></li>' . "\n";
            }
            $out .= '</ul>' . "\n";
            
            for($i = 0; $i < sizeof($this->panel_content); $i++) {
                $t_hide = '';

                if($i != 0) {
                    $t_hide = ' ui-tabs-hide';
                }

                $out .= '<div id="tab_fragment_' . $i . '" class="ui-tabs-panel ui-widget-content ui-corner-bottom' . $t_hide . '">' . $this->panel_content[$i] . '</div>' . "\n";
            }
            $out .= '</div>';
        }

        return $out;
    }

}

MainFactory::load_origin_class('GMTabTokenizer');