<?php
/* --------------------------------------------------------------
  ITerminalDataProvider.php 2015-04-24 wem
  Smart Payment Solutions GmbH
  http://www.smart-payment-solutions.de/
  Copyright (c) 2015 Smart Payment Solutions GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

interface Raautil_ITerminalDataProvider {
    
    /**
     * Returns an instance of Raautil_TerminalData.
     * 
     * @return Raautil_TerminalData
     *    An instance of Raautil_TerminalData
     * 
     * @throws Raautil_TransactionException
     *    In an error occured
     */
    public function getTerminalData();
    
} // End interface Raautil_ITerminalDataProvider

