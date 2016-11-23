<?php
/* --------------------------------------------------------------
  IDataStoreProvider.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

interface Raautil_IDataStoreProvider {
    
    /**
     * Validates the DataStoreProvider
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    public function validateDatastore();
    
    /**
     * Inserts an instance of Raautil_DataStore with a given key.
     * 
     * @param string $key
     *    A unique key for storing
     * 
     * @param Raautil_DataStore $Raautil_DataStore
     *    An instance of Raautil_DataStore to insert
     * 
     * @return void
     *    Does not return any value
     * 
     * @throws Raautil_TransactionException
     *    If the insert failed 
     */
    public function insertDatastore($key = '', $Raautil_DataStore = null);
    
    /**
     * Updates an instance of Raautil_DataStore with a given key.
     * 
     * @param string $key
     *    A unique key for storing
     * 
     * @param Raautil_DataStore $Raautil_DataStore
     *    An instance of Raautil_DataStore to update
     * 
     * @return void
     *    Does not return any value
     * 
     * @throws Raautil_TransactionException
     *    If the update failed
     */
    public function updateDatastore($key = '', $Raautil_DataStore = null);
    
    /**
     * Deletes a stored Raautil_DataStore by its key.
     * 
     * @param string $key
     *    A unique key to delete
     * 
     * @return void
     *    Does not return any value
     * 
     * @throws Raautil_TransactionException
     *    If the update failed
     * 
     */
    public function deleteDatastore($key = '');
    
    /**
     * Reads a stored Raautil_DataStore by its key.
     * 
     * @param string $key
     *    A unique key to delete
     * 
     * @return Raautil_DataStore
     *    An instance of Raautil_DataStore or NULL
     * 
     */
    public function readDatastore($key = '');
    
    /**
     * Reads all stored Raautil_DataStore instances.
     * 
     * @return array
     *    An array with structured subarrays or an empty array
     * 
     *    array(
     *       array(
     *          'key'       => (string) The key,
     *          'data'      => (Raautil_DataStore) an instance of Raautil_DataStore
     *       ),
     *       ...
     *    )
     */
    public function readAllDatastores();
    
} // End interface Raautil_IDataStoreProvider

