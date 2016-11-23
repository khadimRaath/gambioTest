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
 * Config Singleton, which manages all of the config data fetched from the database
 */

class MF_Config
{
    protected static $instance = null;



    /**
     * Config array
     *
     * @var array
     */
    private $config;



    /**
     * Protected constructor to avoid unintended instantiation of this singleton
     *
     */
    protected function __construct()
    {
        // fetch data from database
        $query = xtc_db_query("SELECT
                                     config_key,
                                     config_value
                               FROM
                                     mf_config");

       $this->config = array();
       while ($erg = mysqli_fetch_assoc($query))
       {
           $this->config[$erg['config_key']] = $erg['config_value'];
       }
    }



    /**
     * Protected clone method to avoid unintended use of this singleton
     */
    protected function __clone()
    {
    }



    /**
     * Returns an instance of this class
     *
     * @return MF_Config $instance
     */
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new MF_Config();
        }
        return self::$instance;
    }



    /**
     * Returns the value of key
     *
     * @param string $key
     * @return string $value
     */
    public function getValue($key)
    {
        return $this->config[$key];
    }



    /**
     * Stores $value in $key
     *
     * @param string $key
     * @param $value
     */
    public function storeValue($key, $value)
    {
        $this->config[$key] = $value;
        xtc_db_query("UPDATE
                            mf_config
                      SET
                            config_value = '".$value."'
                      WHERE
                            config_key = '".$key."'
                      LIMIT 1");
    }
}

?>