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

require_once('models/MF/Misc.php');

if (!empty($_GET['emptyTable']))
{
    if ($_GET['emptyTable'] == 'true')
    {
        // empty table mf_errors:
        $query = xtc_db_query("TRUNCATE TABLE mf_errors");
    }
}

// Fetch current error log
$errors = MF_Misc::getErrorLog();

?>

<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">##errors_overview</td>
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
            if (count($errors) > 0)
            {
                ?>
                     <table border="0" cellspacing="1" cellpadding="3" class="smalltext">
                     <tr><td colspan="3"><a href="mediafinanz.php?action=errors&emptyTable=true">##empty_table</a></td></tr>
                      <tr bgcolor="white">
                        <td>##date</td>
                        <td>##customer</td>
                        <td>##error</td>
                      </tr>
                <?php

                // generate rows:
                 $i = 0;
                 foreach ($errors as $error)
                 {
                     $color = ($i++ % 2 == 0) ? 'lightgrey' : 'white';
                     echo '<tr bgcolor="'.$color.'">
                                <td>'.date('d.m.Y H:i', $error['date']) .'</td>
                                <td>'.$error['firstname'].' '.$error['lastname'].'</td>
                                <td>'.$error['errorText'].'</td>
                           </tr>';
                 }

                 echo '</table>';
            }
            else
            {
                echo '##no_errors';
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