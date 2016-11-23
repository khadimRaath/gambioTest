<?php
/* --------------------------------------------------------------
  TerminalDataProviderInifile.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_TerminalDataProviderInifile implements Raautil_ITerminalDataProvider {
    
    private $absoluteFolderPath = '';
    private $nameWithoutExtension = '';
    private $absoluteFilePath = '';
    
    /**
     * Constructs a new Raautil_TerminalDataProviderInifile.
     * 
     * @param string $absoluteFolderPath
     *    The absolute path of a folder with no trailing slash.
     *    This folder should contain the INI file
     * 
     * @param string $nameWithoutExtension 
     *    The name of a file with the lowercase extention '.ini' without extension.
     *    (e.g. 'TEST' refers to the file 'TEST.ini')
     */
    public function __construct($absoluteFolderPath = '', $nameWithoutExtension = '') {
        $this->absoluteFolderPath = isset($absoluteFolderPath) ? (is_string($absoluteFolderPath) ? trim($absoluteFolderPath) : '') : '';
        $this->nameWithoutExtension = isset($nameWithoutExtension) ? (is_string($nameWithoutExtension) ? trim($nameWithoutExtension) : '') : '';
        $this->absoluteFilePath = $this->absoluteFolderPath . '/' . $this->nameWithoutExtension . '.ini';
    } // End constructor
    
    /**
     * Returns the absolute folder path passed in the constructor.
     * 
     * @return string
     *     The absolute folder path passed
     */
    public function getAbsoluteFolderPath() {
        return $this->absoluteFolderPath;
    } // End getAbsoluteFolderPath
    
    /**
     * Returns the name of the file passed in the constructor.
     * 
     * @return string
     *     The name of the file
     */
    public function getNameWithoutExtension() {
        return $this->nameWithoutExtension;
    } // End getNameWithoutExtension
    
    /**
     * Validates the values.
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    public function validate() {
        if($this->absoluteFolderPath == '') {
            throw new Raautil_TransactionException("absoluteFolderPath is missing");
        }
        else if(!is_dir($this->absoluteFolderPath)) {
            throw new Raautil_TransactionException("Folder '" . $this->absoluteFolderPath . "' not found");
        }
        else if($this->nameWithoutExtension == '') {
            throw new Raautil_TransactionException("nameWithoutExtension is missing");
        }
        
        $this->absoluteFilePath = $this->absoluteFolderPath . '/' . $this->nameWithoutExtension . '.ini';
        if(!is_file($this->absoluteFilePath)) {
            throw new Raautil_TransactionException("File '" . $this->absoluteFilePath . "' not found");
        }
        else if(!is_readable($this->absoluteFilePath)) {
            throw new Raautil_TransactionException("File '" . $this->absoluteFilePath . "' is not readable");
        }
    } // End validate
    
    /**
     * Returns the absolute file path of the ini file with extension
     * depending on the values passed in the constuctor.
     * 
     * @return string
     *     The absolute file path of the ini file with extension
     */
    public function getAbsoluteFilePath() {
        return $this->absoluteFilePath;
    } // End getAbsoluteFilePath
    
    /**
     * This method first calls validate() and then tries
     * to read the inifile and construct a new Raautil_TerminalData instance.
     * 
     * @return Raautil_TerminalData 
     *    An instance of Raautil_TerminalData
     * 
     * @throws Raautil_TransactionException
     *    In case of an error
     */
    public function getTerminalData() {
        $this->validate();
        
        $valueArray = @parse_ini_file($this->absoluteFilePath);
        $Raautil_TerminalData = new Raautil_TerminalData($valueArray);
        return $Raautil_TerminalData;
    } // End getTerminalData
    
} // End class Raautil_TerminalDataProviderInifile

