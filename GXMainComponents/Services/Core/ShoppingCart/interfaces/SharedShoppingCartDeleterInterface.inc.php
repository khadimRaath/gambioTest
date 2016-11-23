<?php
/* --------------------------------------------------------------
   SharedShoppingCartDeleterInterface.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Interface SharedShoppingCartDeleterInterface
 *
 * @category   System
 * @package    SharedShoppingCart
 * @subpackage Interfaces
 */
interface SharedShoppingCartDeleterInterface
{
    /**
     * Deletes all shared shopping carts that are expired
     *
     * @param DateTime $expirationDate All shared shopping carts older than that date are expired
     */
    public function deleteShoppingCartsOlderThan(DateTime $expirationDate);
}