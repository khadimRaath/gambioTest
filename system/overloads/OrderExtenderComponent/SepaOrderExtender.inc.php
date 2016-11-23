<?php
/* --------------------------------------------------------------
  SepaOrderExtender.inc.php 2013-12-20 gm
  Gambio GmbH
  http://www.gambio.de
  Copyright (c) 2013 Gambio GmbH
  Released under the GNU General Public License (Version 2)
  [http://www.gnu.org/licenses/gpl-2.0.html]
  --------------------------------------------------------------
 */

class SepaOrderExtender extends SepaOrderExtender_parent
{
	function proceed()
	{
		$t_sql = 'SHOW TABLES LIKE "sepa"';
		$t_result = xtc_db_query($t_sql);
		
		if(xtc_db_num_rows($t_result) > 0)
		{
			$t_sql = 'SELECT 
							sepa_owner, 
							sepa_iban, 
							sepa_bic, 
							sepa_bankname, 
							sepa_prz, 
							sepa_status, 
							sepa_fax 
						FROM sepa 
						WHERE orders_id = "' . xtc_db_input($this->v_data_array['GET']['oID']) . '"';
			$t_result = xtc_db_query($t_sql);

			if(xtc_db_num_rows($t_result) == 1)
			{
				$t_result_array = xtc_db_fetch_array($t_result);
				if($t_result_array['sepa_bankname'] || $t_result_array['sepa_bic'] || $t_result_array['sepa_iban'])
				{
				?>
					<table border="0" width="100%" cellspacing="0" cellpadding="0" class="pdf_menu">
						<tr>
							<td class="dataTableHeadingContent" style="border-right: 0px;">
								SEPA
							</td>
						</tr>
					</table>
					<table border="0" width="100%" cellspacing="0" cellpadding="2" class="sepa">
						<tr>
							<td width="80" class="main gm_strong">
								<?php echo TEXT_BANK_OWNER; ?>
							</td>
							<td colspan="5" class="main">
								<?php echo $t_result_array['sepa_owner']; ?>
							</td>
						</tr>
						<tr>
							<td width="80" class="main gm_strong">
								IBAN:
							</td>
							<td colspan="5" class="main">
								<?php echo $t_result_array['sepa_iban']; ?>
							</td>
						</tr>
						<tr>
							<td width="80" class="main gm_strong">
								BIC:
							</td>
							<td colspan="5" class="main">
								<?php echo $t_result_array['sepa_bic']; ?>
							</td>
						</tr>
						<tr>
							<td width="80" class="main gm_strong">
								<?php echo TEXT_BANK_NAME; ?>
							</td>
							<td colspan="5" class="main">
								<?php echo $t_result_array['sepa_bankname']; ?>
							</td>
						</tr>

						<?php
						if($t_result_array['sepa_status'] == 0)
						{
						?>
						<tr>
							<td width="80" class="main gm_strong">
								<?php echo TEXT_BANK_STATUS; ?>
							</td>
							<td colspan="5" class="main">
								<?php echo "OK"; ?>
							</td>
						</tr>
						<?php
						}
						else
						{
						?>
						<tr>
							<td width="80" class="main gm_strong">
								<?php echo TEXT_BANK_STATUS; ?>
							</td>
							<td colspan="5" class="main">
								<?php echo $t_result_array['sepa_status']; ?>
							</td>
						</tr>
						<?php
						$t_error_text = '';

						switch($t_result_array['sepa_status'])
						{
							case 1 :
								$t_error_text = TEXT_BANK_ERROR_1;
								break;
							case 2 :
								$t_error_text = TEXT_BANK_ERROR_2;
								break;
							case 3 :
								$t_error_text = TEXT_BANK_ERROR_3;
								break;
							case 4 :
								$t_error_text = TEXT_BANK_ERROR_4;
								break;
							case 5 :
								$t_error_text = TEXT_BANK_ERROR_5;
								break;
							case 8 :
								$t_error_text = TEXT_BANK_ERROR_8;
								break;
							case 9 :
								$t_error_text = TEXT_BANK_ERROR_9;
								break;
						}
						?>
						<tr>
							<td width="80" class="main gm_strong">
								<?php echo TEXT_BANK_ERRORCODE; ?>
							</td>
							<td colspan="5" class="main">
								<?php echo $t_error_text; ?>
							</td>
						</tr>
						<tr>
							<td width="80" class="main gm_strong">
								<?php echo TEXT_BANK_PRZ; ?>
							</td>
							<td colspan="5" class="main">
								<?php echo $t_result_array['sepa_prz']; ?>
							</td>
						</tr>
						<?php
						}
						?>
					</table>
				<?php
				}

				if($t_result_array['sepa_fax'])
				{
				?>
					<table border="0" width="100%" cellspacing="0" cellpadding="2" class="sepa">
						<tr>
							<td class="main gm_strong">
								<?php echo TEXT_BANK_FAX; ?>
							</td>
						</tr>
					</table>
				<?php
				}
			}
		}
		$this->v_output_buffer['below_order_info_heading'] = TITLE_SEPA_INFO;
		$this->addContent();
		parent::proceed();
	}
}