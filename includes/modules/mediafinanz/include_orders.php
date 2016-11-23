<?php
/* --------------------------------------------------------------
   include_orders.php 2012-02-14 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   ----------------------------------------------------------

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

$version = phpversion();
$majorVersion = explode('.', $version);
$majorVersion = intval($majorVersion[0]);

$coo_mediafinanz = MainFactory::create_object('GMDataObject', array('mf_config', array('config_key' => 'clientLicence')));
$t_mediafinanz_licence = $coo_mediafinanz->get_data_value('config_value');

if ($majorVersion < 5 || empty($t_mediafinanz_licence))
{
    return;
}
?>
<a style="float:right" class="button" href="Javascript:void()" onclick="window.open('<?php echo xtc_href_link('mediafinanz.php', 'action=display&popup=true&oID='.(int)$_GET['oID']); ?>', 'popup', 'toolbar=0, width=700, height=500')">Bonit&auml;ts&uuml;bersicht</a>