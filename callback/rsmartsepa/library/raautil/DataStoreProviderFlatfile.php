<?php
/* --------------------------------------------------------------
  DataStoreProviderFlatfile.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_DataStoreProviderFlatfile implements Raautil_IDataStoreProvider {
    
    private $absoluteFolderPath = '';
    
    public function __construct($absoluteFolderPath = '') {
        $this->absoluteFolderPath = isset($absoluteFolderPath) ? (is_string($absoluteFolderPath) ? trim($absoluteFolderPath) : '') : '';
    } // End constructor
    
    
    public function getAbsoluteFolderPath() {
        return $this->absoluteFolderPath;
    } // End getAbsoluteFolderPath
    
    /**
     * Validates the values.
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    public function validateDatastore() {
        if($this->absoluteFolderPath == '') {
            throw new Raautil_TransactionException("absoluteFolderPath is missing");
        }
        else if(!is_dir($this->absoluteFolderPath)) {
            throw new Raautil_TransactionException("Folder '" . $this->absoluteFolderPath . "' not found");
        }
    } // End validateDatastore
    
    private function normalizeKey($key = '') {
        $key = str_replace(' ', '_', $key);
        return $key;
    } // End normalizeKey
    
    public function readDatastore($key = '') {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            return NULL;
        }
        
        try {
            $this->validateDatastore();
        } catch(Raautil_TransactionException $ex) {
            return NULL;
        }
        
        $Raautil_DataStore = NULL;
        $key = $this->normalizeKey($key);
        $filepath = $this->absoluteFolderPath . '/' . $key . '.dat';
        if(is_file($filepath) && is_readable($filepath)) {
            $serializedString = @file_get_contents($filepath);
            $Raautil_DataStore = Raautil_DataStore::deserializeDataStore($serializedString);
        }
        return $Raautil_DataStore;
    } // End readDatastore
    
    public function insertDatastore($key = '', $Raautil_DataStore = null) {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Raautil_TransactionException("key is empty");
        }
        
        $this->validateDatastore();
        
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is null");
        }
        else if(!is_object($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no object");
        }
        else if(!($Raautil_DataStore instanceof Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no instance of Raautil_DataStore");
        }
        
        $key = $this->normalizeKey($key);
        $Raautil_DataStoreExisting = $this->readDatastore($key);
        if(isset($Raautil_DataStoreExisting)) {
            throw new Raautil_TransactionException("key is already existing");
        }
        
        $Raautil_DataStore->setTimestampCreated(time());
        $Raautil_DataStore->setTimestampChanged(time());
        $filepath = $this->absoluteFolderPath . '/' . $key . '.dat';
        $serializedString = $Raautil_DataStore->serializeDataStore();
        $writtenBytes = @file_put_contents($filepath, $serializedString);
        if($writtenBytes === FALSE) {
            throw new Raautil_TransactionException("Error writing file");
        }
    } // End insertDatastore
    
    
    public function updateDatastore($key = '', $Raautil_DataStore = null) {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Raautil_TransactionException("key is empty");
        }
        
        $this->validateDatastore();
        
        if(!isset($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is null");
        }
        else if(!is_object($Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no object");
        }
        else if(!($Raautil_DataStore instanceof Raautil_DataStore)) {
            throw new Raautil_TransactionException("Raautil_DataStore is no instance of Raautil_DataStore");
        }
        
        $key = $this->normalizeKey($key);
        $Raautil_DataStoreExisting = $this->readDatastore($key);
        if(!isset($Raautil_DataStoreExisting)) {
            throw new Raautil_TransactionException("key is not existing");
        }
        
        $Raautil_DataStore->setTimestampChanged(time());
        $filepath = $this->absoluteFolderPath . '/' . $key . '.dat';
        $serializedString = $Raautil_DataStore->serializeDataStore();
        $writtenBytes = @file_put_contents($filepath, $serializedString);
        if($writtenBytes === FALSE) {
            throw new Raautil_TransactionException("Error writing file");
        }
    } // End updateDatastore
    
    public function deleteDatastore($key = '') {
        $key = isset($key) ? (is_string($key) ? trim($key) : '') : '';
        if($key == '') {
            throw new Raautil_TransactionException("key is empty");
        }
        
        $this->validateDatastore();
        
        $key = $this->normalizeKey($key);
        $Raautil_DataStoreExisting = $this->readDatastore($key);
        if(!isset($Raautil_DataStoreExisting)) {
            throw new Raautil_TransactionException("key is not existing");
        }
        
        $filepath = $this->absoluteFolderPath . '/' . $key . '.dat';
        @unlink($filepath);
    } // End deleteDatastore
    
    public function readAllDatastores() {
        $resultArray = array();
        try {
            $this->validateDatastore();
        } catch(Raautil_TransactionException $ex) {
            return $resultArray;
        }
        
        $fileList = scandir($this->absoluteFolderPath);
        if(isset($fileList)) {
            if(is_array($fileList)) {
                foreach($fileList as $name) {
                    if ($name === '.' || $name === '..') {
                        continue;
                    }
                    
                    $fileParts = explode('.', $name);
                    if($fileParts !== FALSE) {
                        if(count($fileParts) > 1) {
                            if($fileParts[1] == 'dat') {
                                $key = $fileParts[0];
                                $Raautil_DataStore = $this->readDatastore($key);
                                if(isset($Raautil_DataStore)) {
                                    $entry = array(
                                        'key'   => $key,
                                        'data'  => $Raautil_DataStore,
                                    );
                                    $resultArray[] = $entry;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $resultArray;
    } // End readAllDatastores
    
    
    
    public static function createFolder($absoluteFolderPath = '') {
        $absoluteFolderPath = isset($absoluteFolderPath) ? (is_string($absoluteFolderPath) ? trim($absoluteFolderPath) : ''): '';
        if($absoluteFolderPath == '') {
            return '';
        }
        if(is_dir($absoluteFolderPath)) {
            return $absoluteFolderPath;
        }
        else {
            @mkdir($absoluteFolderPath);
            return $absoluteFolderPath;
        }
    } // End createFolder
    
    
    public static function getDefault() {
        $folder = dirname(dirname(__FILE__) ) . '/rsmartdatastore';
        $folder = self::createFolder($folder);
        return new Raautil_DataStoreProviderFlatfile($folder);
    } // End getDefault
    
} // End class Raautil_DataStoreProviderFlatfile

