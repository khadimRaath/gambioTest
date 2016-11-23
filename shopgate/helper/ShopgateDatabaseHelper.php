<?php
/*
 * Shopgate GmbH
 * http://www.shopgate.com
 * Copyright © 2012-2015 Shopgate GmbH
 * 
 * Released under the GNU General Public License (Version 2)
 * [http://www.gnu.org/licenses/gpl-2.0.html]
 */

class ShopgateDatabaseHelper
{
    /**
     * use via checkTable()
     *
     * @var array
     */
    private $tableExistsCache = array();
    
    /**
     * check if a list of database tables exist
     *
     * @param array $tableNames
     *
     * @return bool
     */
    public function checkTables(array $tableNames)
    {
        foreach ($tableNames as $tableName) {
            if (!$this->checkTable($tableName)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Check if a database table exists
     *
     * @param string $tableName
     *
     * @return bool
     */
    public function checkTable($tableName)
    {
        if (!isset($this->tableExistsCache[$tableName])) {
            $result                             = xtc_db_query("show tables like '{$tableName}'");
            $this->tableExistsCache[$tableName] = !(empty($result) || (xtc_db_num_rows($result) <= 0));
        }
        
        return $this->tableExistsCache[$tableName];
    }
    
    /**
     * check if a column in a database table exist
     * 
     * @param $table
     * @param $column
     *
     * @return bool
     */
    public function checkColumn($table, $column)
    {
        if(!$this->checkTable($table)) {
            return false;
        }
        $result = xtc_db_query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $column . "'");
        while($columnResult = xtc_db_fetch_array($result)) {
            if(isset($column['Field']) && $column['Field'] == $column) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Checks if the given table exists
     *
     * @param string $tableName
     *
     * @throws ShopgateLibraryException
     * 
     * @return boolean
     */
    public function tableExists($tableName)
    {
        $tableName = trim($tableName);
        if (empty($tableName)) {
            return false;
        }
        
        // Get all table names
        $query = xtc_db_query("SHOW TABLES");
        if (!$query) {
            // DB-Error
            throw new ShopgateLibraryException(
                ShopgateLibraryException::PLUGIN_DATABASE_ERROR,
                "Shopgate Plugin - Error checking for table \"$tableName\".", true
            );
        }
        while ($array = xtc_db_fetch_array($query)) {
            $array = array_values($array);
            
            // Check for table name
            if ($array[0] == $tableName) {
                return true;
            }
        }
        
        // The requested table has not been found if execution reaches here
        return false;
    }
}
