<?php

/* -----------------------------------------------------------------------------------------
   Copyright (c) 2011 mediafinanz AG

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program.  If not, see http://www.gnu.org/licenses/.
   ---------------------------------------------------------------------------------------

 * @author Marcel Kirsch
 */

require_once ('includes/modules/mediafinanz/models/MF/Config.php');
require_once ('includes/modules/mediafinanz/models/MF/Encashment.php');
require_once ('includes/modules/mediafinanz/models/MF/Misc.php');

$config = MF_Config::getInstance();

$fileNumber = (int) xtc_db_input($_POST['fileNumber']);
$customerId = (int) xtc_db_input($_POST['customerId']);

//close claim:
$encashment = new MF_Encashment();
$result = false;

try
{
    if (!empty($_POST['dateOfPayment']) && !empty($_POST['paidAmount']))
    {
        // Direct Payment: build appropriate array
        $directPayment = array('dateOfPayment' => xtc_db_input($_POST['dateOfPayment']),
                               'paidAmount'   => xtc_db_input($_POST['paidAmount']));

        $result = $encashment->bookDirectPayment($fileNumber, $directPayment);
    }
}
catch (Exception $e)
{
    MF_Misc::errorLog($customerId, utf8_decode(xtc_db_input($e->getMessage())));
}

?>

<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">##claims_heading</td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>

        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td>

            <?php

            if ($result === true)
            {
                echo '##direct_payment_reported';
            }
            else
            {
                echo '##error_reporting_direct_payment';

            }
            ?>
            </td>
          </tr>
          <tr>
              <td>
                  <a href=<?php echo'"mediafinanz.php?action=display_claim&oID='.$_POST['orderId'].'"'; ?>>##back</a>
              </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
      <tr>
        <td>

        </td>
      </tr>
    </table></td>
