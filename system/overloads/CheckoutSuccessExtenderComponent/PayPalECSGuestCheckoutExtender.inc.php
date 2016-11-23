<?php
/* --------------------------------------------------------------
	PayPalECSGuestCheckoutExtender.inc.php 2015-07-31
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * This CheckoutSuccessExtender is used by the PayPal ECS guest flow to asynchronously log out the customer at the end of checkout
 */
class PayPalECSGuestCheckoutExtender extends PayPalECSGuestCheckoutExtender_parent
{
	public function proceed()
	{
		parent::proceed();

		if(isset($_SESSION['paypal_ecs_logout_required']))
		{
			if(is_numeric($_SESSION['customer_id']) && (int)$_SESSION['customer_id'] == (double)$_SESSION['customer_id']
			   && $_SESSION['customer_id'] > 0
			)
			{
				$logoffIFrameURL = xtc_href_link('shop.php', 'do=PayPal/LogoffECSCustomer', 'SSL');
				$iframeStyle = 'width: 0; height: 0; border: none;';
				$iframeSnippet = '<iframe id="ecslogoff" style="'.$iframeStyle.'" src="'.$logoffIFrameURL.'"></iframe>';
				if(is_array($this->html_output_array))
				{
					$this->html_output_array[] = $iframeSnippet;
				}
				else
				{
					echo $iframeSnippet;
				}
			}
		}
	}

}