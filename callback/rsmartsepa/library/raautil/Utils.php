<?php
/* --------------------------------------------------------------
  Utils.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_Utils {
    
    const CONTENT_TYPE_HTML             = 'text/html';
    const CONTENT_TYPE_JAVASCRIPT       = 'text/javascript';
    const CONTENT_TYPE_CSS              = 'text/css';
    const CONTENT_TYPE_IMAGE_JPG        = 'image/jpeg';
    const CONTENT_TYPE_IMAGE_GIF        = 'image/gif';
    const CONTENT_TYPE_IMAGE_PNG        = 'image/png';
    
    
    private static $templateFolder = '';
    
    /**
     * Returns a status report.
     * 
     * @return array 
     *    Returns a status array
     * 
     *       array(
     *          'certificate_file'  => array(
     *              'existing'          => (boolean) TRUE if cacert.crt exists, FALSE otherwise,
     *              'path'              => (string) The path where cacert.crt is expected,
     *              'size'              => (int) The filesize in bytes if the file exists, 0 otherwise,
     *              'ctime'             => (int) The filectime() timestamp if the file exists, -1 otherwise,
     *              'mtime'             => (int) The filemtime() timestamp if the file exists, -1 otherwise,
     *          ),
     *          'existing_functions' => array(
     *              'cert_check'        => (boolean) TRUE if the function 'cert_check' exists, FALSE otherwise,
     *              'openssl_x509_read' => (boolean) TRUE if the function 'openssl_x509_read' exists, FALSE otherwise,
     *              'openssl_x509_free' => (boolean) TRUE if the function 'openssl_x509_free' exists, FALSE otherwise,
     *              'openssl_verify'    => (boolean) TRUE if the function 'openssl_x509_free' exists, FALSE otherwise,
     *          ),
     *       )
     */
    public static function getStatusReport() {
        $certFILE_PATH = dirname(dirname(__FILE__)) . '/cacert.crt';
        $certFILE_EXISTING = is_file($certFILE_PATH);
        $certFILE_SIZE = 0;
        $certFILE_CTIME = -1;
        $certFILE_MTIME = -1;
        if($certFILE_EXISTING == TRUE) {
            $certFILE_SIZE = @filesize($certFILE_PATH);
            $certFILE_CTIME = @filectime($certFILE_PATH);
            $certFILE_MTIME = @filemtime($certFILE_PATH);
        }
        
        $result = array(
            'certificate_file'  => array(
                'existing'          => $certFILE_EXISTING,
                'path'              => $certFILE_PATH,
                'size'              => $certFILE_SIZE,
                'ctime'             => $certFILE_CTIME,
                'mtime'             => $certFILE_MTIME,
            ),
            'existing_functions' => array(
                'cert_check'        => function_exists('cert_check'),
                'openssl_x509_read' => function_exists('openssl_x509_read'),
                'openssl_x509_free' => function_exists('openssl_x509_free'),
                'openssl_verify'    => function_exists('openssl_verify'),
            ),
        );

        return $result;
    } // End getStatusReport
    
    
    /**
     * Returns the path of a temporary folder by using the
     * function sys_get_temp_dir() if it exists.
     * 
     * @param string $fallbackFolder
     *    The path of a fallback folder for the case that the function
     *    sys_get_temp_dir() does not exist.
     * 
     * @param array $options
     *    An optional array of options in the format
     * 
     *    array(
     *       'writable'     => (boolean) TRUE | FALSE, // If the temp folder or the fallbackFolder must be writable
     *       'readable'     => (boolean) TRUE | FALSE, // If the temp folder or the fallbackFolder must be readable
     *    )
     * 
     * @return string 
     *    If the function sys_get_temp_dir() exists and depending on the options the sys_get_temp_dir() folder
     *    is writable AND readable, sys_get_temp_dir() is returned.
     *    If the function sys_get_temp_dir() does not exist and depending on the options the fallbackFolder
     *    is writable AND readable, the fallbackFolder is returned.
     *    Otherwise an empty string is returned.
     */
    public static function getTempFolder($fallbackFolder = '', $options = array()) {
        $fallbackFolder = isset($fallbackFolder) ? (is_string($fallbackFolder) ? trim($fallbackFolder) : '') : '';
        $options = isset($options) ? (is_array($options) ? $options : array()) : array();
        $mustBeWritable = isset($options['writable']) ? ($options['writable'] == TRUE ? TRUE : FALSE) : FALSE;
        $mustBeReadable = isset($options['readable']) ? ($options['readable'] == TRUE ? TRUE : FALSE) : FALSE;
        
        if(!is_dir($fallbackFolder)) {
            $fallbackFolder = '';
        }
        else {
            if($mustBeWritable == TRUE && !is_writable($fallbackFolder)) {
                $fallbackFolder = '';
            }
            if($fallbackFolder != '' && $mustBeReadable == TRUE && !is_readable($fallbackFolder)) {
                $fallbackFolder = '';
            }
        }
        $result = $fallbackFolder;
        if(function_exists('sys_get_temp_dir')) {
            $tmpDir = sys_get_temp_dir();
            if($mustBeWritable == TRUE && !is_writable($tmpDir)) {
                $fallbackFolder = '';
            }
            if($tmpDir != '' && $mustBeReadable == TRUE && !is_readable($tmpDir)) {
                $tmpDir = '';
            }
            if($tmpDir != '') {
                $result = $tmpDir;
            }
        }
        return $result;
    } // End getTempFolder
    
    /**
     * Checks whether a string is valid UTF-8.
     *
     * All functions designed to filter input should use drupal_validate_utf8
     * to ensure they operate on valid UTF-8 strings to prevent bypass of the
     * filter.
     *
     * When text containing an invalid UTF-8 lead byte (0xC0 - 0xFF) is presented
     * as UTF-8 to Internet Explorer 6, the program may misinterpret subsequent
     * bytes. When these subsequent bytes are HTML control characters such as
     * quotes or angle brackets, parts of the text that were deemed safe by filters
     * end up in locations that are potentially unsafe; An onerror attribute that
     * is outside of a tag, and thus deemed safe by a filter, can be interpreted
     * by the browser as if it were inside the tag.
     *
     * The function does not return FALSE for strings containing character codes
     * above U+10FFFF, even though these are prohibited by RFC 3629.
     *
     * @param $text
     *   The text to check.
     *
     * @return
     *   TRUE if the text is valid UTF-8, FALSE if not.
     */
    public static function validateUTF8($text) {
      if (strlen($text) == 0) {
        return TRUE;
      }
      // With the PCRE_UTF8 modifier 'u', preg_match() fails silently on strings
      // containing invalid UTF-8 byte sequences. It does not reject character
      // codes above U+10FFFF (represented by 4 or more octets), though.
      return (preg_match('/^./us', $text) == 1);
    } // End validateUTF8
    
    /**
     * Encodes special characters in a plain-text string for display as HTML.
     *
     * Also validates strings as UTF-8 to prevent cross site scripting attacks on
     * Internet Explorer 6.
     *
     * @param $text
     *   The text to be checked or processed.
     *
     * @return
     *   An HTML safe version of $text, or an empty string if $text is not
     *   valid UTF-8.
     *
     * @see drupal_validate_utf8()
     * @ingroup sanitization
     */    
    public static function checkPlain($text = '') {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    } // End checkPlain
    
    /**
     * Retrieves a GET or POST Request value from $_REQUEST.
     * 
     * @param string $name
     *    The name of the value to get
     * 
     * @param null|mixed $dflt
     *    The default value if $_REQUEST[$name] does not exist.
     *    Defaults to NULL
     * 
     * @param boolean $checkPlain
     *    A flag telling the function if the found result value $_REQUEST[$name]
     *    should be checked by check_plain()
     *    Defaults to TRUE
     *  
     * @return null|mixed
     *    Either the found $_REQUEST[$name] value or $dflt
     */
    public static function getRequestValue($name = '', $dflt = null, $checkPlain = TRUE) {
        $result = $dflt;
        $name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
        if($name == '') {
            return $result;
        }
        if(isset($_REQUEST[$name])) {
            $checkPlain = isset($checkPlain) ? (is_bool($checkPlain) ? $checkPlain : TRUE) : TRUE;
            $result = $_REQUEST[$name];
            if($checkPlain == TRUE) {
                $result = self::checkPlain($result);
            }
        }
        return $result;    
    } // End getRequestValue
    
    /**
     * Retrieves a GET Request value from $_GET.
     * 
     * @param string $name
     *    The name of the value to get
     * 
     * @param null|mixed $dflt
     *    The default value if $_GET[$name] does not exist.
     *    Defaults to NULL
     * 
     * @param boolean $checkPlain
     *    A flag telling the function if the found result value $_GET[$name]
     *    should be checked by check_plain()
     *    Defaults to TRUE
     *  
     * @return null|mixed
     *    Either the found $_GET[$name] value or $dflt
     */
    public static function getRequestValueGET($name = '', $dflt = null, $checkPlain = TRUE) {
        $result = $dflt;
        $name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
        if($name == '') {
            return $result;
        }
        if(isset($_GET[$name])) {
            $checkPlain = isset($checkPlain) ? (is_bool($checkPlain) ? $checkPlain : TRUE) : TRUE;
            $result = $_GET[$name];
            if($checkPlain == TRUE) {
                $result = self::checkPlain($result);
            }
        }
        return $result;
    } // End getRequestValueGET
    
    /**
     * Retrieves a POST Request value from $_POST.
     * 
     * @param string $name
     *    The name of the value to get
     * 
     * @param null|mixed $dflt
     *    The default value if $_POST[$name] does not exist.
     *    Defaults to NULL
     * 
     * @param boolean $checkPlain
     *    A flag telling the function if the found result value $_POST[$name]
     *    should be checked by check_plain()
     *    Defaults to TRUE
     *  
     * @return null|mixed
     *    Either the found $_POST[$name] value or $dflt
     */
    public static function getRequestValuePOST($name = '', $dflt = null, $checkPlain = TRUE) {
        $result = $dflt;
        $name = isset($name) ? (is_string($name) ? trim($name) : '') : '';
        if($name == '') {
            return $result;
        }
        if(isset($_POST[$name])) {
            $checkPlain = isset($checkPlain) ? (is_bool($checkPlain) ? $checkPlain : TRUE) : TRUE;
            $result = $_POST[$name];
            if($checkPlain == TRUE) {
                $result = self::checkPlain($result);
            }
        }
        return $result;
    } // End getRequestValuePOST
    
    /**
     * Checks if a string starts with a given prefix.
     * 
     * @param string $str
     *    The string to check
     * 
     * @param string $prefix
     *    The prefix to check. It is case sensitive
     * 
     * @return boolean
     *    TRUE if  the string starts with the given prefix, FALSE otherwise
     */
    public static function stringStartsWith($str = '', $prefix = '') {
        $str = isset($str) ? $str : '';
        $prefix = isset($prefix) ? $prefix : '';

        if(strlen($str) >= strlen($prefix)) {
            if(strlen($str) == strlen($prefix)) {
                if($str == $prefix)
                    return TRUE;
                else
                    return FALSE;
            }
            else {
                $part = substr($str, 0, strlen($prefix));
                if($part == $prefix)
                    return TRUE;
                else
                    return FALSE;
            }
        }
        else {
            return FALSE;
        }
    } // End stringStartsWith
    
    
    /**
     * Checks if a string ends with a given suffix.
     * 
     * @param string $str
     *    The string to check
     * 
     * @param string $suffix
     *    The suffix to check. It is case sensitive
     * 
     * @return boolean
     *    TRUE if  the string ends with the given suffix, FALSE otherwise
     */
    public static function stringEndsWith($str = '', $suffix = '') {
        $str = isset($str) ? $str : '';
        $suffix = isset($suffix) ? $suffix : '';

        if(strlen($str) >= strlen($suffix)) {
            if(strlen($str) == strlen($suffix)) {
                if($str == $suffix)
                    return TRUE;
                else
                    return FALSE;
            }
            else {
                $startPos = strlen($str) - strlen($suffix);
                $part = substr($str, $startPos, strlen($suffix));
                if($part == $suffix)
                    return TRUE;
                else
                    return FALSE;
            }
        }
        else {
            return FALSE;
        }
    } // End stringEndsWith
    
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
     * Delivers a resource.
     * 
     * @param string $data
     *    The data to deliver
     * 
     * @param string $contentType
     *    The content type.
     *    This must be one of the constants
     *       CONTENT_TYPE_HTML, CONTENT_TYPE_JAVASCRIPT, CONTENT_TYPE_CSS
     *       CONTENT_TYPE_IMAGE_GIF, CONTENT_TYPE_IMAGE_JPG, CONTENT_TYPE_IMAGE_PNG
     * 
     * @param boolean $doExit
     *   TRUE if exit; should be called at the end to end the PHP script, FALSE otherwise.
     *   (Default is TRUE)
     *   If FALSE, the caller of this function has to do some cleanup work and then
     *   call exit;
     */
    public static function deliverResource($data = NULL, $contentType = '', $doExit = TRUE) {
        if(!isset($data)) {
            return;
        }
        $contentType = isset($contentType) ? (is_string($contentType) ? trim($contentType) : '') : '';
        if($contentType != self::CONTENT_TYPE_HTML &&
           $contentType != self::CONTENT_TYPE_JAVASCRIPT &&
           $contentType != self::CONTENT_TYPE_CSS &&
           $contentType != self::CONTENT_TYPE_IMAGE_GIF &&
           $contentType != self::CONTENT_TYPE_IMAGE_JPG &&
           $contentType != self::CONTENT_TYPE_IMAGE_PNG) {
            return;
        }
        $doExit = isset($doExit) ? (is_bool($doExit) ? $doExit : TRUE) : TRUE;
        
        // Clean output buffers before
        if (ob_get_level()) {
            ob_end_clean();
        }  
        header('content-type: ' . $contentType);
        print($data);
        if($doExit == TRUE) {
            exit;
        }
    } // End deliverResource
    
    /**
     * Sets a template folder.
     * The folder will only be set, if it is an existing folder.
     * 
     * @param string $folder 
     *    The absolute path of a folder
     */
    public static function setTemplateFolder($folder = '') {
        $folder = isset($folder) ? (is_string($folder) ? trim($folder) : '') : '';
        if($folder != '') {
            if(is_dir($folder)) {
                self::$templateFolder = $folder;
            }
        }
    } // End setTemplateFolder

    /**
     * Returns the template folder.
     * 
     * @return string
     *    The absolute path of the template folder.
     *    By default this value is an empty string 
     */
    public static function getTemplateFolder() {
        return self::$templateFolder;
    } // End getTemplateFolder
    
    /**
     * Renders a template.
     * Templates must have the extension '.tpl.php'.
     * The folder of templates has to be set via setTemplateFolder()
     * 
     * @param string $templateName
     *    The name of a template without the extension '.tpl.php'
     * 
     * @param array $variablesArray
     *    A structured array with key/value pairs.
     *    These are variables that are local to the template where
     *    the keys are the local variable names.
     * 
     * @return string
     *    The rendered template or an empty string if the template folder
     *    is not existing or the template is not existing. 
     */
    public static function renderTemplate($templateName = '', $variablesArray = null) {
        $templateFolder = self::getTemplateFolder();
        if($templateFolder == '') {
            return '';
        }
        else if(!is_dir($templateFolder)) {
            return '';
        }
        
        $result = '';
        $templateName = isset($templateName) ? trim($templateName) : '';
        if($templateName != '') {
            $templatePath = $templateFolder . '/' . $templateName;
            if(strpos($templatePath, '.tpl.php') === FALSE) {
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
    
    /**
     * Encodes an array to a base64 string by serializing the array and
     * then calling base64_encode().
     * 
     * @param array $array
     *    The input array
     * 
     * @return string
     *    The base64 string 
     */
    public static function arrayToB64($array = array()) {
        $array = isset($array) ? (is_array($array) ? $array : array()) : array();
        $serStr = serialize($array);
        $b64 = @base64_encode($serStr);
        return $b64;
    } // End arrayToB64
    
    /**
     * Decodes a base64 string encoded with arrayToB64() back to an array.
     * 
     * @param string $b64
     *    The base64 input string
     * 
     * @return array 
     *    An array. On error an empty array is returned.
     */
    public static function b64ToArray($b64 = '') {
        $result = array();
        $b64 = isset($b64) ? (is_string($b64) ? $b64 : '') : '';
        if(trim($b64) == '') {
            return $result;
        }
        
        $serStr = @base64_decode($b64);
        if(isset($serStr) && $serStr !== FALSE) {
            $array = @unserialize($serStr);
            if(isset($array) && $array !== FALSE) {
                if(is_array($array)) {
                    $result = $array;
                }
            }
        }
        
        return $result;
    } // End b64ToArray
    
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
     * Redirects to a given url.
     * 
     * @param string $url
     *    The url with or without query parameters
     * 
     * @param array $options
     *    (optional) An associative array of additional URL options with the following
     *   elements:
     *   - 'query': An array of query key/value-pairs (without any URL-encoding) to
     *     append to the URL.
     *    
     * @param type $http_response_code
     *   (optional) The HTTP status code to use for the redirection, defaults to
     *   302. The valid values for 3xx redirection status codes are defined in
     *   @link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3 RFC 2616 @endlink
     *   and the
     *   @link http://tools.ietf.org/html/draft-reschke-http-status-308-07 draft for the new HTTP status codes: @endlink
     *   - 301: Moved Permanently (the recommended value for most redirects).
     *   - 302: Found (default in PHP, sometimes used for spamming search engines).
     *   - 303: See Other.
     *   - 304: Not Modified.
     *   - 305: Use Proxy.
     *   - 307: Temporary Redirect.
     * 
     */
    public static function redirectTo($url = '', $options = array(), $http_response_code = 302) {
        $url = isset($url) ? (is_string($url) ? trim($url) : '') : '';
        $options = isset($options) ? (is_array($options) ? $options : array()) : array();
        $http_response_code = isset($http_response_code) ? (is_numeric($http_response_code) ? intval($http_response_code) : 302) : 302;
        
        if(isset($options['query']) && is_array($options['query'])) {
            $queryString = trim(self::createQueryParameters($options['query'], TRUE));
            if($queryString != '') {
                if(strpos($url, '?') !== FALSE) {
                    $url = $url . '&' . $queryString;
                }
                else {
                    $url = $url . '?' . $queryString;
                }
            }
        }
        
        header('Location: ' . $url, TRUE, $http_response_code);
        exit;
    } // End redirectTo
    
} // End class Raautil_Utils

