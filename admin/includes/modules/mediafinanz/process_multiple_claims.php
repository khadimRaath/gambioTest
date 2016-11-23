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

require ('includes/modules/mediafinanz/models/MF/Config.php');
require ('includes/modules/mediafinanz/models/MF/Address.php');
require ('includes/modules/mediafinanz/models/MF/Suspect.php');
require ('includes/modules/mediafinanz/models/MF/Encashment.php');
require ('includes/modules/mediafinanz/models/MF/Claim.php');

$config = MF_Config::getInstance();

//user wants to transmit all open orders to mediafinanz:
$encashment = new MF_Encashment();

//get orders, which are ready for transmission:
$ordersMarkedForMediafinanz = $encashment->getOrdersMarkedForMediafinanz();
$ordersMarkedForMediafinanz = $ordersMarkedForMediafinanz['entries'];

$counter = 0;
$failed = array();

foreach ($ordersMarkedForMediafinanz as $singleOrder)
{
    $counter ++;
    $order = $encashment->getOrder($singleOrder['orderId']);
    $reason = '';

    foreach ($order['products'] as $product)
    {
        $reason .= $product['quantity'].'x '.$product['model'].' '.$product['name'].'
';
    }

    //generate claim:
    $claim = new MF_Claim($order['orderId'], $config->getValue('defaultType'));
    $claim->setReason($reason);
    $claim->setOriginalValue($order['total']);
    $claim->setOverdueFees($config->getValue('overdueFees'));
    $claim->setDateOfOrigin(substr($order['purchaseDate'], 0, 10));
    $claim->setDateOfReminder(date('Y-m-d', strtotime($order['purchaseDate']) + $config->getValue('daysFromLastReminder') * 86400));

    //generate suspect:
    $suspect = MF_Suspect::createSuspect((int)$order['customerId']);

    //transmit claim:
    $result = $encashment->newClaim($suspect, $claim);

    if (!$result['success'])
    {
        $failed[] = array('orderId' => $order['orderId'],
                          'error'   => $result['error']);
    }
}

?>

<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">##process_multiclaims_heading</td>
            <td class="pageHeading" align="right"><?php echo xtc_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td>

        <table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td align="center">
            <?php
                if (count($failed) == 0)
                {
                    echo '##orders_transferred <a href="mediafinanz.php?action=claims">##back</a>';
                }
                else
                {
                    echo count($failed) . ' ##of_total ' . $counter . ' ##orders_cannot_be_transferred<br/><br/>';

                    foreach ($failed as $failedOrder)
                    {
                        echo $failedOrder['orderId'].': ' . $failedOrder['error'] . '<br/>';
                    }

                    echo '<a href="mediafinanz.php?action=claims">##back</a>';
                }
            ?>
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
