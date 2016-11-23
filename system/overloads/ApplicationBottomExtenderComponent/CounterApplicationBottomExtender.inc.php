<?php
/* --------------------------------------------------------------
   GPrintApplicationBottomExtender.inc.php 2012-05-23 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2012 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class CounterApplicationBottomExtender extends CounterApplicationBottomExtender_parent
{
	function proceed()
	{
		// -> create new counter obj if not exist
		if(empty($_SESSION['gm_tracking']))
		{
			$gm_tracking = new GMC();

			$_SESSION['gm_tracking'] = $gm_tracking;

			//	-> set once a time ip, browser and platform of the current user
			$this->v_output_buffer['SCRIPT_COUNTER'] = $_SESSION['gm_tracking']->gmc_set_current_user(false);
		}

		$_SESSION['gm_tracking']->gmc_record($this->v_data_array['products_id'], $this->v_data_array['cPath']);
		$_SESSION['gm_tracking']->gmc_delete_old_ip();

		parent::proceed();
	}
}
?>