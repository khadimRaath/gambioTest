<?php
/* --------------------------------------------------------------
  TerminalData.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_TerminalData {
    
    const SIMULATION_MODE_SIMULATION            = 'true';
    const SIMULATION_MODE_NO_SIMULATION         = 'false';
    const APPLICATION_ID_OPM                    = 'OPM';
    const APPLICATION_ID_ICG                    = 'ICG';
    const MATCH_SERVICE_RESOLVER_FIX_URI        = 'Raa_MatchServiceResolverFixedUri';
    const MATCH_SERVICE_RESOLVER_ALG_T          = 'Raa_MatchServiceResolverAlgT';
    
    private $terminalId = '';    
    
    private $matchServiceResolver = '';
    private $secure = 'false';
    private $URI = '';
    private $operationPath = '';

    private $key = '';
    private $providerId = '';
    private $countryId = '';
    private $sellerId = '';
    private $salesPointId = '';
    private $applicationId = '';
    private $description = '';
    private $sellerName = '';

    private $simulationMode = FALSE;
    private $simulationACCOUNTID = '';
    private $simulationFIRSTNAME = '';
    private $simulationLASTNAME = '';
    private $simulationADDRESS1 = '';
    private $simulationADDRESS2 = '';
    private $simulationADDRESS3 = '';
    private $simulationZIPCODE = '';
    private $simulationCITY = '';
    private $simulationCOUNTRY = '';
    private $simulationMSISDN = '';
    private $simulationMALE = -1;
    private $simulationDATEOFBIRTH = '';
    private $simulationBANKACCOUNTNUMBER = '';
    private $simulationBANKCODE = '';

    /**
     * Constructs a new Raautil_TerminalData instance.
     * 
     * @param array $inputArray 
     *    The array with input data
     *    The array must have the following structure
     * 
     *    array(
     *      'terminalId'                  => (Optional) [String] An optional terminal ID,
     *      'description'                 => (Mandatory) [String] The description,
     *      'URI'                         => (Mandatory) [String] The match server URI,
     *      'operationPath'               => (Mandatory) [String] The match server operation path,
     *      'sellerId'                    => (Mandatory) [String] The seller ID,
     *      'key'                         => (Mandatory) [String] The seller key,
     *      'providerId'                  => (Mandatory) [String] The provider ID,
     *      'salesPointId'                => (Mandatory) [String] The sales point ID,
     *      'applicationId'               => (Mandatory) [String] The application ID,
     *      'countryId'                   => (Mandatory) [String] The 2 character country ID (e.g. 'de'),
     *      'matchServiceResolver'        => (Mandatory) [String] The match service resolver (One of the constants MATCH_SERVICE_RESOLVER_FIX_URI or MATCH_SERVICE_RESOLVER_ALG_T),
     *      'secure'                      => (Mandatory) [String] The secure flag controlling if SSL should be used or not('true'|'false'),
     *      'simulationMODE'              => (Optional) [String] An optional simulation mode ('true' or 'false'. Empty is 'false'),
     *      'simulationACCOUNTID'         => (Optional, if no simulationmode) [String] An optional simulation account ID,
     *      'simulationFIRSTNAME'         => (Optional, if no simulationmode) [String] An optional simulation firstname,
     *      'simulationLASTNAME'          => (Optional, if no simulationmode) [String] An optional simulation lastname,
     *      'simulationADDRESS1'          => (Optional, if no simulationmode) [String] An optional simulation address line 1,
     *      'simulationADDRESS2'          => (Optional, if no simulationmode) [String] An optional simulation address line 2,
     *      'simulationADDRESS3'          => (Optional, if no simulationmode) [String] An optional simulation address line 3,
     *      'simulationZIPCODE'           => (Optional, if no simulationmode) [String] An optional simulation zipcode,
     *      'simulationCITY'              => (Optional, if no simulationmode) [String] An optional simulation city,
     *      'simulationCOUNTRY'           => (Optional, if no simulationmode) [String] An optional simulation country (2 character uppercase country code. e.g. 'DE'),
     *      'simulationMSISDN'            => (Optional, if no simulationmode) [String] An optional simulation telephone number of the simulated smartphone,
     *      'simulationDATEOFBIRTH'       => (Optional, if no simulationmode) [String] An optional simulation date of birth (Format 'DD.MM.YYYY'),
     *      'simulationMALE'              => (Optional, if no simulationmode) [Integer] 0=Female, 1=Male,
     *      'simulationBANKACCOUNTNUMBER' => (Optional, if no simulationmode) [String] The bank account number,
     *      'simulationBANKCODE'          => (Optional, if no simulationmode) [String] The bank code,
     *  )
     * 
     * 
     * @throws Raautil_TransactionException
     *    On error
     */
    public function __construct($inputArray = array()) {
        $inputArray = isset($inputArray) ? (is_array($inputArray) ? $inputArray : array()) : array();
        
        // This may throw an Raautil_TransactionException
        $this->parseData($inputArray);
    } // End constructor
    
    /**
     * Returns TRUE if simulation is active, FALSE otherwise
     * 
     * @return boolean
     *     TRUE if if simulation is active, FALSE otherwise
     */
    public function isSimulation() {
        return $this->simulationMode;
    } // End isSimulation
    
    /**
     * Sets the simulation mode.
     * 
     * @param boolean $value
     *    TRUE or FALSE
     * 
     * @return Raautil_TerminalData 
     */
    public function setSimulation($value = FALSE) {
        $this->simulationMode = isset($value) ? ($value == TRUE ? TRUE : FALSE) : FALSE;
        return $this;
    } // End setSimulation
    
    /**
     * Returns either a valid email address or an empty string.
     * 
     * @return string 
     *     Either a valid email address or an empty string
     */
    public function getSimulationACCOUNTID() {
        return $this->simulationACCOUNTID;
    } // End getSimulationACCOUNTID
    
    public function getSimulationFIRSTNAME() {
        return $this->simulationFIRSTNAME;
    } // End getSimulationFIRSTNAME

    public function getSimulationLASTNAME() {
        return $this->simulationLASTNAME;
    } // End getSimulationLASTNAME

    public function getSimulationADDRESS1() {
        return $this->simulationADDRESS1;
    } // End getSimulationADDRESS1

    public function getSimulationADDRESS2() {
        return $this->simulationADDRESS2;
    } // End getSimulationADDRESS2

    public function getSimulationADDRESS3() {
        return $this->simulationADDRESS3;
    } // End getSimulationADDRESS3

    public function getSimulationZIPCODE() {
        return $this->simulationZIPCODE;
    } // End getSimulationZIPCODE

    public function getSimulationCITY() {
        return $this->simulationCITY;
    } // End getSimulationCITY

    public function getSimulationCOUNTRY() {
        return $this->simulationCOUNTRY;
    } // End getSimulationCOUNTRY

    public function getSimulationMSISDN() {
        return $this->simulationMSISDN;
    } // End getSimulationMSISDN

    public function getSimulationMALE() {
        return $this->simulationMALE;
    } // End getSimulationMALE

    public function getSimulationDATEOFBIRTH() {
        return $this->simulationDATEOFBIRTH;
    } // End getSimulationDATEOFBIRTH

    public function getSimulationBANKACCOUNTNUMBER() {
        return $this->simulationBANKACCOUNTNUMBER;
    } // End getSimulationBANKACCOUNTNUMBER
    
    public function getSimulationBANKCODE() {
        return $this->simulationBANKCODE;
    } // End getSimulationBANKCODE
    
    
    public function getTerminalID() {
        return $this->terminalId;
    } // End getTerminalID
        
    public function getMatchServiceResolver() {
        return $this->matchServiceResolver;
    } // End getMatchServiceResolver

    public function getSecure() {
        return $this->secure;
    } // End getSecure

    public function getURI() {
        return $this->URI;
    } // End getURI

    public function getOperationPath() {
        return $this->operationPath;
    } // End getOperationPath

    public function getKey() {
        return $this->key;
    } // End getKey

    public function getSellerKey() {
        return $this->key;
    } // End getSellerKey
    
    public function getProviderID() {
        return $this->providerId;
    } // End getProviderID

    public function getCountryID() {
        return $this->countryId;
    } // End getCountryID

    public function getSellerID() {
        return $this->sellerId;
    } // End getSellerID

    public function getSalesPointID() {
        return $this->salesPointId;
    } // End getSalesPointID

    public function getApplicationID() {
        return $this->applicationId;
    } // End getApplicationID

    public function getDescription() {
        return $this->description;
    } // End getDescription

    public function getSellerName() {
        return $this->sellerName;
    } // End getSellerName

    
    /**
     * Returns an array with terminal data.
     * 
     * @return array
     * 
     *    array(
     *      'terminalId'                  => [String] An optional terminal ID,
     *      'description'                 => [String] The description,
     *      'URI'                         => [String] The match server URI,
     *      'operationPath'               => [String] The match server operation path,
     *      'sellerId'                    => [String] The seller ID,
     *      'key'                         => [String] The seller key,
     *      'providerId'                  => [String] The provider ID,
     *      'salesPointId'                => [String] The sales point ID,
     *      'applicationId'               => [String] The application ID,
     *      'countryId'                   => [String] The 2 character country ID (e.g. 'de'),
     *      'matchServiceResolver'        => [String] The match service resolver (One of the constants MATCH_SERVICE_RESOLVER_FIX_URI or MATCH_SERVICE_RESOLVER_ALG_T),
     *      'secure'                      => [String] The secure flag controlling if SSL should be used or not('true'|'false'),
     *      'simulationMODE'              => [String] An optional simulation mode ('true' or 'false'. Empty is 'false'),
     *      'simulationACCOUNTID'         => [String] An optional simulation account ID,
     *      'simulationFIRSTNAME'         => [String] An optional simulation firstname,
     *      'simulationLASTNAME'          => [String] An optional simulation lastname,
     *      'simulationADDRESS1'          => [String] An optional simulation address line 1,
     *      'simulationADDRESS2'          => [String] An optional simulation address line 2,
     *      'simulationADDRESS3'          => [String] An optional simulation address line 3,
     *      'simulationZIPCODE'           => [String] An optional simulation zipcode,
     *      'simulationCITY'              => [String] An optional simulation city,
     *      'simulationCOUNTRY'           => [String] An optional simulation country (2 character uppercase country code. e.g. 'DE'),
     *      'simulationMSISDN'            => [String] An optional simulation telephone number of the simulated smartphone,
     *      'simulationDATEOFBIRTH'       => [String] An optional simulation date of birth (Format 'DD.MM.YYYY'),
     *      'simulationMALE'              => [Integer] 0=Female, 1=Male,
     *      'simulationBANKACCOUNTNUMBER' => (Optional, if no simulationmode) [String] The bank account number,
     *      'simulationBANKCODE'          => (Optional, if no simulationmode) [String] The bank code,
     *  )
     * 
     */
    public function toArray() {
        $result = array(
            'terminalId'                    => $this->terminalId,
            'description'                   => $this->description,
            'URI'                           => $this->URI,
            'operationPath'                 => $this->operationPath,
            'sellerId'                      => $this->sellerId,
            'key'                           => $this->key,
            'providerId'                    => $this->providerId,
            'salesPointId'                  => $this->salesPointId,
            'applicationId'                 => $this->applicationId,
            'countryId'                     => $this->countryId,
            'matchServiceResolver'          => $this->matchServiceResolver,
            'secure'                        => $this->secure, //(string) secure ('true'|'false'),
            'simulationMODE'                => $this->simulationMode,
            'simulationACCOUNTID'           => $this->simulationACCOUNTID,
            'simulationFIRSTNAME'           => $this->simulationFIRSTNAME,
            'simulationLASTNAME'            => $this->simulationLASTNAME,
            'simulationADDRESS1'            => $this->simulationADDRESS1,
            'simulationADDRESS2'            => $this->simulationADDRESS2,
            'simulationADDRESS3'            => $this->simulationADDRESS3,
            'simulationZIPCODE'             => $this->simulationZIPCODE,
            'simulationCITY'                => $this->simulationCITY,
            'simulationCOUNTRY'             => $this->simulationCOUNTRY,
            'simulationMSISDN'              => $this->simulationMSISDN,
            'simulationDATEOFBIRTH'         => $this->simulationDATEOFBIRTH,
            'simulationMALE'                => $this->simulationMALE,
            'simulationBANKACCOUNTNUMBER'   => $this->simulationBANKACCOUNTNUMBER,
            'simulationBANKCODE'            => $this->simulationBANKCODE,
        );
        return $result;
    } // End toArray

    
    /**
     * Returns the connection properties as array.
     * 
     * @return array
     *    The connection properties 
     */
    public function getConnectionProperties() {
        $result = array(
            'matchServiceResolver'          => $this->matchServiceResolver,
            'secure'                        => $this->secure, //(string) secure ('true'|'false'),
            'URI'                           => $this->URI,
            'operationPath'                 => $this->operationPath,
        );
        return $result;
    } // End getConnectionProperties

    
    /**
     * Returns the terminal properties as array.
     * 
     * @return array
     *     The terminal properties
     */
    public function getTerminalProperties() {
        $result = array(
            'key'                           => $this->key,
            'providerId'                    => $this->providerId,
            'countryId'                     => $this->countryId,
            'sellerId'                      => $this->sellerId,
            'salesPointId'                  => $this->salesPointId,
            'applicationId'                 => $this->applicationId,
            'description'                   => $this->description,
        );
        
        if(trim($this->getSellerName()) != '') {
            $result['sellerName'] = $this->getSellerName();
        }
        
        return $result;
    } // End getTerminalProperties

    
    /**
     * Parses the ini file.
     * 
     * throws Raautil_TransactionException if values are invalid
     */
    private function parseData($props = array()) {
        $terminalid = isset($props['terminalId']) ? trim($props['terminalId']) : '';
        if($terminalid != '') {
            $this->terminalId = $terminalid;
        }
        $this->matchServiceResolver = isset($props['matchServiceResolver']) ? trim($props['matchServiceResolver']) : 'Raa_MatchServiceResolverFixedUri';
        $str = isset($props['secure']) ? trim($props['secure']) : 'false';
        if(stristr($str, 'true') === FALSE && stristr($str, '1') === FALSE) {
            $this->secure = 'false';
        }
        else {
            $this->secure = 'true';
        }
        $this->URI = isset($props['URI']) ? trim($props['URI']) : '';
        $this->operationPath = isset($props['operationPath']) ? trim($props['operationPath']) : '';
        $this->key = isset($props['key']) ? trim($props['key']) : '';
        $this->providerId = isset($props['providerId']) ? trim($props['providerId']) : '';
        $this->countryId = isset($props['countryId']) ? trim($props['countryId']) : '';
        $this->sellerId = isset($props['sellerId']) ? trim($props['sellerId']) : '';
        $this->salesPointId = isset($props['salesPointId']) ? trim($props['salesPointId']) : '';
        $this->applicationId = isset($props['applicationId']) ? trim($props['applicationId']) : '';
        $this->description = isset($props['description']) ? trim($props['description']) : '';
        $this->sellerName = isset($props['sellerName']) ? trim($props['sellerName']) : '';

        // The property 'simulationMODE' either be
        // - undefined: No concrete simulation mode is defined
        // - simulationMODE=simulation: Force simulation
        // - simulationMODE=nosimulation: Force no simulation
        $str = isset($props['simulationMODE']) ? trim($props['simulationMODE']) : 'false';
        if(stristr($str, 'true') === FALSE && stristr($str, '1') === FALSE) {
            $this->simulationMode = FALSE;
        }
        else {
            $this->simulationMode = TRUE;
        }

        
        // The property 'simulationACCOUNTID'
        $val = isset($props['simulationACCOUNTID']) ? trim(strval($props['simulationACCOUNTID'])) : '';
        if($val != '') {
            // Check if it is a valid email address
            $emailValid = (bool)filter_var($val, FILTER_VALIDATE_EMAIL);
            if($emailValid == TRUE) {
                $this->simulationACCOUNTID = $val;
            }
        }

        // The property 'simulationFIRSTNAME'
        $val = isset($props['simulationFIRSTNAME']) ? trim(strval($props['simulationFIRSTNAME'])) : '';
        if($val != '') {
            $this->simulationFIRSTNAME = $val;
        }

        // The property 'simulationLASTNAME'
        $val = isset($props['simulationLASTNAME']) ? trim(strval($props['simulationLASTNAME'])) : '';
        if($val != '') {
            $this->simulationLASTNAME = $val;
        }

        // The property 'simulationADDRESS1'
        $val = isset($props['simulationADDRESS1']) ? trim(strval($props['simulationADDRESS1'])) : '';
        if($val != '') {
            $cval = strtolower($val);
            if($cval == 'herr' || $cval == 'herrn' || $cval == 'frau') {
                $this->simulationADDRESS1 = $val;
            }
        }
        
        // The property 'simulationADDRESS2'
        $val = isset($props['simulationADDRESS2']) ? trim(strval($props['simulationADDRESS2'])) : '';
        if($val != '') {
            $this->simulationADDRESS2 = $val;
        }

        // The property 'simulationADDRESS3'
        $val = isset($props['simulationADDRESS3']) ? trim(strval($props['simulationADDRESS3'])) : '';
        if($val != '') {
            $this->simulationADDRESS3 = $val;
        }

        // The property 'simulationZIPCODE'
        $val = isset($props['simulationZIPCODE']) ? trim(strval($props['simulationZIPCODE'])) : '';
        if($val != '') {
            $this->simulationZIPCODE = $val;
        }

        // The property 'simulationCITY'
        $val = isset($props['simulationCITY']) ? trim(strval($props['simulationCITY'])) : '';
        if($val != '') {
            $this->simulationCITY = $val;
        }
        
        // The property 'simulationCOUNTRY'
        $val = isset($props['simulationCOUNTRY']) ? trim(strval($props['simulationCOUNTRY'])) : '';
        if($val != '') {
            if(strlen($val) == 2) {
                $this->simulationCOUNTRY = strtoupper($val);
            }
        }

        // The property 'simulationMSISDN'
        $val = isset($props['simulationMSISDN']) ? trim(strval($props['simulationMSISDN'])) : '';
        if($val != '') {
            $this->simulationMSISDN = $val;
        }
        
        // The property 'simulationMALE'
        $val = isset($props['simulationMALE']) ? trim(strval($props['simulationMALE'])) : '';
        if($val != '') {
            if(is_numeric($val)) {
                $val = intval($val);
                if($val == 0 || $val == 1) {
                    $this->simulationMALE = $val;
                }
            }
        }

        
        // The property 'simulationDATEOFBIRTH'
        $val = isset($props['simulationDATEOFBIRTH']) ? trim(strval($props['simulationDATEOFBIRTH'])) : '';
        if($val != '') {
            $time = @strtotime($val);
            if($time !== FALSE) {
                $dateFormatted = @date('d.m.Y', $time);
                $this->simulationDATEOFBIRTH = $dateFormatted;
            }
        }

        // The property 'simulationBANKACCOUNTNUMBER'
        $val = isset($props['simulationBANKACCOUNTNUMBER']) ? trim(strval($props['simulationBANKACCOUNTNUMBER'])) : '';
        if($val != '') {
            $this->simulationBANKACCOUNTNUMBER = $val;
        }
        
        // The property 'simulationBANKCODE'
        $val = isset($props['simulationBANKCODE']) ? trim(strval($props['simulationBANKCODE'])) : '';
        if($val != '') {
            $this->simulationBANKCODE = $val;
        }
        
        
        if($this->matchServiceResolver != self::MATCH_SERVICE_RESOLVER_FIX_URI &&
           $this->matchServiceResolver != self::MATCH_SERVICE_RESOLVER_ALG_T) {
            throw new Raautil_TransactionException("Invalid matchServiceResolver");
        }
        
        if($this->secure != 'true' &&
           $this->secure != 'false') {
            throw new Raautil_TransactionException("Invalid secure");
        }
        
        if($this->URI == '') {
            throw new Raautil_TransactionException("Invalid URI");
        }

        if($this->operationPath == '') {
            throw new Raautil_TransactionException("Invalid operationPath");
        }

        if($this->key == '') {
            throw new Raautil_TransactionException("Invalid key");
        }
        
        if($this->providerId == '') {
            throw new Raautil_TransactionException("Invalid providerId");
        }

        if($this->countryId == '') {
            throw new Raautil_TransactionException("Invalid countryId");
        }

        if($this->sellerId == '') {
            throw new Raautil_TransactionException("Invalid sellerId");
        }

        if($this->salesPointId == '') {
            throw new Raautil_TransactionException("Invalid salesPointId");
        }

        if($this->applicationId == '') {
            throw new Raautil_TransactionException("Invalid applicationId");
        }

        if($this->description == '') {
            throw new Raautil_TransactionException("Invalid description");
        }
        
    } // End parseData
    
    
    /**
     * Returns a Raautil_TerminalData object usable for amount transactions 
     * created from an ini file named 'terminalopm.ini' that is located in this 
     * directory where this class is located.
     * 
     * @return Raautil_TerminalData
     *    The Raautil_TerminalData object
     * 
     * @throws Raautil_TransactionException
     *    If the file does not exist or it exists but contains invalid values
     */
    public static function createDefaultOPM() {
        $folder = dirname(__FILE__);
        $fname = 'terminalopm';
        $Raautil_TerminalDataProviderInifile = new Raautil_TerminalDataProviderInifile($folder, $fname);
        return $Raautil_TerminalDataProviderInifile->getTerminalData();
    } // End createDefaultOPM
    
    /**
     * Returns a Raautil_TerminalData object usable for empty (login) transactions 
     * created from an ini file named 'terminalicg.ini' that is located in this 
     * directory where this class is located.
     * 
     * @return Raautil_TerminalData
     *    The Raautil_TerminalData object
     * 
     * @throws Raautil_TransactionException
     *    If the file does not exist or it exists but contains invalid values
     */
    public static function createDefaultICG() {
        $folder = dirname(__FILE__);
        $fname = 'terminalicg';
        $Raautil_TerminalDataProviderInifile = new Raautil_TerminalDataProviderInifile($folder, $fname);
        return $Raautil_TerminalDataProviderInifile->getTerminalData();
    } // End createDefaultICG
    
    public static function createDefaultTerminalData($applicationId = 'OPM',
                                                     $simulation = FALSE,
                                                     $simulationACCOUNTID = 'simulation.rsmartsepa@rubean.com',
                                                     $simulationFIRSTNAME = 'Susi',
                                                     $simulationLASTNAME = 'Simulation',
                                                     $simulationADDRESS1 = 'Frau',
                                                     $simulationADDRESS2 = 'Susi Simulation',
                                                     $simulationADDRESS3 = 'Teststrasse 3',
                                                     $simulationZIPCODE = '81379',
                                                     $simulationCITY = 'Muenchen',
                                                     $simulationCOUNTRY = 'DE',
                                                     $simulationMSISDN = '017112345678',
                                                     $simulationDATEOFBIRTH = '15.05.1985',
                                                     $simulationMALE = 0,
                                                     $simulationBANKACCOUNTNUMBER = '12345678',
                                                     $simulationBANKCODE = '70150000') {
        
        
        $tdArray = array(
            'terminalId'                    => '',
            'description'                   => 'TestTerminal',
            'URI'                           => 'https://p2.raasys.de',
            'operationPath'                 => '/raamatch/service/match4terminal',
            'sellerId'                      => 'ABCDEFGHIJKLMNO',
            'key'                           => 'ABcdeFnGvCrLqN+mvR4i3W7x2KI2u4FTiTlmMu9Rs',
            'providerId'                    => 'T002p2.raasys.de',
            'salesPointId'                  => 'TestTerminal',
            'applicationId'                 => $applicationId,
            'countryId'                     => 'de',
            'matchServiceResolver'          => 'Raa_MatchServiceResolverFixedUri',
            'secure'                        => 'false',
            'simulationMODE'                => ($simulation == TRUE ? Raautil_TerminalData::SIMULATION_MODE_SIMULATION : Raautil_TerminalData::SIMULATION_MODE_NO_SIMULATION),
            'simulationACCOUNTID'           => $simulationACCOUNTID,
            'simulationFIRSTNAME'           => $simulationFIRSTNAME,
            'simulationLASTNAME'            => $simulationLASTNAME,
            'simulationADDRESS1'            => $simulationADDRESS1,
            'simulationADDRESS2'            => $simulationADDRESS2,
            'simulationADDRESS3'            => $simulationADDRESS3,
            'simulationZIPCODE'             => $simulationZIPCODE,
            'simulationCITY'                => $simulationCITY,
            'simulationCOUNTRY'             => $simulationCOUNTRY,
            'simulationMSISDN'              => $simulationMSISDN,
            'simulationDATEOFBIRTH'         => $simulationDATEOFBIRTH,
            'simulationMALE'                => $simulationMALE,
            'simulationBANKACCOUNTNUMBER'   => $simulationBANKACCOUNTNUMBER,
            'simulationBANKCODE'            => $simulationBANKCODE,
        );
        
        try {
            $Raautil_TerminalData = new Raautil_TerminalData($tdArray);
            return $Raautil_TerminalData;
        } catch(Raautil_TransactionException $ex) {
            return NULL;
        }
    } // End createDefaultTerminalData
    
} // End class Raautil_TerminalData

