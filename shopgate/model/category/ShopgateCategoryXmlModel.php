<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */
class ShopgateCategoryXmlModel extends ShopgateCategoryModel
{
    public function setUid()
    {
        parent::setUid($this->item['category_number']);
    }
    
    public function setSortOrder()
    {
        parent::setSortOrder($this->item['order_index']);
    }
    
    public function setParentUid()
    {
        parent::setParentUid($this->item["parent_id"]);
    }
    
    public function setIsActive()
    {
        parent::setIsActive($this->item['is_active']);
    }
    
    public function setName()
    {
        parent::setName($this->item['category_name']);
    }
    
    public function setDeeplink()
    {
        parent::setDeeplink($this->item['url_deeplink']);
    }
    
    public function setImage()
    {
        if ($this->item["url_image"]) {
            $image = new Shopgate_Model_Media_Image();
            $image->setUid(1);
            $image->setSortOrder(1);
            $image->setUrl($this->item["url_image"]);
            $image->setTitle($this->item["category_name"]);
            parent::setImage($image);
        }
    }
}
