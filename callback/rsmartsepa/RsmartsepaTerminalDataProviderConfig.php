<?php
/* --------------------------------------------------------------
  RsmartsepaTerminalDataProviderConfig.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class RsmartsepaTerminalDataProviderConfig implements Raautil_ITerminalDataProvider {
    
    protected $valueArray = array(
        'matchServiceResolver'      => 'Raa_MatchServiceResolverFixedUri',
        'secure'                    => 'false',
        'URI'                       => '', // Configurable
        'operationPath'             => '/raamatch/service/match4terminal',
        'key'                       => '', // Configurable
        'providerId'                => '', // Configurable
        'countryId'                 => '', // Configurable
        'sellerId'                  => '', // Configurable
        'salesPointId'              => '', // Configurable
        'applicationId'             => 'OPM',
        'description'               => '', // Configurable
        'sellerName'                => '',
    );
    
    public function __construct() {
        $this->valueArray['URI']            = trim(RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_URI', ''));
        $this->valueArray['sellerId']       = trim(RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SELLERID', ''));
        $this->valueArray['key']            = trim(RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_KEY', ''));
        $this->valueArray['providerId']     = trim(RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_PROVIDERID', ''));
        $this->valueArray['countryId']      = trim(RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_COUNTRYID', ''));
        $this->valueArray['salesPointId']   = trim(RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_SALESPOINTID', ''));
        $this->valueArray['description']    = trim(RsmartsepaHelper::getConstantValueAsString('MODULE_PAYMENT_RSMARTSEPA_TERMINALDATA_DESCRIPTION', ''));
    } // End constructor

    public function toArray() {
        return $this->valueArray;
    } // End toArray
    
    public function getTerminalData() {
        $Raautil_TerminalData = new Raautil_TerminalData($this->valueArray);
        return $Raautil_TerminalData;
    } // End getTerminalData

} // End class RsmartsepaTerminalDataProviderConfig

