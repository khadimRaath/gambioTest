<?php
/* --------------------------------------------------------------
  TransactionException.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class Raautil_TransactionException extends Exception {
    
    private $unauthorizedCall = FALSE;
    
    public function __construct($msg, $code = 0, $previous = null, $unauthorizedCall = FALSE) {
        parent::__construct($msg, isset($code) ? (is_numeric($code) ? intval($code) : 0) : 0, $previous);
        $this->unauthorizedCall = isset($unauthorizedCall) ? ($unauthorizedCall == TRUE ? TRUE : FALSE) : FALSE;
        if($this->unauthorizedCall == TRUE) {
            $this->code = 8;
        }
    } // End constructor
    
    public function isUnauthorizedCall() {
        return $this->unauthorizedCall;
    } // End isUnauthorizedCall
    
} // End class Raautil_TransactionException


