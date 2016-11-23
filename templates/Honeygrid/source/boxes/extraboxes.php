<?php
/* --------------------------------------------------------------
  extraboxes.php 2016-01-07
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2016 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

$t_boxes = 9;

for($i = 1; $i <= $t_boxes; $i++)
{
	$coo_extrabox = MainFactory::create_object('ExtraboxesBoxContentView');
	if($GLOBALS['coo_template_control']->get_menubox_status('extrabox' . $i))
	{
		$coo_extrabox->reset_content_array();
		$coo_extrabox->set_('extrabox_number', $i);
		$t_box_html = $coo_extrabox->get_html();
		$gm_box_pos = $GLOBALS['coo_template_control']->get_menubox_position('extrabox' . $i);

		$this->set_content_data($gm_box_pos, $t_box_html);
	}
}