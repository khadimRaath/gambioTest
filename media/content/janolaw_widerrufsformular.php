<?php
/* --------------------------------------------------------------
   janolaw_agb.php 2016-01-11
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_janolaw_page = 'model-withdrawal-form';

if(!defined('_GM_VALID_CALL'))
{
   chdir('../../');
   require 'includes/application_top.php';
   $include_mode = false;
}
else
{
   $include_mode = true;
}

$janolaw = MainFactory::create('GMJanolaw');
$html_format = true;
$cache_file_name = '';
echo $janolaw->get_page_content($t_janolaw_page, $include_mode, $html_format, $cache_file_name, $_SESSION['language_code']);
