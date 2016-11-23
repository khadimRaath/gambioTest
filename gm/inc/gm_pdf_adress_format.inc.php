<?php
/* --------------------------------------------------------------
   gm_pdf_adress_format.inc.php 2014-02-06 gambio
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

function gm_pdf_adress_format($string){
    $adress = explode('###', $string);

    foreach($adress as $value) {
        if(!empty($value)) {
            $new_adress[]= trim($value);
        }
    }
    $new_adress = implode(', ', $new_adress);
    return $new_adress;
}