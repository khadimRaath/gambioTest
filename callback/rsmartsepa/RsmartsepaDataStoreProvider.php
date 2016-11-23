<?php
/* --------------------------------------------------------------
  RsmartsepaDataStoreProvider.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
class RsmartsepaDataStoreProvider implements Raautil_IDataStoreProvider {
    
    // Only used if the constant RAAUTIL_USE_TEMPORARY_DATASTORE is set
    // In this case the datastore is stored temporary in this array,
    // where the key (the TID) is the array key and the value is the 
    // instance of the class Raautil_DataStore
    private $tmpDataStore = array();
    
    public function __construct() {
        
    } // End constructor

    /**
     * Validates the values.
     * 
     * @throws Exception
     *    On error
     */
    public function validateDatastore() {
        // Do nothing for the moment
    } // End validateDatastore
    
    public function readDatastore($key = '') {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            return NULL;
        }
        
        try {
            $this->validateDatastore();
        } catch(Exception $ex) {
            return NULL;
        }
        
        $Raautil_DataStore = NULL;
        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            if(isset($this->tmpDataStore[$key])) {
                $Raautil_DataStore = $this->tmpDataStore[$key];
            }
            return $Raautil_DataStore;
        }
        // Special handling for temporary datastore
        
        $dataArray = RsmartsepaHelper::tableRsmartSepaReadByTID($key);
        if(isset($dataArray)) {
            if(isset($dataArray['data'])) {
                $Raautil_DataStore = Raautil_DataStore::deserializeDataStore($dataArray['data']);
                if(isset($Raautil_DataStore)) {
                    $Raautil_DataStore->setLastAction($dataArray['action']);
                    $Raautil_DataStore->setLastStatus($dataArray['status']);
                }
            }
        }
        return $Raautil_DataStore;
    } // End readDatastore
    
    public function insertDatastore($key = '', $Raautil_DataStore = null) {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Exception("key is empty");
        }
        
        $this->validateDatastore();
        
        if(!isset($Raautil_DataStore)) {
            throw new Exception("Raautil_DataStore is null");
        }
        else if(!is_object($Raautil_DataStore)) {
            throw new Exception("Raautil_DataStore is no object");
        }
        else if(!($Raautil_DataStore instanceof Raautil_DataStore)) {
            throw new Exception("Raautil_DataStore is no instance of Raautil_DataStore");
        }
        
        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            $recordExisting = isset($this->tmpDataStore[$key]);
        }
        else {
            $recordExisting = RsmartsepaHelper::tableRsmartSepaExistsTID($key);
        }
        // Special handling for temporary datastore
        
        if($recordExisting == TRUE) {
            throw new Exception("key is already existing");
        }
//        $Raautil_DataStoreExisting = $this->readDatastore($key);
//        if(isset($Raautil_DataStoreExisting)) {
//            throw new Exception("key is already existing");
//        }

        $Raautil_DataStore->setTimestampCreated(time());
        $Raautil_DataStore->setTimestampChanged(time());
        $serializedString = $Raautil_DataStore->serializeDataStore();

        $record = array(
            'tid'           => $key,
            'action'        => $Raautil_DataStore->getLastAction(),
            'status'        => $Raautil_DataStore->getLastStatus(),
            'created'       => time(),
            'changed'       => time(),
            'data'          => $serializedString,
        );
        
        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            $this->tmpDataStore[$key] = $Raautil_DataStore;
            $bWritten = TRUE;
        }
        else {
            $bWritten = RsmartsepaHelper::tableRsmartSepaInsert($record);
        }
        // Special handling for temporary datastore
        if($bWritten == FALSE) {
            throw new Exception("Error inserting");
        }
    } // End insertDatastore
    
    public function updateDatastore($key = '', $Raautil_DataStore = null) {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Exception("key is empty");
        }
        
        $this->validateDatastore();
        
        if(!isset($Raautil_DataStore)) {
            throw new Exception("Raautil_DataStore is null");
        }
        else if(!is_object($Raautil_DataStore)) {
            throw new Exception("Raautil_DataStore is no object");
        }
        else if(!($Raautil_DataStore instanceof Raautil_DataStore)) {
            throw new Exception("Raautil_DataStore is no instance of Raautil_DataStore");
        }

        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            $recordExisting = isset($this->tmpDataStore[$key]);
        }
        else {
            $recordExisting = RsmartsepaHelper::tableRsmartSepaExistsTID($key);
        }
        // Special handling for temporary datastore
        if($recordExisting == FALSE) {
            throw new Exception("key is not existing");
        }
//        $Raautil_DataStoreExisting = $this->readDatastore($key);
//        if(!isset($Raautil_DataStoreExisting)) {
//            throw new Exception("key is not existing");
//        }

        $Raautil_DataStore->setTimestampChanged(time());
        $serializedString = $Raautil_DataStore->serializeDataStore();
        
        $record = array(
            'tid'           => $key,
            'action'        => $Raautil_DataStore->getLastAction(),
            'status'        => $Raautil_DataStore->getLastStatus(),            
            'created'       => time(),
            'changed'       => time(),
            'data'          => $serializedString,
        );
        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            $this->tmpDataStore[$key] = $Raautil_DataStore;
            $bWritten = TRUE;
        }
        else {
            $bWritten = RsmartsepaHelper::tableRsmartSepaUpdate($record);
        }
        // Special handling for temporary datastore
        if($bWritten == FALSE) {
            throw new Exception("Error updating");
        }        
    } // End updateDatastore
    
    public function deleteDatastore($key = '') {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Exception("key is empty");
        }

        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            $recordExisting = isset($this->tmpDataStore[$key]);
        }
        else {
            $recordExisting = RsmartsepaHelper::tableRsmartSepaExistsTID($key);
        }
        // Special handling for temporary datastore
        if($recordExisting == FALSE) {
            throw new Exception("key is not existing");
        }
//        $Raautil_DataStoreExisting = $this->readDatastore($key);
//        if(!isset($Raautil_DataStoreExisting)) {
//            throw new Exception("key is not existing");
//        }

        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            if(isset($this->tmpDataStore[$key])) {
                unset($this->tmpDataStore[$key]);
            }
            $bDeleted = TRUE;
        }
        else {
            $bDeleted = RsmartsepaHelper::tableRsmartSepaDeleteByTID($key);
        }
        // Special handling for temporary datastore
        if($bDeleted == FALSE) {
            throw new Exception("Error deleting");
        }        
    } // End deleteDatastore
    
    public function readAllDatastores() {
        $resultArray = array();
        try {
            $this->validateDatastore();
        } catch(Exception $ex) {
            return $resultArray;
        }

        // Special handling for temporary datastore
        if(defined('RAAUTIL_USE_TEMPORARY_DATASTORE')) {
            $allTids = array();
            foreach($this->tmpDataStore as $key => $value) {
                $allTids[] = $key;
            }
        }
        else {
            $allTids = RsmartsepaHelper::tableRsmartSepaReadAllTIDs(0);
        }
        foreach($allTids as $tid) {
            $Raautil_DataStore = $this->readDatastore($tid);
            if(isset($Raautil_DataStore)) {
                $resultArray[] = $Raautil_DataStore;
            }
        }
        return $resultArray;
    } // End readAllDatastores
    
} // End class RsmartsepaDataStoreProvider

