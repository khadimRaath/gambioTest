<?php
/* --------------------------------------------------------------
   TopbarContentView.inc.php 2014-10-06 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * Class TopbarContentView
 */
class TopbarContentView extends ContentView
{
	public function __construct()
	{
		parent::__construct();

		$this->set_content_template('module/topbar.html');
	}


	public function prepare_data()
	{
		/** @var TopbarNotificationReader $topbarNotificationReader */
		$topbarNotificationReader = MainFactory::create_object('TopbarNotificationReader');

		/** @var TopbarNotification $topbarNotification */
		$topbarNotification = $topbarNotificationReader->getTopbarNotification();
		
		$this->set_content_data('topbarNotification', $topbarNotification);
	}
} 