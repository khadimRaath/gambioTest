<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 *
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */
class ShopgateReviewXmlModel extends ShopgateReviewModel
{
    public function setUid()
    {
        parent::setUid($this->item['reviews_id']);
    }
    
    public function setItemUid()
    {
        parent::setItemUid($this->item['products_id']);
    }
    
    public function setScore()
    {
        parent::setScore($this->buildScore($this->item['reviews_rating']));
    }
    
    public function setReviewerName()
    {
        parent::setReviewerName($this->item['customers_name']);
    }
    
    public function setDate()
    {
        parent::setDate($this->buildDate($this->item['date_added']));
    }
    
    public function setTitle()
    {
        parent::setTitle($this->buildTitle(''));
    }
    
    public function setText()
    {
        parent::setText($this->item['reviews_text']);
    }
}
