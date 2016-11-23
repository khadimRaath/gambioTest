<?php

/*
  MailBeez Automatic Trigger Email Campaigns
  http://www.mailbeez.com

  Copyright (c) 2010 - 2015 MailBeez

  inspired and in parts based on
  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]

 */


class MailBeezShopvotingBoxContentView extends ContentView
{
    public function MailBeezShopvotingBoxContentView()
    {
        parent::__construct();
        $this->set_content_template('boxes/box_mailbeez_shopvoting.html');
        $this->set_caching_enabled(false);
        $this->build_html = false;
    }

    public function prepare_data()
    {
        if ($this->isWidgetActive()) {
            if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting_widget.php')) {
                require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting_widget.php');
                $shopvoting = new Shopvoting_widget();
                $this->content_array['WIDGET_CODE'] = $shopvoting->output();
            }
        } elseif ($_SESSION['style_edit_mode'] === 'edit') {
            $this->build_html = true;
            $this->content_array['WIDGET_CODE'] = 'MailBeez Shopvoting Widget Dummytext';
        }
    }

    protected function isWidgetActive()
    {
        if (defined('MAILBEEZ_MAILHIVE_STATUS') && MAILBEEZ_MAILHIVE_STATUS == 'True' && defined('MAILBEEZ_SHOPVOTING_STATUS') && MAILBEEZ_SHOPVOTING_STATUS == 'True') {

            if (file_exists(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting.php')) {
                require_once(MH_DIR_FS_CATALOG . MH_ROOT_PATH . 'configbeez/config_shopvoting/classes/Shopvoting.php');
            }

            $voting = new Shopvoting();
            $readAccessArray = explode(',', $voting->customer_group_read);

            if (in_array($_SESSION['customers_status']['customers_status_id'], $readAccessArray)) {
                $this->build_html = true;
            }
        }

        return $this->build_html;
    }
}
