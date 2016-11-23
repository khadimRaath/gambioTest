<?php

/**
 * Class Actindo_Connector_Service_Category
 * @package actindo
 * @author  Patrick Prasse <pprasse@actindo.de>
 * @author  Chris Westerfield <westerfield@actindo.de>
 * @version $Revision: 511 $
 * @copyright Copyright© Actindo GmbH 2015, <support@actindo.de>, Carl-Zeiss-Ring 15 - 85737 Ismaning
 * @license http://opensource.org/licenses/GPL-2.0 GNU Public License
 */
class Actindo_Connector_Service_Category
{
    /**
     * function get categories
     * @param string $params
     * @return array
     */
    public function get($params) {
        return categories_get(array('params'=>$params));
    }

    /**
     * performs operations on single categories or the category tree
     * technical info about param $data: the type should be just "struct", but when $action is "delete" actindo spuriously passes the type "array"
     * @param string $params
     * @param string $action known actions are: add, delete, textchange (rename category), append (move category), above (move category), below (move category)
     * @param int $categoryID the category id to perform operations on
     * @param int $parentID the parent id of the category to perform operations on
     * @param int $referenceID this does something aswell
     * @param struct|array $data data required to perform the called action
     * @return struct
     */
    public function action($params,$action, $categoryID, $parentID, $referenceID, $data) {
        return category_action(array('params'=>$params,'action'=>$action,'categoryID'=>$categoryID,'parentID'=>$parentID,'referenceID'=>$referenceID,'data'=>$data));
    }
}
