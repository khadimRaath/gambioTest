<?php
/* -----------------------------------------------------------------------------------------
   $Id: postfinanceag_creditcards.php, v.2.1 swisswebXperts GmbH
   2014-01-24 swisswebXperts GmbH

	 Copyright (c) 2009 swisswebXperts GmbH www.swisswebxperts.ch
	 Released under the GNU General Public License (Version 2)
	 [http://www.gnu.org/licenses/gpl-2.0.html]
   ---------------------------------------------------------------------------------------*/
include_once('postfinanceag/postfinance.php');

class postfinanceag_creditcards_ORIGIN extends postfinance
{
    var $title, $description, $enabled, $orderid, $productive;

    function postfinanceag_creditcards()
    {
        $this->code = 'postfinanceag_creditcards';
        $this->images = explode(',', MODULE_PAYMENT_POSTFINANCEAG_CREDITCARDS_IMAGES);

        $this->paymentMethod = 'CreditCard';

        parent::__construct();
    }

    function install()
    {
        $configSQL = "INSERT INTO " . TABLE_CONFIGURATION . "
            (
                configuration_key,
                configuration_value,
                configuration_group_id, sort_order,
                use_function,
                set_function,
                date_added
            ) VALUES
            ('MODULE_PAYMENT_" . $this->codeUpperCase . "_STATUS',
                'True',
                6, 10,
                null,
                'xtc_cfg_select_option(array(\'True\', \'False\'), ',
                now()
            ),
            ('MODULE_PAYMENT_" . $this->codeUpperCase . "_SORT_ORDER',
                '1',
                6, 20,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_" . $this->codeUpperCase . "_ALLOWED',
                '',
                6, 80,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_" . $this->codeUpperCase . "_IMAGES',
                'visa,mastercard',
                6, 85,
                null,
                null,
                now()
            ),
            ('MODULE_PAYMENT_" . $this->codeUpperCase . "_CURRENCY',
                'Selected Currency',
                6, 90,
                null,
                'xtc_cfg_select_option(array(\'Selected Currency\',\'CHF\',\'EUR\',\'USD\'), ',
                now()
            ),
            ('MODULE_PAYMENT_" . $this->codeUpperCase . "_ZONE',
                '0',
                6, 100,
                'xtc_get_zone_class_title',
                'xtc_cfg_pull_down_zone_classes(',
                now()
            )
        ";

        xtc_db_query($configSQL);
    }
}
MainFactory::load_origin_class('postfinanceag_creditcards');