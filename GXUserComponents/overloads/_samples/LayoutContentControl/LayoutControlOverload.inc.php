<?php
/* --------------------------------------------------------------
   LayoutControlOverload.inc.php 2016-06-24
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class LayoutControlOverload
 *
 * This sample demonstrates the overloading of the LayoutContentControl class. After enabling this sample head to
 * the admin shop online/offline and edit the topbar content. Then visit the shop frontend where you will see that
 * the content was changed by this overload.
 *
 * @see LayoutContentControl
 */
class LayoutControlOverload extends LayoutControlOverload_parent
{
	/**
	 * Overloaded constructor of the layout content control.
	 */
	public function __construct()
	{
		parent::__construct();
		
		$style = 'text-align: center;padding: 25px;margin: 50px  35px;background: #D9EDF7;color: #3187CC;';
		
		echo '
			<div style="' . $style . '">
				<h4>Layout Content Control Overload is used!</h4>
				<p>
					This overload will replace the topbar content message of the admin "Shop Online/Offline" page. 
				</p>
			</div>
		';
	}
	
	
	/**
	 * Overloaded "_addTopbarContent" method.
	 *
	 * This method will replace the content of the topbar element with a new one.
	 *
	 * @param ContentView $layoutView
	 */
	protected function _addTopbarContent(ContentView $layoutView)
	{
		$topbarContent = '';
		
		if(gm_get_conf('TOPBAR_NOTIFICATION_MODE', 'ASSOC', true) === 'permanent'
		   || (isset($_SESSION['hide_topbar']) && $_SESSION['hide_topbar'] !== true)
		   || !isset($_SESSION['hide_topbar'])
		)
		{
			/* @var TopbarContentView $view */
			$view          = MainFactory::create_object('TopbarContentView');
			$topbarContent = $view->get_html();
		}
		
		$topbarContent = preg_replace('/(<span .*?>).*?(<\/span>)/',
		                              '$1Yout topbar content was replaced by the overload!$2',
		                              str_replace("\r\n", '', $topbarContent), 1);
		
		$layoutView->set_('topbar_content', $topbarContent);
	}
}
