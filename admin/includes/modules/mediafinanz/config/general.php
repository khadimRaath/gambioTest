<?php
/* --------------------------------------------------------------
   general.php 2012-11-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

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
    $config->storeValue('clientLicence', xtc_db_input(trim($_POST['clientLicence'])));
    $config->storeValue('clientId', (int)($_POST['clientId']));
    $config->storeValue('sandbox', (int)($_POST['sandbox']));

    $msg = '<strong style="font-size:14px;">##data_saved</strong>';
}

$coo_mediafinanz = MainFactory::create_object('GMDataObject', array('mf_config', array('config_key' => 'clientLicence')));
$t_mediafinanz_licence = $coo_mediafinanz->get_data_value('config_value');

if(empty($t_mediafinanz_licence) == false || isset($_GET['show_registerKey']))
{
	$registerKey = $config->getValue('registrationKey');
	if (strlen($registerKey) == 0)
	{
		$registrationService = new MF_Registration();
		$registerKey = $registrationService->createRegistrationKey();
		$config->storeValue('registrationKey', $registerKey);
	}
}

$t_sandbox_active = $config->getValue('sandbox') == 1;

?>
<tr>
    <td id="configtitle" colspan="2"><br /></td>
</tr>
<tr>
    <td id="smallText" colspan="2">##steps_to_use_module:
        <ol>
            <li>##step_one: <a href="http://www.mediafinanz.de/de/service/zugangsdaten?ref=Partner_Gambio" target="_blank">##sign_up</a></li>
            <li>##step_two_a <a href="https://mandos.mediafinanz.de/api" target="_blank">https://mandos.mediafinanz.de/api</a> ##step_two_b:<div id="registrationKey"><?php echo $registerKey;?></div>. <a href="<?php echo xtc_href_link('mediafinanz.php', 'action=config&show_registerKey=1'); ?>">##get_registration_key</a></li>
            <li>##step_three.</li>
            <li>##step_four.</li>
        </ol>
    </td>
</tr>
<tr>
    <td id="configtitle" colspan="2"><br />##general_options</td>
</tr>
<tr>
    <td class="smallText"><b>##version</b></td>
    <td class="smallText"><?php echo $config->getValue('version'); ?></td>
</tr>
<tr>
    <td class="smallText"><b>##use_sandbox_mode</b></td>
    <td class="smallText">
      <input type="radio" name="sandbox" id="sandbox_active" value="1" <?php echo $t_sandbox_active === true ? 'checked="checked"' : '' ?>>
        <label for="sandbox_active">##sandbox_active</label>
      <input type="radio" name="sandbox" id="sandbox_inactive" value="0" <?php echo $t_sandbox_active !== true ? 'checked="checked"' : '' ?>>
        <label for="sandbox_inactive">##sandbox_inactive</label>
    </td>
</tr>
<tr>
    <td class="smallText"><b>##client_licence</b></td>
    <td class="smallText"><input type="text" name="clientLicence" size="50" value="<?php echo $config->getValue('clientLicence'); ?>"/></td>
</tr>
<tr>
    <td class="smallText"><b>##client_id</b></td>
    <td class="smallText"><input type="text" name="clientId" size="5" value="<?php echo $config->getValue('clientId'); ?>"/></td>
</tr>
