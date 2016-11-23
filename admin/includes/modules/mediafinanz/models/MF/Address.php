<?php

/* -----------------------------------------------------------------------------------------
   Copyright (c) 2011 mediafinanz AG

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see http://www.gnu.org/licenses/.
   ---------------------------------------------------------------------------------------

 * @author Marcel Kirsch
 */

/**
 * Address Class
 *
 * @version 2009-01-12
 */

class MF_Address
{
    private $street;
    private $city;
    private $postcode;
    private $country;


    /**
     * Constructor
     *
     * @param string $street
     * @param string $city
     * @param int $postcode
     * @param string $country
     */
    public function __construct($street, $city, $postcode, $country)
    {
        $this->street   = trim($street);
        $this->city     = trim($city);
        $this->postcode = trim($postcode);
        $this->country  = trim($country);
    }



    /**
     * Returns Street
     *
     * @return string street
     */
    public function getStreet()
    {
        return $this->street;
    }



    /**
     * Returns City
     *
     * @return string City
     */
    public function getCity()
    {
        return $this->city;
    }



    /**
     * Returns Postcode
     *
     * @return int postcode
     */
    public function getPostcode()
    {
        return $this->postcode;
    }



    /**
     * Returns the Country (e.g. DE)
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }



    /**
     * tries to extract street name and housenumber from a german address (e.g. Weiße Breite 5)
     *
     * @param string $street
     * @return array
     */
    public function getStreetArray()
    {
        $street = $this->street;

        /* We also allow hexadecimal characters, because some servers do not have the right locale settings.
           C4 = Ä, E4 = ä, D6 = Ö, F6 = ö, DC = Ü, FC = ü, DF = ß
        */
        if (!preg_match('/^([\w\xDF\xC4\xE4\xD6\xF6\xDC\xFC\-\. ]+?)[,]?[\/]?[ ]?(\d+ ?[a-zA-Z]?(?: ?[-\/] ?\d+ ?[a-zA-Z]?)*)$/', $street, $matches))
        {
            return array('street'      => $street,
                         'houseNumber' => '');
        }
        else
        {
            return array(
               'street'      => $matches[1],
               'houseNumber' => preg_replace('/\s/', '', strtolower($matches[2])));
        }
    }
}

?>