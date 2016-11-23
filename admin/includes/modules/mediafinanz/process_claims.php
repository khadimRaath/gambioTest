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
require ('includes/modules/mediafinanz/models/MF/Encashment.php');

$config = MF_Config::getInstance();
$ordersId = (int) xtc_db_input($_GET['oID']);
$encashment = new MF_Encashment();

if ($_GET['store'] == 'true')
{
    $orderQuery = xtc_db_query('SELECT
                                    date_purchased
                                FROM
                                    orders
                                WHERE
                                    orders.orders_id = '.$ordersId.'
                                LIMIT 1');

    $orderErg = mysqli_fetch_assoc($orderQuery);

    require ('includes/modules/mediafinanz/models/MF/Address.php');
    require ('includes/modules/mediafinanz/models/MF/Suspect.php');
    require ('includes/modules/mediafinanz/models/MF/Claim.php');

    //generate claim:
    $claim = new MF_Claim($ordersId, $_POST['claimType']);
    $claim->setReason(xtc_db_input($_POST['reason']));
    $claim->setOriginalValue(xtc_db_input($_POST['originalValue']));
    $claim->setOverdueFees(xtc_db_input($_POST['overdueFees']));
    $claim->setDateOfOrigin(substr($orderErg['date_purchased'], 0, 10));
    $claim->setDateOfReminder(xtc_db_input($_POST['dateOfLastReminder']));
    $claim->setNote(xtc_db_input(str_replace(array("\r", "\n", "\t"), '', $_POST['notice'])));

    //generate suspect:
    $suspect = MF_Suspect::createSuspectByOrder($ordersId);
    $suspect->setLastname($_POST['lastname']);
    $suspect->setFirstname($_POST['firstname']);

    //transmit claim:
    $result = $encashment->newClaim($suspect, $claim);
}
else
{
    //get details to an order:
    $order = $encashment->getOrder($ordersId);
}

?>

<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">##process_claims_heading</td>
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

            if (!empty($order) && $_GET['store'] != 'true')
            {
                 if ((!empty($order['firstname'])) && (!empty($order['lastname'])))
                     {
                         $firstname = $order['firstname'];
                         $lastname  = $order['lastname'];
                     }
                     else
                     {
                         $explodeMark = strrpos($order['tmpName'], ' ');
                         $firstname = substr($order['tmpName'], 0, $explodeMark);
                         $lastname   = substr($order['tmpName'], $explodeMark + 1);
                     }
                ?>
                <form action="mediafinanz.php?action=process_claim&store=true&oID=<?php echo $ordersId; ?>" method="POST" name="newClaim">
                     <script type="text/javascript">
                        var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "newClaim", "dateOfLastReminder","btnDate1","<?php echo date('Y-m-d', strtotime($order['purchaseDate']) + $config->getValue('daysFromLastReminder') * 86400); ?>",scBTNMODE_CUSTOMBLUE);
                     </script>
                     <?php echo '<!--'.date('Y-m-d', strtotime($order['purchaseDate']) + $config->getValue('daysFromLastReminder') * 86400).'-->'; ?>
                     <table border="0" cellspacing="1" cellpadding="3" class="smalltext">
                      <tr bgcolor="white">
                        <td><b>##firstname</b></td>
                        <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" /></td>
                      </tr>
                      <tr bgcolor="white">
                        <td><b>##lastname</b></td>
                        <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" /></td>
                      </tr>
                      <tr bgcolor="white">
                        <td><b>##invoice_number</b></td>
                        <td><b><?php echo $order['orderId']; ?></b></td>
                      </tr>
                      <tr bgcolor="white">
                        <td><b>##original_value</b></td>
                        <td><b><?php echo sprintf("%01.2f", $order['total']); ?></b><input type="hidden" name="originalValue" value="<?php echo $order['total']; ?>" /></td>
                      </tr>
                      <tr bgcolor="white">
                        <td><b>##order_date</b></td>
                        <td><b><?php echo $order['purchaseDate']; ?></b></td>
                      </tr>
                      <tr bgcolor="white">
                        <td><b>##claim_type</b></td>
                        <td><b>
                            <select name="claimType">
                                <option value="1">##goods_sold</option>
                                <option value="2">##goods_sold_precharge</option>
                                <option value="3">##services_rendered</option>
                            </select>
                        </b></td>
                        </tr>
                      <tr bgcolor="white">
                        <td valign="top"><b>##reason<br/>##printed_as_is</b></td>
                        <td>
                        <?php
                            $productString = '';
                            foreach ($order['products'] as $entry)
                            {
                                $productString .= $entry['quantity'].'x '.$entry['model'].' '.$entry['name'].', ';
                            }
                            $productString = substr($productString, 0, -2);
                        ?>
                        <textarea name="reason" cols="30" rows="10"><?php echo $productString; ?></textarea>
                        </td>
                       </tr>
                      <tr bgcolor="white">
                        <td><b>##overdue_fees_so_far</b></td>
                        <td><b><input type="text" name="overdueFees" value="<?php echo $config->getValue('overdueFees'); ?>"/></b></td>
                      </tr>
                       <tr bgcolor="white">
                        <td><b>##date_last_appeal</b></td>
                        <td><script type="text/javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
                      </tr>
                      <tr bgcolor="white">
                        <td><b>##notice</b></td>
                        <td><b><textarea name="notice" cols="30" rows="10"> </textarea></b></td>
                      </tr>
                      <tr bgcolor="white">
                        <td colspan="2"><input type="submit" /></td>
                      </tr>
                    </table>
                  </form>

                <?php
            }
            elseif ($_GET['store'] == 'true')
            {
                if ($result['success'])
                {
                    echo '##claim_transferred<br/ >##file_number: '.$result['fileNumber'].'<br/> <a href="mediafinanz.php?action=claims">##back</a>';
                }
                else
                {
                    $path = parse_url($_SERVER['HTTP_REFERER']);
                    echo $result['error'].'<br/> <a href="mediafinanz.php?'.$path['query'].'">##back</a>';
                }
            }
            else
            {
                echo '##no_data_with_invoice_id';
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
