<?php
/* --------------------------------------------------------------
  RsmartsepaTransactionLogger.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */
class RsmartsepaTransactionLogger extends Raa_Logger {
    
    public function log($type = 'debug', $title = '', $output = '') {
        if(class_exists('RsmartsepaHelper', FALSE)) {
            $output = str_replace('<br/>', "\r\n", $output);
            RsmartsepaHelper::debug('log', $title, $output);
        }
    } // end log
    
} // End RsmartsepaTransactionLogger

