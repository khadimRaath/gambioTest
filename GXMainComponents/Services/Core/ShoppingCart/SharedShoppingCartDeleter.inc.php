<?php
/* --------------------------------------------------------------
   SharedShoppingCartDeleter.inc.php 2016-04-08 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class SharedShoppingCartDeleter
 *
 * @category   System
 * @package    SharedShoppingCart
 */
class SharedShoppingCartDeleter implements SharedShoppingCartDeleterInterface
{
    /**
     * @var CI_DB_query_builder
     */
    protected $db;
    
    /**
     * Constructor
     */
    public function __construct(CI_DB_query_builder $db)
    {
        $this->db = $db;
    }

    /**
     * @override
     */
    public function deleteShoppingCartsOlderThan(DateTime $expirationDate)
    {
        $this->db->delete(
            'shared_shopping_carts',
            'creation_date < "' . $expirationDate->format('Y-m-d H:i:s') . '"');
    }
}