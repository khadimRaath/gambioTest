<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GNU General Public License (GPL 2)
 * that is bundled with this package in the file LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Payone to newer
 * versions in the future. If you wish to customize Payone for your
 * needs please refer to http://www.payone.de for more information.
 *
 * @category        Payone
 * @package         Payone
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @author          Matthias Walter <info@noovias.com>
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */

/**
 * This Class Initializes the PAYONE SDK
 *
 * It configures the Autoloader for the included Log4php framework
 * And also includes the PAYONE Autoloader
 *
 * @category        Payone
 * @package         Payone
 * @copyright       Copyright (c) 2012 <info@noovias.com> - www.noovias.com
 * @license         <http://www.gnu.org/licenses/> GNU General Public License (GPL 2)
 * @link            http://www.noovias.com
 */

/* --------------------------------------------------------------
	Bootstrap.php 2014-06-20 mabr
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

class Payone_Bootstrap
{
    public function init()
    {
        $this->initPayone();
    }

    protected function initPayone()
    {
        require_once dirname(__FILE__).'/Autoload.php';
        new Payone_Autoload();
    }

}
