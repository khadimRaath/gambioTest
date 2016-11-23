<?php
/* --------------------------------------------------------------
  RsmartsepaHelper.php 2015-06-11 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
class RsmartsepaHelper {
    
    const RSMARTSEPA_VERSION                = '1.0.6';
    
    private static $debugging = TRUE;
    private static $cronMessages = array();
    
    public function __construct() {
        
    } // End constructor

    public function t($string = '', $replacementArgs = array(), $language = '') {
        $string = isset($string) ? (is_string($string) ? $string : '') : '';
        $language = isset($language) ? (is_string($language) ? strtolower(trim($language)) : '') : '';
        $string = $this->_getLanguageString($string, $language);
        
        if(isset($replacementArgs) && is_array($replacementArgs)) {
            foreach($replacementArgs as $key => $value) {
                if(is_string($key) && is_string($value)) {
                    $string = str_replace($key, $value, $string);
                }
            }
        }
        return $string;
    } // End t
    
    private function _getLanguageString($string = '', $language = '') {
        $result = '';
        
        if($string == "Pay realtime, save and easy all over Europe.") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_PAYSAVEINEUROPE', 
                    'Realtime, einfach und sicher in ganz Europa bezahlen.');
        }
        else if($string == "Please start your rSm@rt-App, tap on the symbol 'Transfer' and scan the displayed QR-Code") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_SCANTEXT', 
                    'Starten Sie nun Ihre rSmart-App, dr&uuml;cken Sie bitte auf das Symbol "&Uuml;berweisung" und scannen/fotografieren dann den angezeigten QR-Code');
        }
        else if($string == "Order Number") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_ORDERNUMBER', 
                    'Order Nummer');            
        }
        else if($string == "Seller") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_SELLER', 
                    'Verk&auml;ufer');            
        }
        else if($string == "Amount") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_AMOUNT', 
                    'Betrag');            
        }
        else if($string == "Cancel") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_CANCEL', 
                    'Abbrechen');            
        }
        else if($string == "Simulate MATCH") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_SIMULATEMATCH', 
                    'Simuliere Erfolg');            
        }
        else if($string == "Simulate FAILURE") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_SIMULATEFAILURE', 
                    'Simuliere Fehler');            
        }
        else if($string == "If you have the rSm@rtSEPA app installed, following the link") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT1', 
                    'Falls Sie die rSmartSEPA App installiert haben, bewirkt der Link');            
        }
        else if($string == "will launch the app.") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT2', 
                    'den Start dieser App.');            
        }
        else if($string == "Otherwise the browser will try to open a non existing page.") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT3', 
                    'Andernfalls wird der Browser versuchen, eine nicht existierende Seite zu &ouml;ffnen.');            
        }
        else if($string == "Do you really want to follow this link?") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_TOUCHTEXT4', 
                    'Wollen Sie diesem Link wirklich folgen?');            
        }
        else if($string == "Simulation MATCH Flag has been set. The next poll cycle will react on it.") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_MATCHFLAGSET', 
                    'Das MATCH Simulationsflag wurde gesetzt. The n&auml;chste Pollzyklus wird darauf reagieren.');            
        }
        else if($string == "Simulation FAILURE Flag has been set. The next poll cycle will react on it.") {
            $result = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_FAILUREFLAGSET', 
                    'Das FEHLER Simulationsflag wurde gesetzt. The n&auml;chste Pollzyklus wird darauf reagieren.');            
        }
        
        return $result;
    } // End _getLanguageString
    
    
    
    public static function debug($type = '', $debugArgs0 = NULL, $debugArgs1 = NULL) {
        if(self::$debugging == TRUE) {
            
            if(defined('DIR_FS_CATALOG')) {
                $path = DIR_FS_CATALOG . 'mwmucdebug.php';
                if(is_file($path)) {
                    include_once $path;
                }
            }
            
            if(class_exists('MwmucDebug', FALSE)) {
                $type = isset($type) ? (is_string($type) ? trim($type) : '') : '';
                if($type == 'printExceptionStackTrace') {
                    MwmucDebug::printExceptionStackTrace($debugArgs0, $debugArgs1);
                }
                else if($type == 'printDefinedConstants') {
                    MwmucDebug::printDefinedConstants($debugArgs0, $debugArgs1);
                }
                else if($type == 'printGlobalVariables') {
                    MwmucDebug::printGlobalVariables($debugArgs0);
                }
                else if($type == 'printSessionVariables') {
                    MwmucDebug::printSessionVariables($debugArgs0);
                }
                else if($type == 'log') {
                    MwmucDebug::log($debugArgs0, $debugArgs1);
                }
            }
        }
    } // End debug
    
    /**
     * Checks if a particular PHP extension is installed.
     * 
     * @param string $name
     *    The name of the extension. Valid names are:
     *    - 'curl' : To check if cURL is installed
     *    - 'libgd': To check if libgd is installed
     * 
     * @return boolean 
     *    TRUE if the requested extension is installed, FALSE otherwise
     */
    public static function isPHPExtensionInstalled($name = '') {
        $result = FALSE;
        $name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
        if($name == '') {
            return $result;
        }
        if($name == 'curl') {
            if(function_exists('curl_version')) {
                $result = TRUE;
            }
        }
        else if($name == 'libgd') {
            if(function_exists('gd_info')) {
                $result = TRUE;
            }
        }
        return $result;
    } // End isPHPExtensionInstalled
    
    public static function defineConstant($name = '', $value = '') {
        $name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
        if($name != '') {
            if(isset($value)) {
                if(is_string($value) || is_int($value) || is_float($value) || is_bool($value)) {
                    if(!defined($name)) {
                        define($name, $value);
                    }
                }
            }
        }
    } // End defineConstant
    
    /**
     * Returns the value of a constant as string.
     * 
     * @param string $name
     *    The name of the constant
     * 
     * @param string $dflt
     *    A default value (Default is '')
     * 
     * @return string
     *    The result or $dflt or an empty string
     */
    public static function getConstantValueAsString($name = '', $dflt = '') {
        $name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
        $result = isset($dflt) ? (is_string($dflt) ? trim($dflt) : '') : '';
        if($name == '') {
            return $result;
        }
        if(defined($name)) {
            $result = strval(constant($name));
        }
        return $result;
    } // End getConstantValueAsString
    
    /**
     * Returns the value of a constant as boolean.
     * 
     * @param string $name
     *    The name of the constant
     * 
     * @param boolean $dflt
     *    The default value (Default is FALSE)
     * 
     * @return boolean
     *    TRUE or FALSE 
     */
    public static function getConstantValueAsBoolean($name = '', $dflt = FALSE) {
        $name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
        $dflt = isset($dflt) ? ($dflt == TRUE ? TRUE : FALSE) : FALSE;
        $dfltStr = $dflt == TRUE ? 'true' : 'false';
        $result = $dflt;
        $str = strtolower(self::getConstantValueAsString($name, $dfltStr));
        if($str == 'true' || $str == '1') {
            $result = TRUE;
        }
        else if($str == 'false' || $str == '0') {
            $result = FALSE;
        }
        
//        if($name == 'MODULE_PAYMENT_RSMARTSEPA_SIMULATIONMODE') {
//            $libDir = self::getLibraryDirectory();
//            $fpath = $libDir . '/TerminalSdkSimulator.php';
//            if(is_file($fpath)) {
//                include_once($fpath);
//                if(class_exists('TerminalSdkSimulator', FALSE)) {
//                    if(TerminalSdkSimulator::isForceSimulation()) {
//                        $result = TRUE;
//                    }
//                }
//            }
//        }
//        if($name == 'MODULE_PAYMENT_RSMARTSEPA_DEBUGMODE') {
//            $libDir = self::getLibraryDirectory();
//            $fpath = $libDir . '/TerminalSdkSimulator.php';
//            if(is_file($fpath)) {
//                include_once($fpath);
//                if(class_exists('TerminalSdkSimulator', FALSE)) {
//                    if(TerminalSdkSimulator::isForceDebugging()) {
//                        $result = TRUE;
//                    }
//                }
//            }            
//        }
        
        return $result;
    } // End getConstantValueAsBoolean
    
    /**
     * Returns the value of a constant as integer.
     * 
     * @param string $name
     *    The name of the constant
     * 
     * @param int $dflt
     *    The default value (Default is 0)
     * 
     * @return int
     *    The result value 
     */
    public static function getConstantValueAsInt($name = '', $dflt = 0) {
        $dflt = isset($dflt) ? (is_numeric($dflt) ? intval($dflt) : 0) : 0;
        $dfltStr = strval($dflt);
        $result = $dflt;
        $str = self::getConstantValueAsString($name, $dfltStr);
        if(is_numeric($str)) {
            $result = intval($str);
        }
        return $result;
    } // End getConstantValueAsInt
    
    /**
     * Returns the shop base url.
     * 
     * @param string $connection
     *    'SSL' for https, 'NONSSL' for http
     * 
     * @return string
     *    The shop url
     */
    public static function getShopUrl($connection = 'NONSSL') {
        $connection = isset($connection) ? (is_string($connection) ? strtoupper(trim($connection)) : 'NONSSL') : 'NONSSL';
        if($connection != 'NONSSL' && $connection != 'SSL') {
            $connection = 'NONSSL';
        }
        $result = ($connection == 'SSL' ? trim(self::getConstantValueAsString('HTTPS_SERVER', '')) : trim(self::getConstantValueAsString('HTTP_SERVER', '')) ) .
                  trim(self::getConstantValueAsString('DIR_WS_CATALOG', ''));
        return $result;
    } // End getShopUrl
    
    
    public static function getLanguageName($langName = 'english') {
        $langName = isset($langName) ? (is_string($langName) ? trim($langName) : 'english') : 'english';
        if($langName == '') {
            $langName = 'english';
        }
        $langArray = array();
        $langDir = DIR_FS_CATALOG . 'lang/';
        foreach (new DirectoryIterator($langDir) as $file) {
            if($file->isDir()) {
                $langArray[] = $file->getFilename();
            }
        }
        return (!in_array($langName, $langArray)) ? 'english' : $langName;
    } // End getLanguageName
    
    public static function getLanguageCode($langName = 'english') {
        $langName = isset($langName) ? (is_string($langName) ? trim($langName) : 'english') : 'english';
        if($langName == '') {
            $langName = 'english';
        }
        
        switch ($langName) {
            case 'german' : return 'de';
            case 'dutch'  : return 'nl';
            case 'french' : return 'fr';
            case 'italian': return 'it';
            case 'polish' : return 'pl';
            case 'english':
            default       : return 'en';
	}
    } // End getLanguageCode
    
    /**
     * Escapes the given string via mysql_real_esacpe_string (if function exists & a db-connection is available) or mysql_escape_string
     * @param type $string
     * @return type 
     */
    public static function escapeSql($string) {
        return (function_exists('mysqli_real_escape_string') && mysqli_ping($GLOBALS["___mysqli_ston"])) ? ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : "")) : ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $string) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    } // End escapeSql
    
    /**
     * Returns the directory where this script is located.
     * 
     * @return string
     *    The directory where this script is located
     */
    public static function getHelperDirectory() {
        $dir = dirname(__FILE__);
        $dir = str_replace("\\", '/', $dir);
        return $dir;
    } // End getHelperDirectory
    
    /**
     * Checks if a file exists relative to 'callback/rsmartsepa/'.
     * 
     * @param string $relPath
     *    The relative path to 'callback/rsmartsepa/'.
     *    (e.g. 'resources/images/rsmart.png'
     * 
     * @return boolean
     *    TRUE if the file exists, FALSE if not
     */
    public static function existFile($relPath = '') {
        $relPath = isset($relPath) ? (is_string($relPath) ? trim($relPath) : '') : '';
        if($relPath == '') {
            return FALSE;
        }
        $dir = self::getHelperDirectory();
        $filePath = $dir . '/' . $relPath;
        $result = is_file($filePath);
        return $result;
    } // End existFile
    
    /**
     * Returns the subdirectory 'resources'.
     * 
     * @return string 
     *    The subdirectory 'resources'
     */
    public static function getResourcesDirectory() {
        $dir = self::getHelperDirectory();
        $dir = $dir . '/resources';
        return $dir;
    } // End getResourcesDirectory
    
    /**
     * Returns the subdirectory 'resources/templates'.
     * 
     * @return string 
     *    The subdirectory 'resources/templates'
     */
    public static function getTemplatesDirectory() {
        $dir = self::getResourcesDirectory();
        $dir = $dir . '/templates';
        return $dir;
    } // End getTemplatesDirectory
    
    /**
     * Returns the subdirectory 'library'.
     * 
     * @return string 
     *    The subdirectory 'library'
     */
    public static function getLibraryDirectory() {
        $dir = self::getHelperDirectory();
        $dir = $dir . '/library';
        return $dir;
    } // End getLibraryDirectory
    
    /**
     * Returns (or allocates) the subdirectory 'datastore'.
     * 
     * @return string 
     *    The subdirectory 'datastore'
     */
	public static function getDatastoreDirectory() {
		// MWMUC-11.06.2015: Changed to the cache directory
		$dir = DIR_FS_CATALOG . 'cache';
		return $dir;
	} // End getDatastoreDirectory
    
    public static function startLibrary() {
        $libraryStartError = '';
        $libdir = self::getLibraryDirectory();
        if(is_dir($libdir)) {
            $includePath = $libdir . '/TerminalSdkLibrary.php';
            if(is_file($includePath)) {
                require_once $includePath;
                
                $helperdir = self::getHelperDirectory();
                $includePath = $helperdir . '/RsmartsepaTransactionLogger.php';
                if(is_file($includePath)) {
                    require_once $includePath;
                }
                else {
                    throw new Exception("Script " . $includePath . " is not existing");
                }
                $includePath = $helperdir . '/RsmartsepaTerminalDataProviderConfig.php';
                if(is_file($includePath)) {
                    require_once $includePath;
                }
                else {
                    throw new Exception("Script " . $includePath . " is not existing");
                }
                $includePath = $helperdir . '/RsmartsepaTransactionWrapper.php';
                if(is_file($includePath)) {
                    require_once $includePath;
                }
                else {
                    throw new Exception("Script " . $includePath . " is not existing");
                }
                $includePath = $helperdir . '/RsmartsepaDataStoreProvider.php';
                if(is_file($includePath)) {
                    require_once $includePath;
                }
                else {
                    throw new Exception("Script " . $includePath . " is not existing");
                }
                
                $datastoredir = self::getDatastoreDirectory();
                if(!is_dir($datastoredir)) {
                    throw new Exception("Datastore Directory " . $datastoredir . " is not existing");
                }
                else if(!is_writable($datastoredir)) {
                    throw new Exception("Datastore Directory " . $datastoredir . " is not writable");
                }
                else if(!is_readable($datastoredir)) {
                    throw new Exception("Datastore Directory " . $datastoredir . " is not readable");
                }
                
                // Check Terminaldata
//                $terminalDataFile = $helperdir . '/terminaldata.ini';
//                if(!is_file($terminalDataFile)) {
//                    throw new Exception("File " . $terminalDataFile . " is not existing");
//                }
//                else if(!is_readable($terminalDataFile)) {
//                    throw new Exception("File " . $terminalDataFile . " is not readable");
//                }
                
                try {
                    $RsmartsepaTerminalDataProviderConfig = new RsmartsepaTerminalDataProviderConfig();
                    $RsmartsepaTerminalDataProviderConfig->getTerminalData();
                } catch (Exception $ex) {
                    $msg = self::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_STR_INVALID_TERMINALDATA', 'Ungültige Konfiguration. Grund:') .
                           ' ' . $ex->getMessage();
                    throw new Exception($msg);
                }
                
            }
            else {
                throw new Exception("Script " . $includePath . " is not existing");
            }
        }
        else {
            throw new Exception("Directory " . $libdir . " is not existing");
        }
    } // End startLibrary
    
    
    /**
     * Retrieves a request value.
     * 
     * @param string $name
     *    The name of the request value
     * 
     * @param string $dflt
     *    The default value (Default is '')
     * 
     * @param boolean $htmlspecial
     *    Use htmlspecialchars() (Default = TRUE)
     * 
     * @return string
     *    The request value or the default value 
     */
    public static function getRequestValue($name = '', $dflt = '', $htmlspecial = TRUE) {
        $htmlspecial = isset($htmlspecial) ? ($htmlspecial == TRUE ? TRUE : FALSE) : TRUE;
        $result = isset($dflt) ? $dflt : '';
        $name = isset($name) ? trim($name) : '';
        if($name == '') {
            return $result;
        }
        if(isset($_REQUEST[$name])) {
            if($htmlspecial == TRUE)
                $result = htmlspecialchars($_REQUEST[$name]);
            else
                $result = $_REQUEST[$name];
        }
        return $result;
    } // End getRequestValue
    
    /**
     * Retrieves a session value.
     * 
     * @param string $name
     *    The name of the value
     * 
     * @param mixed $dflt
     *    A default value (Default is NULL)
     * 
     * @return mixed
     *    The value or NULL 
     */
    public static function getSessionValue($name = '', $dflt = NULL) {
        $name = isset($name) ? trim($name) : '';
        $result = $dflt;
        if($name == '') {
            return $result;
        }
        if(isset($_SESSION[$name])) {
            $result = $_SESSION[$name];
        }
        return $result;
    } // End getSessionValue
    
    /**
     * Sets or unsets a session value.
     * 
     * @param string $name
     *    The name of the value
     * 
     * @param mixed $value
     *    The value or NULL. 
     *    If the value is NULL and the session variable exists, 
     *    it will be unset
     * 
     * @return boolean
     *    TRUE or FALSE
     */
    public static function setSessionValue($name = '', $value = NULL) {
        $name = isset($name) ? trim($name) : '';
        if($name == '') {
            return FALSE;
        }
        
        if(isset($value)) {
            $_SESSION[$name] = $value;
            return TRUE;
        }
        else {
            if(isset($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            return TRUE;
        }
    } // End setSessionValue
    
    /**
     * Delivers a resource. Resources are delivered from the 'resources'
     * subdirectory.
     * Resources are described by 2 request values. 
     * The first request value is named 'rsmartseparestype'. 
     * It can have the following values:
     * - 'js' : A javascript file from the subdirectory 'resources/js'
     * - 'css' : A style sheet file from the subdirectory 'resources/css'
     * - 'jpg' : A .jpg image file from the subdirectory 'resources/images'
     * - 'gif' : A .gif image file from the subdirectory 'resources/images'
     * - 'png' : A .png image file from the subdirectory 'resources/images'
     * 
     * The second request value is named 'rsmartseparesname' and contains the name of
     * the resource file without extension.
     * 
     * If the resource was found, it is delivered with the appropriate
     * header and then PHP will exit.
     * 
     * If the resource is not found, this function will return FALSE.
     * 
     * @return boolean
     *    FALSE if the resource was not found.
     */
    public static function deliverResource() {
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $resDir = self::getResourcesDirectory();
        $restype = trim(self::getRequestValue('rsmartseparestype', ''));
        $resname = trim(self::getRequestValue('rsmartseparesname', ''));
        
        if($restype == 'js') {
            $content = FALSE;
            $filePATH = $resDir . '/js/' . $resname . '.js';
            if(is_file($filePATH)) {
                $content = @file_get_contents($filePATH);
            }
            if($content !== FALSE) {
                header('Content-Type: text/javascript');
                print($content);
                exit;
            }
        } // end: if($restype == 'js')
        else if($restype == 'css') {
            $content = FALSE;
            $filePATH = $resDir . '/css/' . $resname . '.css';
            if(is_file($filePATH)) {
                $content = @file_get_contents($filePATH);
            }
            if($content !== FALSE) {
                header('Content-Type: text/css');
                print($content);
                exit;
            }   
        } // end: else if($restype == 'css')
        else if($restype == 'jpg') {
            $content = FALSE;
            $filePATH = $resDir . '/images/' . $resname . '.jpg';
            if(is_file($filePATH)) {
                $content = @file_get_contents($filePATH);
            }
            if($content !== FALSE) {
                header('Content-Type: image/jpeg');
                print($content);
                exit;
            }                        
        } // end: else if($restype == 'jpg')
        else if($restype == 'gif') {
            $content = FALSE;
            $filePATH = $resDir . '/images/' . $resname . '.gif';
            if(is_file($filePATH)) {
                $content = @file_get_contents($filePATH);
            }
            if($content !== FALSE) {
                header('Content-Type: image/gif');
                print($content);
                exit;
            }            
        } // end: else if($restype == 'gif')
        else if($restype == 'png') {
            $content = FALSE;
            $filePATH = $resDir . '/images/' . $resname . '.png';
            if(is_file($filePATH)) {
                $content = @file_get_contents($filePATH);
            }
            if($content !== FALSE) {
                header('Content-Type: image/png');
                print($content);
                exit;
            }
        } // end: else if($restype == 'png')
        else if($restype == 'qrcode') {
            $tid = $resname;
            try {
                self::startLibrary();
                $RsmartsepaTransactionWrapper = new RsmartsepaTransactionWrapper();
                $RsmartsepaTransactionWrapper->deliverQrCodeForTID($tid);
            } catch(Exception $ex) {
                $content = '';
                header('Content-Type: image/png');
                print($content);
                exit;
            }
        }
        return FALSE;
    } // End deliverResource
    
    public static function readResourceFile($relpath = '') {
        $relpath = isset($relpath) ? (is_string($relpath) ? trim($relpath) : '') : '';
        if($relpath == '') {
            return '';
        }
        $result = '';
        $filePATH = self::getResourcesDirectory() . '/' . $relpath;
        if(is_file($filePATH)) {
            $content = @file_get_contents($filePATH);
            if($content !== FALSE) {
                $result = $content;
            }
        }
        return $result;
    } // End readResourceFile
    
    /**
     * Creates a url for a resource that is located in the subdirectory 
     * 'callback/rsmartsepa/'.
     * 
     * @param string $relPath
     *     The relative path to 'callback/rsmartsepa/'.
     *     (e.g. 'resources/images/rsmart.png'
     * 
     * @return string
     *     The url or an empty string if $relPath is empty 
     */
    public static function createResourceUrl($relPath = '') {
        $relPath = isset($relPath) ? (is_string($relPath) ? trim($relPath) : '') : '';
        if($relPath == '') {
            return '';
        }
        $path = 'callback/rsmartsepa/';
        if(defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false) {
            $path = '../callback/rsmartsepa/';
        }
        $path = $path . $relPath;
        $result = self::createUrl($path, 
                                  array(), 
                                  'SSL', true, false);
        return $result;        
    } // End createResourceUrl
    
//    public static function createRSmartLogoUrl() {
//        $path = 'callback/rsmartsepa/callback.php';
//        if(defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false) {
//            $path = '../callback/rsmartsepa/callback.php';
//        }
//        $result = RsmartsepaHelper::createUrl($path, 
//                                            array(
//                                                'rsmartsepaaction'      => 'rsmartsepagetres',
//                                                'rsmartseparestype'     => 'png',
//                                                'rsmartseparesname'     => 'rsmart',
//                                            ), 
//                                            'SSL', true, false);
//        return $result;
//    } // End createRSmartLogoUrl

    public static function createRSmartLogoUrl() {
        $result = self::createResourceUrl('resources/images/rsmart.png');
        return $result;
    } // End createRSmartLogoUrl
    
    public static function createRSmartLogoImageTag() {
        $logoUrl = self::createRSmartLogoUrl();
        $result = '<img style="border: none; padding: 2px;" src="' . 
                  $logoUrl .
                  '" alt="rSmart Logo" width="100" />';
        return $result;
    } // End createRSmartLogoImageTag
    
    public static function createUrl($page = '',
                                     $parameters = array(),
                                     $connection = 'NONSSL',
                                     $add_session_id = true, 
                                     $search_engine_safe = true, 
                                     $p_relative_url = false) {
        $page = isset($page) ? (is_string($page) ? $page : '') : '';
        $parameters = isset($parameters) ? (is_array($parameters) ? $parameters : array()) : array();
        $connection = isset($connection) ? (is_string($connection) ? trim($connection) : 'NONSSL') : 'NONSSL';
        if($connection != 'SSL' && $connection != 'NONSSL') {
            $connection = 'NONSSL';
        }
        $add_session_id = isset($add_session_id) ? ($add_session_id == TRUE ? TRUE : FALSE) : TRUE;
        $search_engine_safe = isset($search_engine_safe) ? ($search_engine_safe == TRUE ? TRUE : FALSE) : TRUE;
        $p_relative_url = isset($p_relative_url) ? ($p_relative_url == TRUE ? TRUE : FALSE) : FALSE;
        
        $params = self::createQueryParameters($parameters, TRUE);        
        $result = xtc_href_link($page, $params, $connection, $add_session_id, $search_engine_safe, $p_relative_url);
        $result = str_replace('&amp;', '&', $result);
        return $result;
    } // End createUrl
    
    public static function createCallbackUrl($parameters = array(),
                                             $connection = 'NONSSL',
                                             $add_session_id = true, 
                                             $search_engine_safe = true, 
                                             $p_relative_url = false) {
        $path = 'callback/rsmartsepa/callback.php';
        if(defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false) {
            $path = '../callback/rsmartsepa/callback.php';
        }
        $result = self::createUrl($path,
                                  $parameters,
                                  $connection,
                                  $add_session_id,
                                  $search_engine_safe,
                                  $p_relative_url);
        return $result;
    } // End createCallbackUrl

    public static function createCronUrl($parameters = array(),
                                             $connection = 'NONSSL',
                                             $add_session_id = true, 
                                             $search_engine_safe = true, 
                                             $p_relative_url = false) {
        $path = 'callback/rsmartsepa/cron.php';
        if(defined('DIR_WS_ADMIN') && strpos($_SERVER['REQUEST_URI'], constant('DIR_WS_ADMIN')) !== false) {
            $path = '../callback/rsmartsepa/cron.php';
        }
        $result = self::createUrl($path,
                                  $parameters,
                                  $connection,
                                  $add_session_id,
                                  $search_engine_safe,
                                  $p_relative_url);
        return $result;
    } // End createCronUrl
    
    /**
     * Creates query parameters.
     * 
     * @param array $parameters
     *    An array with key/value pairs.
     *    The keys are the query parameter names and
     *    the values are the query parameter values
     * 
     * @param boolean $urlEncode
     *    TRUE if the value should be urlencoded, FALSE otherwise
     *    (Default = TRUE)
     * 
     * @return string 
     *    A query paramer string in the format:
     *    "key1=value1&key2=value2...."
     */
    public static function createQueryParameters($parameters = array(), $urlEncode = TRUE) {
        $parameters = isset($parameters) ? (is_array($parameters) ? $parameters : array()) : array();
        $urlEncode = isset($urlEncode) ? ($urlEncode == TRUE ? TRUE: FALSE) : TRUE;
        $result = '';
        foreach($parameters as $name => $value) {
            if($urlEncode == TRUE) {
                $value = urlencode($value);
            }
            $entry = trim($name) . '=' . $value;
            if($result == '')
                $result = $entry;
            else
                $result = $result . '&' . $entry;
        }
        return $result;
    } // End createQueryParameters
    
    
    /**
     * Check if a record with a given TID exists.
     * 
     * @param string $tid
     *    The TID to check for
     * 
     * @return boolean 
     *    TRUE if the record exists, FALSE if not or $tid is empty
     */
    public static function tableRsmartSepaExistsTID($tid = '') {
        $tid = isset($tid) ? (is_string($tid) ? trim($tid) : '') : '';
        if($tid == '') {
            return FALSE;
        }
        $tid = self::escapeSql($tid);
        if(strlen($tid) > 128) {
            $tid = substr($tid, 0, 128);
        }
        
        try {
            $sql = "select created from rsmartsepa where tid = '" . $tid . "'";
            $query = xtc_db_query($sql);
            $records = xtc_db_fetch_array($query);
            if(!isset($records)) {
                return FALSE;
            }
            else if($records === FALSE) {
                return FALSE;
            }
            $result = FALSE;
            if(is_array($records)) {
                if(isset($records['created'])) {
                   $result = TRUE; 
                }
            }
            return $result;
        } catch(Exception $ex) {
            return FALSE;
        }
    } // End tableRsmartSepaExistsTID
    
    /**
     * Reads a Raautil_DataStore.
     * 
     * @param string $tid
     *    The TID
     * 
     * @return array|NULL
     *    If $tid is empty or no record was found, NULL is returned.
     *    Otherwise a structured array is returned in the format:
     * 
     *    array(
     *       'tid'          => (string) the TID,
     *       'action'       => (string) the last action,
     *       'status'       => (string) the last status,
     *       'created'      => (int) the creation timestamp,
     *       'changed'      => (int) the changed timestamp,
     *       'data'         => (string) a serialized version of the Raautil_DataStore,
     *    )
     */
    public static function tableRsmartSepaReadByTID($tid = '') {
        $tid = isset($tid) ? (is_string($tid) ? trim($tid) : '') : '';
        if($tid == '') {
            return NULL;
        }
        $tid = self::escapeSql($tid);
        if(strlen($tid) > 128) {
            $tid = substr($tid, 0, 128);
        }
        
        try {
            $sql = "select tid, action, status, created, changed, data from rsmartsepa where tid = '" . $tid . "'";
            $query = xtc_db_query($sql);
            $records = xtc_db_fetch_array($query);
            if(!isset($records)) {
                return NULL;
            }
            else if($records === FALSE) {
                return NULL;
            }
            
            $result = NULL;
            if(is_array($records)) {
                $result = array(
                    'tid'       => '',
                    'action'    => '',
                    'status'    => '',
                    'created'   => 0,
                    'changed'   => 0,
                    'data'      => '',
                );
                $result['tid'] = isset($records['tid']) ? trim($records['tid']) : '';
                $result['action'] = isset($records['action']) ? trim($records['action']) : '';
                $result['status'] = isset($records['status']) ? trim($records['status']) : '';
                $result['created'] = isset($records['created']) ? intval($records['created']) : 0;
                $result['changed'] = isset($records['changed']) ? intval($records['changed']) : 0;
                $data = '';
                if(isset($records['data'])) {
                    $serstr = @base64_decode($records['data']);
                    if(isset($serstr) && $serstr !== FALSE) {
                        $data = $serstr;
                    }
                }
                $result['data'] = $data;
                return $result;
            }
        } catch(Exception $ex) {
            return NULL;
        }
    } // End tableRsmartSepaReadByTID
    
    /**
     * Reads all or particular TIDs.
     * 
     * @param int $durationSeconds
     *     If $durationSeconds is 0, all existing TIDs are read.
     *     If $durationSeconds is > 0 then those TIDs are read
     *     where the changed timestamp is than this number of seconds
     *     in the past. 
     * 
     * @param string $action
     *     If $action is not empty, also the action column must match
     *     (Default is an empty string)
     * 
     * @param string $status
     *     If $status is not empty, also the status column must match
     *     (Default is an empty string)
     * 
     * @param string $sortOrder
     *     'asc'  : Sort the TIDs by changed timestamp ascending
     *     'desc' : Sort the TIDs by changed timestamp descending
     *     ''     : Not sorting
     *     (Default is '')
     * 
     * @return array
     *     Either an empty array or an array containing only the TIDs
     *     of the selected records
     */
    public static function tableRsmartSepaReadAllTIDs($durationSeconds = 0, $action = '', $status = '', $sortOrder = '') {
        $resultArray = array();
        
        $durationSeconds = isset($durationSeconds) ? (is_int($durationSeconds) ? abs($durationSeconds) : 0) : 0;
        $now = time();
        $now = $now - $durationSeconds;
        
        $action = isset($action) ? (is_string($action) ? trim($action) : '') : '';
        if($action != '') {
            $action = self::escapeSql($action);
            if(strlen($action) > 20) {
                $action = substr($action, 0, 20);
            }
        }
        $status = isset($status) ? (is_string($status) ? trim($status) : '') : '';
        if($status != '') {
            $status = self::escapeSql($status);
            if(strlen($status) > 20) {
                $status = substr($status, 0, 20);
            }
        }
        
        $sortOrder = isset($sortOrder) ? (is_string($sortOrder) ? strtolower(trim($sortOrder)) : '') : '';
        
        try {
            $sql = "select tid from rsmartsepa ";
            $where = '';
            if($durationSeconds > 0) {
                $where = " WHERE changed <= " . strval($now);
            }
            if($action != '') {
                if($where == '') {
                    $where = " WHERE action = '" . $action . "' ";
                }
                else {
                    $where = $where . " AND action = '" . $action . "' ";
                }
            }
            if($status != '') {
                if($where == '') {
                    $where = " WHERE status = '" . $status . "' ";
                }
                else {
                    $where = $where . " AND status = '" . $status . "' ";
                }
            }
            if($sortOrder == 'asc') {
                if($where == '') {
                    $where = " ORDER BY changed ASC";
                }
                else {
                    $where = $where . " ORDER BY changed ASC";
                }                
            }
            else if($sortOrder == 'desc') {
                if($where == '') {
                    $where = " ORDER BY changed DESC";
                }
                else {
                    $where = $where . " ORDER BY changed DESC";
                }                
            }
            $sql = $sql . $where;
            $query = xtc_db_query($sql);
            while ($record = xtc_db_fetch_array($query)) {
                $tid = isset($record['tid']) ? $record['tid'] : '';
                if($tid != '') {
                    $resultArray[] = $tid;
                }
            }
            
            
        } catch(Exception $ex) {}
        
        return $resultArray;
    } // End tableRsmartSepaReadAllTIDs
    
    /**
     * Delete a record by its TID.
     * 
     * @param string $tid
     *    The TID to delete
     * 
     * @return boolean
     *    TRUE or FALSE 
     */
    public static function tableRsmartSepaDeleteByTID($tid = '') {
        $tid = isset($tid) ? (is_string($tid) ? trim($tid) : '') : '';
        if($tid == '') {
            return FALSE;
        }
        $tid = self::escapeSql($tid);
        if(strlen($tid) > 128) {
            $tid = substr($tid, 0, 128);
        }
        
        try {
            $sql = "DELETE FROM rsmartsepa WHERE tid = '" . $tid . "'";
            xtc_db_query($sql);
            return TRUE;
        } catch(Exception $ex) {
            return FALSE;
        }
    } // End tableRsmartSepaDeleteByTID
    
    /**
     * Inserts or updates a Raautil_DataStore.
     * 
     * @param array $record
     *    A structured array in the format:
     * 
     *    array(
     *       'tid'          => (string) the TID,
     *       'action'       => (string) the last action,
     *       'status'       => (string) the last status,
     *       'created'      => (int) the creation timestamp,
     *       'changed'      => (int) the changed timestamp,
     *       'data'         => (string) a serialized version of the Raautil_DataStore
     *    )
     * 
     * @return boolean
     *    TRUE or FALSE 
     */
    public static function tableRsmartSepaSave($record = array()) {
        if(!isset($record)) {
            return FALSE;
        }
        else if(!is_array($record)) {
            return FALSE;
        }
        
        if(!isset($record['tid']) || 
           !isset($record['action']) || 
           !isset($record['status']) || 
           !isset($record['created']) || 
           !isset($record['changed']) || 
           !isset($record['data'])) {
            return FALSE;
        }
        
        $tid = self::escapeSql(trim($record['tid']));
        if($tid == '') {
            return FALSE;
        }
        if(strlen($tid) > 128) {
            $tid = substr($tid, 0, 128);
        }
        $existing = self::tableRsmartSepaExistsTID($tid);
        if($existing == TRUE) {
            return self::tableRsmartSepaUpdate($record, FALSE);
        }
        else {
            return self::tableRsmartSepaInsert($record, FALSE);
        }
    } // End tableRsmartSepaSave
    
    /**
     * Inserts a Raautil_DataStore.
     * 
     * @param array $record
     *    A structured array in the format:
     * 
     *    array(
     *       'tid'          => (string) the TID,
     *       'action'       => (string) the last action,
     *       'status'       => (string) the last status,
     *       'created'      => (int) the creation timestamp,
     *       'changed'      => (int) the changed timestamp,
     *       'data'         => (string) a serialized version of the Raautil_DataStore
     *    )
     * 
     * @param boolean $checkExisting
     *    TRUE to check if the record already exists, FALSE if not
     *    (Default is TRUE)
     * 
     * @return boolean
     *    TRUE or FALSE 
     */
    public static function tableRsmartSepaInsert($record = array(), $checkExisting = TRUE) {
        if(!isset($record)) {
            return FALSE;
        }
        else if(!is_array($record)) {
            return FALSE;
        }
        
        if(!isset($record['tid']) || 
           !isset($record['action']) || 
           !isset($record['status']) || 
           !isset($record['created']) || 
           !isset($record['changed']) || 
           !isset($record['data'])) {
            return FALSE;
        }
        
        $tid = self::escapeSql(trim($record['tid']));
        if($tid == '') {
            return FALSE;
        }
        if(strlen($tid) > 128) {
            $tid = substr($tid, 0, 128);
        }
        $action = self::escapeSql(trim($record['action']));
        if(strlen($action) > 20) {
            $action = substr($action, 0, 20);
        }
        $status = self::escapeSql(trim($record['status']));
        if(strlen($status) > 20) {
            $status = substr($status, 0, 20);
        }
        
        $checkExisting = isset($checkExisting) ? ($checkExisting == TRUE ? TRUE : FALSE) : TRUE;
        if($checkExisting == TRUE) {
            $existing = self::tableRsmartSepaExistsTID($tid);
        }
        else {
            $existing = FALSE;
        }
        if($existing == FALSE) {
            // Insert
            $datab64 = @base64_encode($record['data']);
            $datab64 = self::escapeSql($datab64);
            $record['created'] = time();
            $record['changed'] = time();
            try {
                $sql = "INSERT INTO rsmartsepa (`tid`, `action`, `status`, `created`, `changed`, `data`) VALUES " .
                       "(" .
                       "'" . $tid . "', " .
                       "'" . $action . "', " .
                       "'" . $status . "', " .
                       "'" . $record['created'] . "', " .
                       "'" . $record['changed'] . "', " .
                       "'" . $datab64 . "')";
                $result = xtc_db_query($sql);
                return TRUE;
            } catch(Exception $ex) {
                return FALSE;
            }            
        }
        else {
            return FALSE;
        }
    } // End tableRsmartSepaInsert
    
    /**
     * Updates a Raautil_DataStore.
     * 
     * @param array $record
     *    A structured array in the format:
     * 
     *    array(
     *       'tid'          => (string) the TID,
     *       'created'      => (int) the creation timestamp,
     *       'changed'      => (int) the changed timestamp,
     *       'data'         => (string) a serialized version of the Raautil_DataStore
     *    )
     * 
     * @param boolean $checkExisting
     *    TRUE to check if the record already exists, FALSE if not
     *    (Default is TRUE)
     * 
     * @return boolean
     *    TRUE or FALSE 
     */
    public static function tableRsmartSepaUpdate($record = array(), $checkExisting = TRUE) {
        if(!isset($record)) {
            return FALSE;
        }
        else if(!is_array($record)) {
            return FALSE;
        }
        
        if(!isset($record['tid']) || 
           !isset($record['action']) || 
           !isset($record['status']) || 
           !isset($record['created']) || 
           !isset($record['changed']) || 
           !isset($record['data'])) {
            return FALSE;
        }
        
        $tid = self::escapeSql(trim($record['tid']));
        if($tid == '') {
            return FALSE;
        }
        $action = self::escapeSql(trim($record['action']));
        if(strlen($action) > 20) {
            $action = substr($action, 0, 20);
        }
        $status = self::escapeSql(trim($record['status']));
        if(strlen($status) > 20) {
            $status = substr($status, 0, 20);
        }
        
        $checkExisting = isset($checkExisting) ? ($checkExisting == TRUE ? TRUE : FALSE) : TRUE;
        if($checkExisting == TRUE) {
            $existing = self::tableRsmartSepaExistsTID($tid);
        }
        else {
            $existing = TRUE;
        }
        if($existing == TRUE) {
            // Update
            $datab64 = @base64_encode($record['data']);
            $datab64 = self::escapeSql($datab64);
            $record['changed'] = time();
            try {
                $sql = "UPDATE rsmartsepa SET data = '" . $datab64 . "', " .
                       "action = '" . $action . "', " .
                       "status = '" . $status . "', " .
                       "changed = '" . $record['changed'] . "' " .
                       " WHERE tid = '" . $tid . "'";
                $result = xtc_db_query($sql);
                return TRUE;
            } catch(Exception $ex) {
                return FALSE;
            }
        }
        else {
            return FALSE;
        }
    } // End tableRsmartSepaUpdate
    
    
    /**
     * Converts a PHP variable into JSON format.
     * We use HTML-safe strings, i.e. with <, > and & escaped.
     *
     * @param mixed $var
     *   The variable to encode.
     *   Usually this is an array
     *
     * @return string
     *   The JSON format
     */
    public static function jsonEncode($var) {
        // json_encode() does not escape <, > and &, so we do it with str_replace().
        return str_replace(array('<', '>', '&'), array('\u003c', '\u003e', '\u0026'), json_encode($var));
    } // End jsonEncode
    
    public static function createInlineConfigCode($jsConfigClassName = '', $configParams = array()) {
        $jsConfigClassName = isset($jsConfigClassName) ? (is_string($jsConfigClassName) ? trim($jsConfigClassName) : '') : '';
        $configParams = isset($configParams) ? (is_array($configParams) ? $configParams : array()) : array();
        if($jsConfigClassName == '') {
            return '';
        }
    
        // json_encode() does not escape <, > and &, so we do it with str_replace().
        $json = str_replace(array('<', '>', '&'), array('\u003c', '\u003e', '\u0026'), json_encode($configParams));
        $jsCode = 'jQuery.extend(' . $jsConfigClassName . ', ' . $json . ');';
        return $jsCode;
        //$inlineCode = '<script type="text/javascript">' . $jsCode . '</script>';
        //return $inlineCode;
    } // End createInlineConfigCode
    
    
    
    /**
     * This function is used to return a variable (usually an array) in JSON format as result of a request.
     * The header is 'content-type: application/json'.
     *
     * @param mixed $var
     *   The variable to return.
     *   Usually this is an array
     *
     * @param boolean $doExit
     *   TRUE if exit; should be called at the end to end the PHP script, FALSE otherwise.
     *   (Default is TRUE)
     *   If FALSE, the caller of this function has to do some cleanup work and then
     *   call exit;
     */
    public static function jsonOutput($var = NULL, $doExit = TRUE) {
        // Close Database Connection
        if(function_exists('xtc_db_close')) {
            xtc_db_close();
        }
        
        // Clean output buffers before
        if (ob_get_level()) {
            ob_end_clean();
        }
        // We are returning JSON, so tell the browser.
        header('content-type: application/json');

        if (isset($var)) {
            $encoded = self::jsonEncode($var);
            echo $encoded;
        }
        $doExit = isset($doExit) ? (is_bool($doExit) ? $doExit : TRUE) : TRUE;
        if($doExit === TRUE)
            exit;
    } // End jsonOutput
    
    /**
     * This function is used to return html as result of a request.
     *
     * @param string $html
     *   The html code.
     *
     * @param boolean $doExit
     *   TRUE if exit; should be called at the end to end the PHP script, FALSE otherwise.
     *   (Default is TRUE)
     *   If FALSE, the caller of this function has to do some cleanup work and then
     *   call exit;
     */
    public static function htmlOutput($html = '', $doExit = TRUE) {
        // Close Database Connection
        if(function_exists('xtc_db_close')) {
            xtc_db_close();
        }
        
        // Clean output buffers before
        if (ob_get_level()) {
            ob_end_clean();
        }
        // We are returning HTML, so tell the browser.
        header('content-type: text/html');

        $html = isset($html) ? (is_string($html) ? $html : '') : '';
        print($html);
        $doExit = isset($doExit) ? (is_bool($doExit) ? $doExit : TRUE) : TRUE;
        if($doExit === TRUE)
            exit;
    } // End htmlOutput

    /**
     * This function is used to return plain text as result of a request.
     *
     * @param string $text
     *   The text to return.
     *
     * @param boolean $doExit
     *   TRUE if exit; should be called at the end to end the PHP script, FALSE otherwise.
     *   (Default is TRUE)
     *   If FALSE, the caller of this function has to do some cleanup work and then
     *   call exit;
     */
    public static function textOutput($text = '', $doExit = TRUE) {
        // Close Database Connection
        if(function_exists('xtc_db_close')) {
            xtc_db_close();
        }
        
        // Clean output buffers before
        if (ob_get_level()) {
            ob_end_clean();
        }
        // We are returning text, so tell the browser.
        header('content-type: text/plain');

        $text = isset($text) ? (is_string($text) ? $text : '') : '';
        print($text);
        $doExit = isset($doExit) ? (is_bool($doExit) ? $doExit : TRUE) : TRUE;
        if($doExit === TRUE)
            exit;
    } // End textOutput
    
    public static function ajaxReturn($status = '9', $result = 'PENDING', $hash = '') {
        $resultArray = array(
            'status'        => '9',
            'result'        => 'FAILURE',
            'hash'          => '',
        );
        $status = isset($status) ? (is_string($status) ? trim($status) : '9') : '9';
        if($status != '0' && $status != '8' && $status != '9') {
            $status = '9';
        }
        $resultArray['status'] = $status;
        $result = isset($result) ? (is_string($result) ? trim($result) : 'FAILURE') : 'FAILURE';
        $resultArray['result'] = $result;
        $hash = isset($hash) ? (is_string($hash) ? trim($hash) : '') : '';
        $resultArray['hash'] = $hash;
        self::jsonOutput($resultArray, TRUE);
    } // End ajaxReturn
    
    public static function getTemplateNames() {
        $result = array();
        $templates = array();
        $dir = self::getTemplatesDirectory();
        $fileList = scandir($dir);
        if(isset($fileList)) {
            if(is_array($fileList)) {
                foreach($fileList as $filename) {
                    if ($filename === '.' || $filename === '..') {
                        continue;
                    }
                    
                    $ext = '.tpl.php';
                    if(strpos($filename, $ext) !== FALSE) {
                        $filename = substr($filename, 0, strlen($filename) - strlen($ext));
                        if(strpos($filename, '_main') !== FALSE) {
                            $templates[] = $filename;
                        }
                    }
                }
            }
        }
        
        $result[] = 'rsmartsepadefault';
        foreach($templates as $name) {
            if($name == 'rsmartsepadefault') {
                continue;
            }
            $result[] = $name;
        }        
        return $result;
    } // End getTemplateNames
    
    public static function getTemplateDescriptions() {
        $result = '';
        $templateNames = self::getTemplateNames();
        $dir = self::getTemplatesDirectory();
        foreach($templateNames as $name) {
            $infofilePath = $dir . '/' . $name . '.info';
            if(is_file($infofilePath)) {
                $cont = @file_get_contents($infofilePath);
                if(isset($cont) && $cont !== FALSE) {
                    $cont = trim($cont);
                    $parameters = array(
                        'rsmartsepaaction'      => 'rsmartsepatemplatetest',
                        'simulation'            => 'false',
                        'rsmartsepatemplate'    => trim($name),
                    );
                    $previewUrl = RsmartsepaHelper::createCallbackUrl($parameters, 'NONSSL', true, false, false);
                    $previewTitle = MODULE_PAYMENT_RSMARTSEPA_STR_PREVIEWLINKTITLE;
                    $previewLink = '<a href="' . $previewUrl . '" target="_blank" title="' . $previewTitle . '" ><b>' . $name . '</b></a>';
                    //$li = '<li><b>' . $name . '</b><br/>' . $cont . '</li>';
                    $li = '<li>' . $previewLink . '<br/>' . $cont . '</li>';
                    if($result == '') {
                        $result = '<ul>' . $li;
                    }
                    else {
                        $result = $result . $li;
                    }
                }
            }
        }
        if($result != '') {
            $result = $result . '</ul>';
        }
        return $result;
    } // End getTemplateDescriptions
    
    
    /**
     * Checks if a template in the subdirectory 'resources/templates' exists.
     * 
     * @param string $templateName
     *    The name of a template without the extension '.tpl.php'
     * 
     * @return boolean 
     *    TRUE if the template exists, FALSE otherwise
     */
    public static function existsTemplate($templateName = '') {
        $result = FALSE;
        $templateName = isset($templateName) ? trim($templateName) : '';
        if($templateName != '') {
            $templatePath = self::getTemplatesDirectory() . '/' . $templateName;
            $pos = strpos($templatePath, '.tpl.php');
            if($pos === FALSE) {
                $templatePath .= '.tpl.php';
            }
            if(is_file($templatePath)) {
                $result = TRUE;
            }
        }
        return $result;
    } // End existsTemplate
    
    /**
     * Renders a a template in the subdirectory 'resources/templates'.
     * 
     * @param string $templateName
     *    The name of a template without the extension '.tpl.php'
     * 
     * @param array $variablesArray
     *    A structured array with key/value pairs that become
     *    local variables within the template
     * 
     * @return string
     *    The rendered html code or an empty string 
     */
    public static function renderTemplate($templateName = '', $variablesArray = null) {
        $result = '';
        $templateName = isset($templateName) ? trim($templateName) : '';
        if($templateName != '') {
            $templatePath = self::getTemplatesDirectory() . '/' . $templateName;
            $pos = strpos($templatePath, '.tpl.php');
            if($pos === FALSE) {
                $templatePath .= '.tpl.php';
            }
            
            if(is_file($templatePath)) {
                if(isset($variablesArray)) {
                    if(is_array($variablesArray)) {
                        extract($variablesArray, EXTR_SKIP);
                    }
                }
                
                ob_start(); // Start output buffering
                include $templatePath;
                $result = ob_get_clean(); // Stop output buffering
            }
        }
        
        return $result;
    } // End renderTemplate
    
    public static function createCheckMatchServerMarkup() {
        $result = '';
        $moduleEnabled = self::getConstantValueAsBoolean('MODULE_PAYMENT_RSMARTSEPA_STATUS', FALSE);
        if($moduleEnabled == FALSE) {
            return $result;
        }
        
        $resDir = self::getResourcesDirectory();
        $filePATH = $resDir . '/js/checkmatchserver.js';
        $content = FALSE;
        if(is_file($filePATH)) {
            $content = @file_get_contents($filePATH);
        }
        if($content !== FALSE) {
            $callbackurl = RsmartsepaHelper::createCallbackUrl(array('rsmartsepaaction' => 'rsmartsepaajax'), 'SSL');
            $content = str_replace('@callbackurl@', $callbackurl, $content);
            // data-rsmartseparole='checkmatchserverbutton'
            $markup = '<span data-rsmartseparole="checkmatchserverbutton" style="cursor: pointer; padding: 2px; border: solid 1px #000000; background-color: #0000ff; color: #ffffff;">Test Connection to Matchserver</span>' .
                      "\r\n" .
                      '<script type="text/javascript">' . 
                      $content .
                      '</script>';
            $result = $markup;
        }
        
        
        return $result;
    } // End createCheckMatchServerMarkup
    
    
    
    public static function orderExists($order_id = 0) {
        $order_id = (int)$order_id;
        $order_query = xtc_db_query('select * from ' . TABLE_ORDERS . " where orders_id = '" . $order_id . "'");
        if (xtc_db_num_rows($order_query) < 1) {
            // Order not found
            return FALSE;
        }
        else {
            return TRUE;
        }
    } // End orderExists
    
    public static function orderUpdate($order_id = 0, $paymentSuccess = FALSE, $historyMessage = '') {
        $order_id = (int)$order_id;
        $paymentSuccess = isset($paymentSuccess) ?($paymentSuccess == TRUE ? TRUE : FALSE) : FALSE;
        if(!self::orderExists($order_id)) {
            return FALSE;
        }
        
        $historyMessage = isset($historyMessage) ? (is_string($historyMessage) ? trim($historyMessage) : '') : '';
        if($paymentSuccess == TRUE) {
            $newStatus = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_OK', '1');
            $sql = "update " . TABLE_ORDERS . " set orders_status = '" . $newStatus . 
                   "', last_modified = now() where orders_id = '" . $order_id . "'";
            if(xtc_db_query($sql)) {
                // Status history
                $sql_data_array = array(
                    'orders_id'         => $order_id,
                    'orders_status_id'  => $newStatus,
                    'date_added'        => 'now()',
                    'customer_notified' => 1, // TRUE
                    'comments'          => $historyMessage,
                );
                xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        else {
            // Payment failed
            $newStatus = RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_ORDER_STATUS_ID_ERROR', '99');
            $sql = "update " . TABLE_ORDERS . " set orders_status = '" . $newStatus . 
                   "', last_modified = now() where orders_id = '" . $order_id . "'";
            if(xtc_db_query($sql)) {
                // Status history
                $sql_data_array = array(
                    'orders_id'         => $order_id,
                    'orders_status_id'  => $newStatus,
                    'date_added'        => 'now()',
                    'customer_notified' => 0, // TRUE
                    'comments'          => $historyMessage,
                );
                xtc_db_perform(TABLE_ORDERS_STATUS_HISTORY, $sql_data_array);

                return TRUE;
            }
            else {
                return FALSE;
            }            
        }
    } // End orderUpdate
    
//    public static function getCronKey($urlEncoded = FALSE) {
//        $iniFilePath = str_replace("\\", '/', dirname(__FILE__) . '/terminaldata.ini');
//        if(!is_file($iniFilePath)) {
//            return 'undefined';
//        }
//        $valueArray = @parse_ini_file($iniFilePath);
//        if(!isset($valueArray)) {
//            return 'undefined';
//        }
//        else if(!is_array($valueArray)) {
//            return 'undefined';
//        }
//        
//        $key = isset($valueArray['key']) ? (is_string($valueArray['key']) ? trim($valueArray['key']) : '') : '';
//        $id = isset($valueArray['sellerId']) ? (is_string($valueArray['sellerId']) ? trim($valueArray['sellerId']) : '') : '';
//        if($key == '' || $id == '') {
//            return 'undefined';
//        }
//        
//        $hmacHashFunction = 'sha256';
//        $result =  base64_encode(pack("H*", hash_hmac($hmacHashFunction, $id, $key)));
//        $urlEncoded = isset($urlEncoded) ? ($urlEncoded == TRUE ? TRUE : FALSE) : FALSE;
//        if($urlEncoded == TRUE) {
//            $result = urlencode($result);
//        }
//        return $result;
//    } // End getCronKey
    
    public static function getCronKey($urlEncoded = FALSE) {
        try {
            self::startLibrary();
            $RsmartsepaTerminalDataProviderConfig = new RsmartsepaTerminalDataProviderConfig();
            $valueArray = $RsmartsepaTerminalDataProviderConfig->toArray();
            $key = isset($valueArray['key']) ? (is_string($valueArray['key']) ? trim($valueArray['key']) : '') : '';
            $id = isset($valueArray['sellerId']) ? (is_string($valueArray['sellerId']) ? trim($valueArray['sellerId']) : '') : '';
            if($key == '' || $id == '') {
                return 'undefined';
            }
            
            $hmacHashFunction = 'sha256';
            $result =  base64_encode(pack("H*", hash_hmac($hmacHashFunction, $id, $key)));
            $urlEncoded = isset($urlEncoded) ? ($urlEncoded == TRUE ? TRUE : FALSE) : FALSE;
            if($urlEncoded == TRUE) {
                $result = urlencode($result);
            }
            return $result;            
        } catch (Exception $ex) {
            return 'undefined';
        }
    } // End getCronKey
    
    
    public static function cronInit($msg = '') {
        self::$cronMessages = array();
        self::cronAddMessage($msg);
    } // End cronInit
    
    public static function cronAddMessage($msg = '') {
        $msg = isset($msg) ? (is_string($msg) ? trim($msg) : '') : '';
        if($msg != '') {
            $msgEntry = array(
                'ts'    => time(),
                'msg'   => $msg,
            );
            self::$cronMessages[] = $msgEntry;
        }
    } // End cronAddMessage
    
    public static function cronGetMessagesAsString() {
        $result = '';
        if(count(self::$cronMessages) > 0) {
            foreach(self::$cronMessages as $msgEntry) {
                $line = date('Y.m.d-H:i:s', $msgEntry['ts']) .
                        ': ' .
                        $msgEntry['msg'];
                if($result == '') {
                    $result = $line;
                }
                else {
                    $result = $result . "\r\n" . $line;
                }
            }
        }
        return $result;
    } // End cronGetMessagesAsString
    
} // End class RsmartsepaHelper

