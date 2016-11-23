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

// Get config:
require_once ('includes/modules/mediafinanz/models/MF/Config.php');
$config = MF_Config::getInstance();

require_once ('includes/modules/mediafinanz/models/MF/Misc.php');
require_once ('includes/modules/mediafinanz/models/MF/Registration.php');

$options = isset($_GET['options'])  ? $_GET['options']  : 'general';
$options = isset($_POST['options']) ? $_POST['options'] : $options;
$options = file_exists('includes/modules/mediafinanz/config/'.$options.'.php') ? $options : 'general';

$store   = isset($_POST['store'])   ? true              : false;


$string =  '<td class="boxCenter" width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">##page_heading</td>
            <td class="pageHeading" align="right"><img src="images/pixel_trans.gif" border="0" alt="" width="HEADING_IMAGE_WIDTH" height="HEADING_IMAGE_HEIGHT"></td>
          </tr>
        </table>

         <form action="mediafinanz.php?action=config" method="POST" name="mediafinanz">
         <table border="0" cellspacing="1" cellpadding="3" class="config" width="800px">
            <tr>
               <td colspan="2">
                 <div>
                         <ul id="navlist">
                                 <li><a href="mediafinanz.php?action=config&options=general" id="configinactive">##general_info</a></li>
                                 <li><a href="mediafinanz.php?action=config&options=claim" id="configinactive">##claims_options</a></li>
                                 <li><a href="mediafinanz.php?action=config&options=score" id="configinactive">##general_credit_ratings_options</a></li>
                                 <li><a href="mediafinanz.php?action=config&options=score_person" id="configinactive">##person_credit_ratings_options</a></li>
                                 <li><a href="mediafinanz.php?action=config&options=score_company" id="configinactive">##corporate_credit_ratings_options</a></li>
                         </ul>
                 </div>
               </td>
            </tr>';

$string = str_replace($options.'" id="configinactive"', $options.'" id="configactive"', $string);
echo $string;

include('includes/modules/mediafinanz/config/'.$options.'.php');

echo '<tr>
        <td>&nbsp;</td>
        <td class="smallText"><input type="hidden" name="options" value="'.$options.'"/>
                              <input type="hidden" name="store" value="1"/>
                              <input type="submit" /></td>
      </tr>';

if (!empty($msg))
{
    echo '<td class="smallText" colspan="2">'.$msg.'</td>';
}

echo '</table></form></table>';