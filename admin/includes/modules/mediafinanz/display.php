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

require ('includes/modules/mediafinanz/models/MF/Suspect.php');
require ('includes/modules/mediafinanz/models/MF/Address.php');
require ('includes/modules/mediafinanz/models/MF/Config.php');


/**
 * Creates a Suspect out of an order id
 *
 * ::CAUTION:: we need this function and not the equivalent of MF_Suspect, because
 * we want to use the fields customers_* for scoring not billing_*.
 * It is not possible to use MF_Suspect::createSuspect($customerId), because an order
 * of an guest account would have no entry in customer table.
 *
 * @param int $orderId
 * @return MF_Suspect
 */
function createSuspectByOrder($orderId)
{
    // Fetch complete customer information from order
    $orderQuery = xtc_db_query('SELECT
                                    IFNULL(customers_company, "") AS company,
                                    customers_id,
                                    customers_email_address AS email,
                                    customers_telephone AS phone,
                                    customers_firstname AS firstname,
                                    customers_lastname AS lastname,
                                    customers_street_address AS street,
                                    customers_postcode AS postcode,
                                    customers_city AS city,
                                    countries_iso_code_2 as country,
                                    IFNULL(customers_vat_id, "") as vat_id
                                FROM
                                    orders
                                JOIN  countries
                                  ON customers_country = countries_name
                                WHERE
                                    orders_id = '.(int) xtc_db_input($orderId).'
                                LIMIT 1');

    $result = mysqli_fetch_assoc($orderQuery);

    if ($result)
    {
        //suspect found!
        $address = new MF_Address($result['street'],
                                  $result['city'],
                                  $result['postcode'],
                                  $result['country']);

        $suspect = new MF_Suspect($result['customers_id'],
                                  $result['company'],
                                  '@',
                                  $result['firstname'],
                                  $result['lastname'],
                                  $result['email'],
                                  $result['phone'],
                                  $result['vat_id'],
                                  $address,
                                  '');

        return $suspect;
    }

    return false;
}



if (isset($_GET['cID']))
{
    $customerId = (int) xtc_db_input($_GET['cID']);
    $suspect = MF_Suspect::createSuspect($customerId);
}
else if (isset($_GET['oID']))
{
    $orderId = (int) xtc_db_input($_GET['oID']);
    $suspect = createSuspectByOrder($orderId);
    $customerId = $suspect->getCustomerId();
}
$newScore = false;

// Check if a new score should be get by mf:
if ($_GET['getPersonScore'] == 'true')
{
   $config = MF_Config::getInstance();

    if ($suspect !== false)
    {
        $scoreClass = $suspect->getScoreClass();
        $scoreResult = $scoreClass->getScore($suspect);

        if ($scoreResult)
        {
            $newScore = true;
        }
    }
}

$scoreClass = $suspect->getScoreClass();
$score = $scoreClass->getOldScore($suspect);

?>
<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">##creditworthiness_overview</td>
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
            if ($score)
            {
                ?>
                     <table border="0" cellspacing="1" cellpadding="3" class="smalltext">
                      <tr bgcolor="white">
                        <td><b>
                        <?php
                            //show name depending on person or company:
                            if (!$suspect->isCompany())
                            {
                                echo $suspect->getFirstname().' '.$suspect->getLastname();
                            }
                            else
                            {
                                echo $suspect->getCompany();
                            } ?>
                           </b></td>
                        <td>##score:<br/> <strong><?php echo $score['score']; ?></strong></td>
                        <?php $ident = isset($_GET['oID']) ? '&oID='.$_GET['oID'] : '&cID='.$_GET['cID']; ?>
                        <td>##last_check:<br/> <?php echo date('d.m.Y H:i', $score['requestTime']).'<br/><a href="mediafinanz.php?action=display&getPersonScore=true'.$ident.($isPopup ? '&popup=true' : ''); ?>">##check_again</a></td>
                      </tr>
                      <tr bgcolor="white">
                        <td colspan="5"> <?php echo $score['text'] ?></td>
                      </tr>

                      <?php

                      if (!empty($score['negativeEntries']))
                      {
                          echo '<tr><td colspan="3"><table class="smalltext"><tr bgcolor="lightgrey"><td colspan="7"><b>##negative_entries:</b></td>
                                <tr bgcolor="white">
                                  <td>##code:</td>
                                  <td>##reason:</td>
                                  <td>##amount_optional:</td>
                                  <td>##type_of_value:</td>
                                  <td>##number_entries:</td>
                                  <td>##date_optional:</td>
                                </tr>';
                          echo $score['negativeEntries'];

                          echo '</table></td></tr>';
                      }

                      if ($_GET['getPersonScore'] == 'true')
                      {
                          if ($newScore)
                          {
                              echo '<tr bgcolor="white"><td colspan="3">##recheck_successful</td></tr>';
                          }
                          else
                          {
                              echo '<tr bgcolor="white"><td colspan="3">##recheck_failed</td></tr>';
                          }
                      }

                     ?>

                    </table>

                <?php
            }
            else
            {
                $ident = isset($_GET['oID']) ? '&oID='.$_GET['oID'] : '&cID='.$_GET['cID'];
                echo '##no_data_available <a href="mediafinanz.php?action=display&getPersonScore=true'.$ident.($isPopup ? '&popup=true' : '').'">##get_info</a>';

                if (($_GET['getPersonScore'] == 'true') && !$newScore)
                {
                    echo '<br/>##check_failed';
                }
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
