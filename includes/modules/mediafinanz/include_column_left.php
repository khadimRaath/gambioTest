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

$version = phpversion();
$majorVersion = explode('.', $version);
$majorVersion = intval($majorVersion[0]);

if ($majorVersion < 5)
{
    return;
}

//MEDIAFINANZ START
echo ('<div class="dataTableHeadingContent"><b>mediafinanz</b></div>');
if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['mediafinanz'] == '1'))
{
    echo '<a href="' . xtc_href_link('mediafinanz.php?action=config', '', 'NONSSL') . '" class="menuBoxContentLink"> -' . Konfiguration . '</a><br>';
}

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['mediafinanz'] == '1'))
{
    echo '<a href="' . xtc_href_link('mediafinanz.php?action=errors', '', 'NONSSL') . '" class="menuBoxContentLink"> -' . Fehler . '</a><br>';
}

if (($_SESSION['customers_status']['customers_status_id'] == '0') && ($admin_access['mediafinanz'] == '1'))
{
    echo '<a href="' . xtc_href_link('mediafinanz.php?action=claims', '', 'NONSSL') . '" class="menuBoxContentLink"> -' . Forderungen . '</a><br>';
}
//MEDIAFINANZ END
?>