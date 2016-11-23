<?php
/* --------------------------------------------------------------
   GoogleConversionTrackingCheckoutSuccessExtender.inc.php 2014-10-17 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

class GoogleConversionTrackingCheckoutSuccessExtender extends GoogleConversionTrackingCheckoutSuccessExtender_parent
{
	function proceed()
	{
		parent::proceed();
		
		$this->v_output_buffer['GOOGLE_CONVERSION'] = GOOGLE_CONVERSION;
		
		if( isset($this->v_data_array['orders_id'])
			&& !empty($this->v_data_array['orders_id'])
			&& isset($this->v_data_array['coo_order'])
			&& is_object($this->v_data_array['coo_order']) )
		{
			$t_amount = round($this->v_data_array['coo_order']->info['pp_total'], 2);
			
			$t_conversion_id = GOOGLE_CONVERSION_ID;
			
			$t_html = '
				<!-- Google Code for Purchase Conversion Page -->
				<script type="text/javascript">
				/* <![CDATA[ */
				var google_conversion_id = ' . GOOGLE_CONVERSION_ID . ';
				var google_conversion_language = "' . GOOGLE_LANG . '";
				var google_conversion_format = "2";
				var google_conversion_color = "ffffff";
				if (' . $t_amount . ') {
					var google_conversion_value = ' . $t_amount . ';
				}
				var google_conversion_label = "' . GOOGLE_CONVERSION_LABEL . '";
				/* ]]> */
				</script>
				<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js">
				</script>
				<noscript>
				<div style="display:inline;">
				<img height="1" width="1" style="border-style:none;" alt="" src="https://www.googleadservices.com/pagead/conversion/' . GOOGLE_CONVERSION_ID . '/?value=' . $t_amount . '&amp;label=' . GOOGLE_CONVERSION_LABEL . '&amp;guid=ON&amp;script=0"/>
				</div>
				</noscript>
			';
			$this->v_output_buffer['GOOGLE_CONVERSION_BLOCK'] = !empty($t_conversion_id) ? $t_html : '';
		}
	}
}