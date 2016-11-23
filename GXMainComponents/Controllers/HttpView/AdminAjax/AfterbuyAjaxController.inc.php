<?php

/* --------------------------------------------------------------
   AfterbuyAjaxController.php 2016-07-08
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class AfterbuyAjaxController
 *
 * This class handles the ajax requests for afterbuy.
 *
 * @category   System
 * @package    AdminHttpViewControllers
 * @extends    AdminHttpViewController
 */
class AfterbuyAjaxController extends AdminHttpViewController
{
    /**
     * @var int Order ID.
     */
    protected $orderId;

    public function actionAfterbuySend()
    {
        if(!$this->_isAdmin())
        {
            throw new AuthenticationException('No admin privileges. Please contact the administrator.');
        }

        require_once(DIR_FS_CATALOG . 'gm/inc/gm_prepare_number.inc.php');
        require_once (DIR_FS_CATALOG.'includes/classes/afterbuy.php');

        $this->orderId = (int)$_GET['orderId'];

        try
        {
            $afterBuy = new xtc_afterbuy_functions($this->orderId);
            if($afterBuy->order_send())
            {
                $afterBuy->process_order();
            }

            return  MainFactory::create('HttpControllerResponse', 'success');
        }
        catch (Exception $e)
        {
            return  MainFactory::create('HttpControllerResponse', 'error');
        }
    }

    /**
     * Check if the customer is the admin.
     *
     * @return bool Is the customer the admin?
     */
    protected function _isAdmin()
    {
        try
        {
            $this->validateCurrentAdminStatus();

            return true;
        }
        catch(LogicException $exception)
        {
            return false;
        }
    }
}

