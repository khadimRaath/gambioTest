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

if ($store)
{
    $config->storeValue('recheckSuspect', (int)($_POST['recheckSuspect']));
    $config->storeValue('paymentModules', strtolower(str_replace(' ', '', xtc_db_input($_POST['paymentModules']))));
    $config->storeValue('orderTotal', (int)($_POST['orderTotal']));
    $config->storeValue('allowPaymentWithNoResult', (int)($_POST['allowPaymentWithNoResult']));
    $config->storeValue('requestType', $_POST['requestType']);
    $config->storeValue('requestOnModules', strtolower(str_replace(' ', '', xtc_db_input($_POST['requestOnModules']))));
    $config->storeValue('paymentErrorText', xtc_db_input($_POST['paymentErrorText']));
    $config->storeValue('minAmountForRequest', (int) xtc_db_input($_POST['minAmountForRequest']));
    $config->storeValue('maxAmountForRequest', (int) xtc_db_input($_POST['maxAmountForRequest']));
    $config->storeValue('allowPaymentUnderMinAmount', (int) xtc_db_input($_POST['allowPaymentUnderMinAmount']));
    $config->storeValue('allowPaymentOverMaxAmount',(int) xtc_db_input($_POST['allowPaymentOverMaxAmount']));

    $msg = '<strong style="font-size:14px;">##data_saved</strong>';
}

?>

<tr>
    <td id="configtitle" colspan="2"><br />##general_credit_ratings_options</td>
</tr>
<tr>
    <td class="smallText"><b>##min_amount_for_request</b></td>
    <td class="smallText"><input type="text" name="minAmountForRequest" size="50" value="<?php echo $config->getValue('minAmountForRequest'); ?>"/></td>
</tr>
<tr>
    <td class="smallText"><b>##allow_payment_under_min_amount</b></td>
    <td class="smallText">
    <?php

    if ($config->getValue('allowPaymentUnderMinAmount') == 1)
    {
        echo '<input type="radio" name="allowPaymentUnderMinAmount" value="0">##active</input><br />
              <input type="radio" name="allowPaymentUnderMinAmount" value="1" checked>##inactive</input>';
    }
    else
    {
        echo '<input type="radio" name="allowPaymentUnderMinAmount" value="0" checked>##active</input><br />
              <input type="radio" name="allowPaymentUnderMinAmount" value="1">##inactive</input>';
    }
    ?>
    </td>
</tr>
<tr>
    <td class="smallText"><b>##max_amount_for_request</b></td>
    <td class="smallText"><input type="text" name="maxAmountForRequest" size="50" value="<?php echo $config->getValue('maxAmountForRequest'); ?>"/></td>
</tr>
<tr>
    <td class="smallText"><b>##allow_payment_over_max_amount</b></td>
    <td class="smallText">
    <?php

    if ($config->getValue('allowPaymentOverMaxAmount') == 1)
    {
        echo '<input type="radio" name="allowPaymentOverMaxAmount" value="0">##active</input><br />
              <input type="radio" name="allowPaymentOverMaxAmount" value="1" checked>##inactive</input>';
    }
    else
    {
        echo '<input type="radio" name="allowPaymentOverMaxAmount" value="0" checked>##active</input><br />
              <input type="radio" name="allowPaymentOverMaxAmount" value="1">##inactive</input>';
    }
    ?>
    </td>
</tr>
<tr>
    <td class="smallText"><b>##request_type</b></td>
    <td class="smallText">
    <?php

    if ($config->getValue('requestType') == 'always')
    {
        echo '<input type="radio" name="requestType" value="always" checked>##request_type_always</input><br />
              <input type="radio" name="requestType" value="paymentDepending">##request_type_payment_depending</input>';
    }
    else
    {
        echo '<input type="radio" name="requestType" value="always">##request_type_always</input><br />
              <input type="radio" name="requestType" value="paymentDepending" checked>##request_type_payment_depending</input>';
    }
    ?>
    </td>
</tr>
<tr>
    <td class="smallText"><b>##recheck_suspect</b></td>
    <td class="smallText"><input type="text" name="recheckSuspect" size="5" value="<?php echo $config->getValue('recheckSuspect'); ?>"/></td>
</tr>
<tr>
    <td class="smallText"><b>##payment_modules</b></td>
    <td class="smallText"><input type="text" name="paymentModules" size="50" value="<?php echo $config->getValue('paymentModules'); ?>"/></td>
</tr>
<tr>
    <td class="smallText"><b>##order_total</b></td>
    <td class="smallText"><input type="text" name="orderTotal"size="5"  value="<?php echo $config->getValue('orderTotal'); ?>"/></td>
</tr>
<tr>
    <td class="smallText"><b>##allow_payment_with_no_result</b></td>
    <td class="smallText">
    <?php

    if ($config->getValue('allowPaymentWithNoResult') == 1)
    {
        echo '<input type="radio" name="allowPaymentWithNoResult" value="1" checked>##active</input><br />
              <input type="radio" name="allowPaymentWithNoResult" value="0">##inactive</input>';
    }
    else
    {
        echo '<input type="radio" name="allowPaymentWithNoResult" value="1">##active</input><br />
              <input type="radio" name="allowPaymentWithNoResult" value="0" checked>##inactive</input>';
    }
?>
    </td>
</tr>
<tr class="configSeparator">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
</tr>
<tr>
    <td id="configtitle" colspan="2"><br />##options_for_payment_method_dependent_requests</td>
</tr>
<tr>
    <td class="smallText"><b>##request_on_modules</b></td>
    <td class="smallText"><input type="text" name="requestOnModules" size="50" value="<?php echo $config->getValue('requestOnModules'); ?>"/></td>
</tr>
<tr>
    <td class="smallText"><b>##payment_error_text</b></td>
    <td class="smallText"><textarea name="paymentErrorText" rows="5" cols="47"><?php echo htmlspecialchars($config->getValue('paymentErrorText')); ?></textarea></td>
</tr>