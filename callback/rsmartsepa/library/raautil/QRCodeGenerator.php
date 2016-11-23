<?php
/* --------------------------------------------------------------
  QRCodeGenerator.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
class Raautil_QRCodeGenerator {
    
    const ERROR_CORRECTION_LEVEL_LOW        = 'L';
    const ERROR_CORRECTION_LEVEL_MEDIUM     = 'M';
    const ERROR_CORRECTION_LEVEL_QUALITY    = 'Q';
    const ERROR_CORRECTION_LEVEL_HIGH       = 'H';
    
    // The tid
    private $tid = '';
    
    // The data to encode
    private $data = '';
    
    private $qrCodeTmpFolder = '';
    
    // The error correction level
    // - L - smallest
    // - M
    // - Q
    // - H - best
    private $errorCorrectionLevel = 'L';
    
    private $matrixPointSize = 3;
    
    private $margin = 2;
    
    private $fileName = NULL;
    
    private $qrCodeData = '';
    private $b64QrCodeData = '';
    private $generated = FALSE;

    public function __construct($tid = '', $data = '', $qrCodeTmpFolder = '', $errorCorrectionLevel = 'L', $matrixPointSize = 4, $margin = 2) {
        $this->tid = isset($tid) ? (is_string($tid) ? $tid : '') : '';
        $this->data = isset($data) ? (is_string($data) ? $data : '') : '';
        $this->qrCodeTmpFolder = isset($qrCodeTmpFolder) ? (is_string($qrCodeTmpFolder) ? trim($qrCodeTmpFolder) : '') : '';
        
        $this->errorCorrectionLevel = isset($errorCorrectionLevel) ? (is_string($errorCorrectionLevel) ? $errorCorrectionLevel : self::ERROR_CORRECTION_LEVEL_LOW) : self::ERROR_CORRECTION_LEVEL_LOW;
        if($this->errorCorrectionLevel != self::ERROR_CORRECTION_LEVEL_LOW &&
           $this->errorCorrectionLevel != self::ERROR_CORRECTION_LEVEL_MEDIUM &&
           $this->errorCorrectionLevel != self::ERROR_CORRECTION_LEVEL_QUALITY &&
           $this->errorCorrectionLevel != self::ERROR_CORRECTION_LEVEL_HIGH) {
            $this->errorCorrectionLevel = self::ERROR_CORRECTION_LEVEL_LOW;
        }
        
        $this->matrixPointSize = isset($matrixPointSize) ? (is_int($matrixPointSize) ? abs($matrixPointSize) : 3) : 3;
        if($this->matrixPointSize < 1 || $this->matrixPointSize > 10) {
            $this->matrixPointSize = 3;
        }
        
        $this->margin = isset($margin) ? (is_int($margin) ? abs($margin) : 2) : 2;
        if($this->margin < 0) {
            $this->margin = 0;
        }
        
    } // End constructor
    
    public function validate() {
        if(trim($this->tid == '')) {
            throw new Raautil_TransactionException("TID is empty");
        }
        else if(trim($this->data) == '') {
            throw new Raautil_TransactionException("No data to encode");
        }
        
        if($this->qrCodeTmpFolder == '') {
            throw new Raautil_TransactionException("qrCodeTmpFolder is empty");
        }
        else if(!is_dir($this->qrCodeTmpFolder)) {
            throw new Raautil_TransactionException("qrCodeTmpFolder is not an existing folder");
        }
        else if(!is_writable($this->qrCodeTmpFolder)) {
            throw new Raautil_TransactionException("qrCodeTmpFolder is not writable");
        }
    } // End validate

    public function isValid() {
        try {
            $this->validate();
            return TRUE;
        } catch(Raautil_TransactionException $ex) {
            return FALSE;
        }
    } // End isValid
    
    public function getTID() {
        return $this->tid;
    } // End getTID
    
    public function getData() {
        return $this->data;
    } // End getData
    
    public function getErrorCorrectionLevel() {
        return $this->errorCorrectionLevel;
    } // End getErrorCorrectionLevel
    
    public function getMatrixPointSize() {
        return $this->matrixPointSize;
    } // End getMatrixPointSize
    
    public function getMargin() {
        return $this->margin;
    } // End getMargin
    
    public function isGenerated() {
        return $this->generated;
    }
    
    public function getQRCodeData() {
        return $this->qrCodeData;
    } // End getQRCodeData
    
    public function getBase64QRCodeData() {
        return $this->b64QrCodeData;
    } // End getBase64QRCodeData
    
    public function createPNG() {
        if(!$this->isValid()) {
            return;
        }
        
        if(defined('RAAUTIL_CREATE_QRCODE_OUTPUTBUFFERING')) {
            // Start Output buffering
            ob_start();
            QRcode::png($this->data, false, $this->errorCorrectionLevel, $this->matrixPointSize, $this->margin);
            $content = ob_get_contents();
            ob_end_clean();
            if(isset($content)) {
                if($content !== FALSE) {
                    $this->qrCodeData = $content;
                    $b64 = @base64_encode($content);
                    $this->b64QrCodeData = $b64;
                    $this->generated = TRUE;
                }
            }
        }
        else {
            $pngFile = $this->qrCodeTmpFolder . '/' . $this->tid . '.png';
            $this->fileName = $pngFile;
            QRcode::png($this->data, $pngFile, $this->errorCorrectionLevel, $this->matrixPointSize, $this->margin);
            if(is_file($pngFile)) {
                $content = @file_get_contents($pngFile);
                if(isset($content)) {
                    if($content !== FALSE) {
                        $this->qrCodeData = $content;
                        $b64 = @base64_encode($content);
                        $this->b64QrCodeData = $b64;
                        if(!defined('RAAUTIL_KEEP_DATASTORE_IMAGE')) {
                            @unlink($pngFile);
                        }
                        $this->generated = TRUE;
                    }
                }
            }
        }
    } // End createPNG
    
    
    public static function createDataFromTransactionData($tid = '', $Raa_ServerInfo = NULL, $Raa_TransactionData = NULL) {
        $result = '';
        $tid = isset($tid) ? trim($tid) : '';
        $result = 'TID:' . $tid;
        if(isset($Raa_TransactionData)) {
            if($Raa_TransactionData instanceof Raa_TransactionData) {
                $data = 'Type:' . $Raa_TransactionData->getType();
                if($Raa_TransactionData instanceof Raa_TransactionDataAmount) {
                    $data = $data . ', Id:' . $Raa_TransactionData->getLocalTransactionId() .
                            ', Amount:' . $Raa_TransactionData->getAmount() . ' ' . $Raa_TransactionData->getCurrencyCode();
                }
            }
            $result = $result . "\n" . $data;
        }
        return $result;
    } // End createDataFromTransactionData
    
    
} // End class Raautil_QRCodeGenerator

