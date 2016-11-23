<?php
/* -----------------------------------------------------------------------------------------
   $Id: postfinanceag_basic.php, v.2.1 swisswebXperts GmbH
   2014-01-24 swisswebXperts GmbH

	 Copyright (c) 2009 swisswebXperts GmbH www.swisswebxperts.ch
	 Released under the GNU General Public License (Version 2)
	 [http://www.gnu.org/licenses/gpl-2.0.html]
   ---------------------------------------------------------------------------------------*/
include_once('postfinanceag/postfinance.php');

class postfinanceag_basic_ORIGIN extends postfinance
{
    public function __construct()
    {
        $this->code = 'postfinanceag_basic';
        $this->images = array('postcard', 'efinance');

        $this->paymentMethodList = array('PostFinance e-finance', 'PostFinance Card');

        parent::__construct();
    }
}
MainFactory::load_origin_class('postfinanceag_basic');