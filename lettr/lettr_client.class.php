<?php
/*	--------------------------------------------------------------
	lettr_client.class.php
	Digineo GmbH
	http://www.digineo.de
	Copyright (c) 2010 Digineo GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------*/

  /**
   * @package Lettr
   * @subpackage REST_Client
   * @access private
   * @author Digineo GmbH
   * @copyright Digineo GmbH, 2010
   * @link http://www.digineo.de
   * @link mailto:kontakt@digineo.de
   */
  class Lettr_Client {
    /**
     * Setzt die Zugangsdaten zur Lettr-API.
     *  
     * @var array assoziativ, enthält 'api-key'
     */
    private static $credentials = array();
    
    /**
     * Überprüft, ob die Zugangsdaten zur API bereits gesetzt wurden,
     * und schmeißt ggf. eine Lettr_Exception.
     */
    private static function check_credentials(){
      if(!is_array(self::$credentials)){
        throw new Lettr_Exception("Credentials sind nicht definiert.");
      }
    }
    
    /**
     * Setzt die Zugangsdaten zur API.
     * 
     * Schmeißt Lettr_IllegalArgumentException, wenn sie unvollständig gesetzt werde
     * 
     * @param string/array $credentials Enthält entweder einen API-Key als String oder ein assoziatives Array bestehend aus 'username' und 'password' mit entsprechenden Werten.
     */
    public static function set_credentials($api_key_or_username_and_password){
      $credentials = array();
      $credentials["site"] = "https://lettr.de/";
      
      if(!$api_key_or_username_and_password)
      {
        throw new Lettr_IllegalArgumentException("API-Key ist leer oder nicht definiert.");
      } elseif (is_string($api_key_or_username_and_password)){
        $credentials["api_key"] = $api_key_or_username_and_password;
      }
      elseif (is_array($api_key_or_username_and_password)) {
        Lettr_Validation::presence_of('api_key_or_username_and_password', $api_key_or_username_and_password, array("username", "password"));
        $credentials = array_merge($credentials, $api_key_or_username_and_password);
      } else {
      }
      self::$credentials = $credentials;
    }
    
    /**
     * Holt per GET Daten einer Resource ab.
     * 
     * @param $url string Pfad der Resource
     */
    public function get($url){
      return $this->send($url, 'GET');
    }
    
    /**
     * Erstellt per POST eine neue Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array Daten
     */
    public function post($url, $data){
      return $this->send($url, 'POST', $data);
    }
    
    /**
     * Aktualisiert per PUT eine vorhandene Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array Daten
     */
    public function put($url, $data){
      return $this->send($url, 'PUT', $data);
    }
    
    /**
     * Löscht per DELETE eine vorhandene Resource.
     * 
     * @param $url string Pfad der Resource
     * @param $data array (optional) Daten, die zum Löschen der Resource verwendet werden sollen.
     */
    public function delete($url, $data = null){
      return $this->send($url, 'DELETE', $data);
    }
    
    /**
     * Setzt einen REST-Request ab.
     * 
     * @param $url string Pfad der Resource
     * @param $method string REST-Methode
     * @param $data array (optional) zu übergebende Daten
     */
    protected function send($url, $method, $data = null){
      self::check_credentials();
      $this->errors = null;
      
      $header = array("Accept: application/json");
      
      if(!empty(self::$credentials["api_key"])) {
        $header[] = "X-Lettr-API-key: " . self::$credentials["api_key"];
      }
      
      $ch  = curl_init();
      curl_setopt($ch, CURLOPT_URL,            self::$credentials["site"] . $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_TIMEOUT,        15);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $method);
      curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
      curl_setopt($ch, CURLOPT_POSTFIELDS,     self::serialized_params($data));
      
      // Auf Benutzernamen und Kennwort prüfen!
      if(!empty(self::$credentials["username"])){
        curl_setopt($ch, CURLOPT_USERPWD, self::$credentials["username"] . ":" . self::$credentials["password"]);
      }
      
      /*
        Wenn hier der fehler 'error setting certificate verify locations' auftritt, 
        dann fehlt das ca-certificates-Paket. Das muss nachinstalliert werden mit:
         
        apt-get install ca-certificates
      */
      $data = curl_exec($ch);
      
      if (curl_errno($ch)) {
        // cURL-Fehler
        throw new Lettr_CurlException(curl_error($ch),curl_errno($ch));
      } else {
        $info = curl_getinfo($ch);
        curl_close($ch);

        // Behandeln der HTTP-Statuscodes
        switch($info['http_code']) {
          case 200:   // OK - Anfrage erfolgreich
          case 201:   // Created - Anfrage erfolgreich
          case 202:   // Accepted - Anfrage erfolgreich
            return true;
            break;
          case 400:   // Bad Request - Fehler in der Übermittlung
            throw new Lettr_ClientErrorException('400 Bad Request - Daten wurden nicht erfolgreich übermittelt', 400);
            break;
          case 401:   // Unauthorized - Fehlerhafte Zugangsdaten
            throw new Lettr_ClientErrorException('401 Unauthorized - Bitte Benutzerdaten für Lettr-Service überprüfen!', 401);
            break;
          case 402:   // Payment Required - Kreditlimit überschritten
            throw new Lettr_ClientErrorException('402 Payment Required - Bitte Credits des Lettr-Service überprüfen!', 402);
            break;
          case 403:   // Forbidden - Der Zugang wurde gesperrt
            throw new Lettr_ClientErrorException('403 Forbidden - Dienst temporär nicht verfügbar oder Zugang gesperrt', 403);
            break;
          case 404:   // Not Found - URL nicht mehr aktuell
            throw new Lettr_ClientErrorException('404 Not Found - URL nicht gefunden - Lettr-API auf dem neusten Stand?', 404);
            break;
          case 407:   // Proxy Authentication Required - Falls Proxy verwendet wird (Aktuell nicht relevant)
            throw new Lettr_ClientErrorException('407 Proxy Authentication Required - Bitte Benutzerdaten für Proxy-Server prüfen', 407);
            break;
          case 408:   // Request Timeout - Daten wurden zu langsam übermittelt (erneuter Versuch?)
            throw new Lettr_ClientErrorException('408 Request Timeout - Anfrage zu einem späteren Zeitpunkt neu stellen', 408);
            break;
          case 413:   // Request Entity Too Large - Anfrage zu groß (E-Mail zu groß?)
            throw new Lettr_ClientErrorException('413 Request Entity Too Large - Versendete E-Mail größer als maximum?', 413);
            break;
          case 418:   // I'm a Teapot - Soll ja vorkommen ;-)
            throw new Lettr_ClientErrorException('418 I\'m a Teapot - Sorry, wir liefern nur an Kaffeekannen ;-)', 418);
            break;
          case 422:   // Unprocessable Entity
            throw new Lettr_UnprocessableEntityException("422 Unprocessable Entity - Datenformat fehlerhaft (".print_r($data, true).")", 422);
            break;
          case 500:   // Internal Server Error - Anfrage später erneut absenden
            throw new Lettr_ServerErrorException('500 Internal Server Error - Der Lettr-Service steht gerade nicht zur Verfügung', 500);
            break;
          case 502:   // Bad Gateway - Anfrage später erneut senden 
            throw new Lettr_ServerErrorException('502 Bad Gateway - Der Lettr-Service steht gerade nicht zur Verfügung', 502);
            break;
          case 503:   // Service Unavailable - Retry-After abfragen und später erneut versuchen
            throw new Lettr_ServerErrorException('503 Service Unavailable - Der Lettr-Service steht gerade nicht zur Verfügung', 503);
            break;
        }
      }
    }
    
    public static function serialized_params($params, $prefix="", $return_as_hash=true) {
      $results = array();
      
      foreach($params as $key=>$value) {
        if(is_array($value)){
          $sub_results = self::serialized_params($value, empty($prefix) ? $key : $prefix.'['.$key.']', false);
          $results = array_merge($results, $sub_results);
        } else {
          array_push($results, array(empty($prefix) ? $key : $prefix.'['.$key.']', $value));
        }
      }
      
      if($return_as_hash) {
        $final_results = array();
        
        foreach($results as $result) {
          $final_results[$result[0]] = $result[1];
        }
        return($final_results);
      }
      
      return($results);
    }
  }