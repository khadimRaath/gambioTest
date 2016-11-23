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
 * Class with several misc functions
 *
 * @author Marcel Kirsch
 * @version 2009-01-06
 *
 */
class MF_Misc
{
    /**
     * Converts strings to utf8
     *
     * @param string $item
     * @param string $key
     */
    public static function toUtf8(&$item, $key)
    {
        $item = utf8_encode($item);
    }



    /**
     * Makes a log entry
     *
     * @param int $customerId
     * @param string $errorText
     */
    public static function errorLog($customerId, $errorText)
    {
        xtc_db_query("INSERT INTO
                                 mf_errors (customerId, errorText, date)
                             VALUES
                                 ('" . $customerId . "',
                                  '" . xtc_db_input($errorText) . "',
                                  '" . time() . "')");
    }



    /**
     * Gets the current error log
     *
     * @return array
     */
    public static function getErrorLog()
    {
        $query = xtc_db_query("SELECT
                                   IFNULL(customers_firstname, 'System') AS firstname,
                                   IFNULL(customers_lastname, '') AS lastname,
                                   errorText,
                                   date
                               FROM
                                   mf_errors
                               LEFT JOIN
                                   customers ON (customers.customers_id = mf_errors.customerId)
                               ORDER BY
                                   date DESC");

        $errorEntries = array();

        while($row = mysqli_fetch_assoc($query))
        {
            $errorEntries[] = $row;
        }

        return $errorEntries;
    }
}